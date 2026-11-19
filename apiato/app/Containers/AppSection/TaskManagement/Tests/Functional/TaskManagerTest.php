<?php

namespace App\Containers\AppSection\TaskManagement\Tests\Functional;

use App\Containers\AppSection\Authentication\Data\Factories\ClientFactory;
use App\Containers\AppSection\TaskManagement\Data\Seeders\TaskManagementSeeder_100;
use App\Containers\AppSection\TaskManagement\Models\Task;
use App\Containers\AppSection\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Tests\TestCase;

final class TaskManagerTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = false;

    protected function setUp(): void
    {
        parent::setUp();
        ClientFactory::webClient();
    }

    private function user(): User
    {
        return User::factory()->createOne(['password' => 'TaskManager123!']);
    }

    private function token(User $user): array
    {
        Auth::forgetGuards();
        return $this->postJson(route('passport.token'), [
            'grant_type' => 'password',
            'client_id' => config('appSection-authentication.clients.web.id'),
            'client_secret' => config('appSection-authentication.clients.web.secret'),
            'username' => $user->email,
            'password' => 'TaskManager123!',
        ])->assertOk()->json();
    }

    private function api(string $method, string $path = '', array $data = [], ?string $token = null): \Illuminate\Testing\TestResponse
    {
        Auth::forgetGuards();
        return $this->json($method, route('api.tasks.index') . $path, $data,
            $token ? ['Authorization' => 'Bearer ' . $token] : []);
    }

    public function testGuestWebPagesRedirectToLogin(): void
    {
        foreach (['/tasks', '/tasks/create', '/tasks/1/edit'] as $path) {
            $this->get($path)->assertRedirect(route('login.form'));
        }
        $this->get('/login')->assertOk()->assertSee('name="_token"', false);
        $this->get('/register')->assertOk()->assertSee('name="_token"', false);
    }

    public function testEveryCrudRouteRequiresAnApiToken(): void
    {
        foreach ([['GET', ''], ['GET', '/1'], ['POST', ''], ['PATCH', '/1'], ['DELETE', '/1']] as [$method, $path]) {
            $this->api($method, $path)->assertUnauthorized();
        }
    }

    public function testWebSessionAloneDoesNotAuthenticateApi(): void
    {
        $this->actingAs($this->user(), 'web');
        $this->getJson(route('api.tasks.index'))->assertUnauthorized();
    }

    public function testRegistrationCreatesUserWithHashedPassword(): void
    {
        $this->post('/register', [
            'name' => 'Yeni istifadəçi', 'email' => 'NEW@example.com',
            'password' => 'TaskManager123!', 'password_confirmation' => 'TaskManager123!',
        ])->assertRedirect(route('login.form'));
        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('TaskManager123!', $user->password));
        $this->assertGuest('web');
    }

    public function testInvalidAndDuplicateRegistrationAreRejected(): void
    {
        $user = $this->user();
        $this->post('/register', ['name' => '', 'email' => $user->email, 'password' => 'short'])
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function testLoginCreatesSessionAndPasswordGrantTokensWithoutExposingSecrets(): void
    {
        $user = $this->user();
        $login = $this->post('/login', ['email' => $user->email, 'password' => 'TaskManager123!']);
        $login->assertRedirect(route('tasks.index'))
            ->assertSessionHas('passport.access_token')->assertSessionHas('passport.refresh_token');
        $this->assertAuthenticatedAs($user, 'web');
        $access = session('passport.access_token');
        $refresh = session('passport.refresh_token');
        $secret = config('appSection-authentication.clients.web.secret');
        foreach (['/tasks', '/tasks/create', '/tasks/1/edit'] as $path) {
            $this->get($path)->assertOk()->assertSee($access, false)
                ->assertDontSee($refresh, false)->assertDontSee($secret, false);
        }
        $this->api('GET', token: $access)->assertOk();
    }

    public function testInvalidLoginDoesNotCreateTokens(): void
    {
        $user = $this->user();
        $this->post('/login', ['email' => $user->email, 'password' => 'incorrect'])
            ->assertSessionHasErrors('email')->assertSessionMissing('passport');
        $this->assertGuest('web');
    }

    public function testCreateAssignsAuthenticatedOwnerAndDefaults(): void
    {
        $owner = $this->user();
        $other = $this->user();
        $token = $this->token($owner)['access_token'];
        $created = $this->api('POST', data: ['title' => 'Yeni tapşırıq', 'user_id' => $other->id], token: $token)
            ->assertCreated()->assertJsonPath('data.user_id', $owner->id)
            ->assertJsonPath('data.is_completed', false)->assertJsonPath('data.description', null);
        $this->assertDatabaseHas('tasks', ['id' => $created->json('data.id'), 'user_id' => $owner->id]);
    }

    public function testListAndCompletionFilterAreOwnerScoped(): void
    {
        $owner = $this->user();
        $owner->tasks()->create(['title' => 'Gözləyən']);
        $completed = $owner->tasks()->create(['title' => 'Bitmiş', 'is_completed' => true]);
        $this->user()->tasks()->create(['title' => 'Başqasına aid']);
        $token = $this->token($owner)['access_token'];
        $this->api('GET', token: $token)->assertOk()->assertJsonCount(2, 'data');
        $this->api('GET', '?is_completed=1', token: $token)->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $completed->id);
        $this->api('GET', '?is_completed=0', token: $token)->assertOk()
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.is_completed', false);
    }

    public function testInvalidPayloadsReturn422(): void
    {
        $owner = $this->user();
        $task = $owner->tasks()->create(['title' => 'Etibarlı']);
        $token = $this->token($owner)['access_token'];
        foreach ([[], ['title' => ''], ['title' => str_repeat('ə', 151)], ['title' => 'OK', 'is_completed' => 'wrong'], ['title' => 'OK', 'description' => []]] as $data) {
            $this->api('POST', data: $data, token: $token)->assertUnprocessable();
        }
        $this->api('PATCH', '/' . $task->id, ['title' => null], $token)->assertUnprocessable();
        $this->api('GET', '?is_completed=wrong', token: $token)->assertUnprocessable();
    }

    public function testReadUpdateToggleAndDelete(): void
    {
        $owner = $this->user();
        $task = $owner->tasks()->create(['title' => 'İlkin', 'description' => 'Qeyd']);
        $token = $this->token($owner)['access_token'];
        $path = '/' . $task->id;
        $this->api('GET', $path, token: $token)->assertOk()->assertJsonPath('data.title', 'İlkin');
        $this->api('PATCH', $path, ['title' => 'Yeni', 'description' => null, 'user_id' => $this->user()->id], $token)
            ->assertOk()->assertJsonPath('data.title', 'Yeni')->assertJsonPath('data.description', null)
            ->assertJsonPath('data.user_id', $owner->id);
        $this->api('PATCH', $path, ['is_completed' => true], $token)->assertOk()->assertJsonPath('data.is_completed', true);
        $this->api('DELETE', $path, token: $token)->assertNoContent();
        $this->api('GET', $path, token: $token)->assertNotFound();
    }

    public function testAnotherUsersTaskReturns404ForReadUpdateAndDelete(): void
    {
        $owner = $this->user();
        $task = $this->user()->tasks()->create(['title' => 'Şəxsi']);
        $token = $this->token($owner)['access_token'];
        foreach (['GET', 'PATCH', 'DELETE'] as $method) {
            $this->api($method, '/' . $task->id, ['title' => 'Dəyiş'], $token)->assertNotFound();
        }
        $this->assertSame('Şəxsi', $task->fresh()->title);
    }

    public function testPasswordGrantRefreshRotatesTokens(): void
    {
        $tokens = $this->token($this->user());
        $refreshed = $this->postJson(route('passport.token'), [
            'grant_type' => 'refresh_token',
            'client_id' => config('appSection-authentication.clients.web.id'),
            'client_secret' => config('appSection-authentication.clients.web.secret'),
            'refresh_token' => $tokens['refresh_token'],
        ])->assertOk()->json();
        $this->assertNotSame($tokens['access_token'], $refreshed['access_token']);
        $this->api('GET', token: $refreshed['access_token'])->assertOk();
        $this->postJson(route('passport.token'), [
            'grant_type' => 'refresh_token',
            'client_id' => config('appSection-authentication.clients.web.id'),
            'client_secret' => config('appSection-authentication.clients.web.secret'),
            'refresh_token' => $tokens['refresh_token'],
        ])->assertBadRequest();
    }

    public function testWebLogoutRevokesAccessAndRefreshAndEndsSession(): void
    {
        $user = $this->user();
        $this->post('/login', ['email' => $user->email, 'password' => 'TaskManager123!'])->assertRedirect();
        $access = session('passport.access_token');
        $refresh = session('passport.refresh_token');
        $id = session('passport.token_id');
        $this->post('/logout')->assertRedirect(route('login.form'))->assertSessionMissing('passport');
        $this->assertGuest('web');
        $this->assertTrue((bool) Passport::token()->newQuery()->findOrFail($id)->revoked);
        $this->assertSame(0, Passport::refreshToken()->newQuery()->where('access_token_id', $id)->where('revoked', false)->count());
        $this->api('GET', token: $access)->assertUnauthorized();
        $this->postJson(route('passport.token'), [
            'grant_type' => 'refresh_token',
            'client_id' => config('appSection-authentication.clients.web.id'),
            'client_secret' => config('appSection-authentication.clients.web.secret'),
            'refresh_token' => $refresh,
        ])->assertBadRequest();
        $this->get('/tasks')->assertRedirect(route('login.form'));
    }

    public function testWebFormsRejectMissingCsrfToken(): void
    {
        // Laravel bypasses CSRF in the testing environment; exercise the real middleware.
        $this->app->instance('env', 'local');
        $this->post('/register', [])->assertStatus(419);
        $this->post('/login', [])->assertStatus(419);
        $this->actingAs($this->user(), 'web')->post('/logout')->assertStatus(419);
    }

    public function testSeederCreatesTwoUsersWithOwnTasksAndIsRepeatable(): void
    {
        $this->seed(TaskManagementSeeder_100::class);
        $usersAfterFirstSeed = User::count();
        $tasksAfterFirstSeed = Task::count();
        $this->seed(TaskManagementSeeder_100::class);
        $this->assertSame($usersAfterFirstSeed, User::count());
        $this->assertSame($tasksAfterFirstSeed, Task::count());
        $this->assertSame(2, User::whereIn('email', ['aysel@example.com', 'murad@example.com'])->count());
        foreach (User::whereIn('email', ['aysel@example.com', 'murad@example.com'])->get() as $user) {
            $this->assertSame(2, $user->tasks()->count());
        }
    }

    public function testCorsAllowsWebOriginAndBearerHeader(): void
    {
        $this->options(route('api.tasks.index'), [], [
            'Origin' => config('apiato.apps.web.url'),
            'Access-Control-Request-Method' => 'PATCH',
            'Access-Control-Request-Headers' => 'authorization,content-type',
        ])->assertNoContent()->assertHeader('Access-Control-Allow-Origin', config('apiato.apps.web.url'));
    }
}
