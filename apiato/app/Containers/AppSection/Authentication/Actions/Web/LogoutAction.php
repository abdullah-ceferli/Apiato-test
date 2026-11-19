<?php

namespace App\Containers\AppSection\Authentication\Actions\Web;

use App\Containers\AppSection\Authentication\Tasks\RevokeWebTokenTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

final class LogoutAction extends ParentAction
{
    public function __construct(private readonly RevokeWebTokenTask $revokeToken)
    {
    }

    public function run(Session $session): void
    {
        $this->revokeToken->run($session);
        Auth::guard('web')->logout();

        $session->invalidate();

        $session->regenerateToken();
    }
}
