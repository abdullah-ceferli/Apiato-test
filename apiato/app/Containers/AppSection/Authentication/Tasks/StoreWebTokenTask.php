<?php

namespace App\Containers\AppSection\Authentication\Tasks;

use App\Containers\AppSection\Authentication\Data\DTOs\PasswordToken;
use App\Containers\AppSection\Authentication\Data\Factories\TokenAttributeFormatter;
use App\Ship\Parents\Tasks\Task;
use Illuminate\Contracts\Session\Session;

final class StoreWebTokenTask extends Task
{
    public function __construct(private readonly TokenAttributeFormatter $formatter)
    {
    }

    public function run(Session $session, PasswordToken $token): void
    {
        $attributes = $this->formatter->format($token->accessToken);
        $session->put('passport', [
            'access_token' => $token->accessToken,
            'refresh_token' => $token->refreshToken->value(),
            'token_id' => $attributes['oauth_access_token_id'],
            'expires_at' => time() + $token->expiresIn,
        ]);
    }
}
