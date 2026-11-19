<?php

namespace App\Containers\AppSection\Authentication\UI\WEB\Controllers;

use App\Containers\AppSection\Authentication\Actions\RegisterUserAction;
use App\Containers\AppSection\Authentication\UI\WEB\Requests\RegisterRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class RegisterController extends WebController
{
    public function showForm(): View
    {
        return view('appSection@authentication::register');
    }

    public function __invoke(RegisterRequest $request, RegisterUserAction $action): RedirectResponse
    {
        $action->run($request->validated());

        return redirect()->route('login.form')->with('status', 'Hesab yaradıldı. İndi daxil ola bilərsiniz.');
    }
}
