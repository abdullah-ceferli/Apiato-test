<?php

namespace App\Containers\AppSection\Authentication\Actions\Api;

use App\Containers\AppSection\Authentication\Values\RefreshToken;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\Cookie as CookieFacade;
use Symfony\Component\HttpFoundation\Cookie;

final class RevokeTokenAction extends ParentAction
{
    public function run(User|null $user): Cookie
    {
        $token = $user?->token();
        if ($token) {
            $token->revoke();
            \Laravel\Passport\Passport::refreshToken()->newQuery()
                ->where('access_token_id', $token->oauth_access_token_id)
                ->update(['revoked' => true]);
        }

        return CookieFacade::forget(RefreshToken::cookieName());
    }
}
