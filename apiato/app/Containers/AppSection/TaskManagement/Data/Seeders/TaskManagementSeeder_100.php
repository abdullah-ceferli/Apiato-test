<?php

namespace App\Containers\AppSection\TaskManagement\Data\Seeders;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Seeders\Seeder;

final class TaskManagementSeeder_100 extends Seeder
{
    public function run(): void
    {
        foreach ([['Aysel', 'aysel@example.com'], ['Murad', 'murad@example.com']] as [$name, $email]) {
            $user = User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => 'TaskManager123!']);
            $user->tasks()->firstOrCreate(['title' => 'Apiato tapşırığını hazırla'], ['description' => 'Route, Request, Controller, Action və Task axınını yoxla.']);
            $user->tasks()->firstOrCreate(['title' => 'Passport mövzusunu oxu'], ['is_completed' => true]);
        }
    }
}
