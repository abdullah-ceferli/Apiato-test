<?php

namespace App\Containers\AppSection\Authentication\Actions\Web;

use App\Containers\AppSection\Authentication\Actions\Api\WebClient\IssueTokenAction;
use App\Containers\AppSection\Authentication\Tasks\StoreWebTokenTask;
use App\Containers\AppSection\Authentication\Values\UserCredential;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class LoginAction extends ParentAction
{
    public function __construct(
        private readonly IssueTokenAction $issueToken,
        private readonly StoreWebTokenTask $storeToken,
    ) {
    }

    public function run(string $email, string $password, bool $remember): RedirectResponse
    {
        $credentials = [
            'email' => static fn (Builder $query): Builder => $query
                ->orWhereRaw('lower(email) = lower(?)', [$email]),
            'password' => $password,
        ];

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            try {
                $token = $this->issueToken->run(UserCredential::create($email, $password));
                $this->storeToken->run(session()->driver(), $token);
            } catch (\Throwable $exception) {
                Auth::guard('web')->logout();
                session()->invalidate();
                session()->regenerateToken();

                throw $exception;
            }

            session()->regenerate();

            return redirect()->intended(route('tasks.index'));
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }
}
