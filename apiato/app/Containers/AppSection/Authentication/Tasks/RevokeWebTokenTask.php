<?php

namespace App\Containers\AppSection\Authentication\Tasks;

use App\Ship\Parents\Tasks\Task;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;

final class RevokeWebTokenTask extends Task
{
    public function run(Session $session): void
    {
        $id = $session->get('passport.token_id');
        if (!$id) {
            return;
        }

        // The database ID remains available even after the JWT has expired.
        DB::transaction(static function () use ($id): void {
            Passport::token()->newQuery()->whereKey($id)->update(['revoked' => true]);
            Passport::refreshToken()->newQuery()->where('access_token_id', $id)->update(['revoked' => true]);
        });
        $session->forget('passport');
    }
}
