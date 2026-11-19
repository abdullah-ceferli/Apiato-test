<?php

namespace App\Containers\AppSection\TaskManagement\UI\WEB\Middleware;

use App\Containers\AppSection\Authentication\Actions\Web\LogoutAction;
use Illuminate\Http\Request;

final class RequireSessionToken
{
    public function handle(Request $request, \Closure $next): mixed
    {
        // The debug toolbar can serialize session contents into the page.
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        if (!$request->session()->has('passport.access_token')) {
            app(LogoutAction::class)->run($request->session());

            return redirect()->guest(route('login.form'));
        }

        return $next($request)->header('Cache-Control', 'no-store, private');
    }
}
