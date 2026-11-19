<?php

namespace App\Containers\AppSection\TaskManagement\Tasks;

use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task;
use Illuminate\Database\Eloquent\Collection;

final class ListTasksTask extends Task
{
    public function run(User $user, array $filters): Collection
    {
        $query = $user->tasks()->latest('id');
        if (isset($filters['is_completed'])) {
            $query->where('is_completed', (bool) $filters['is_completed']);
        }
        return $query->get();
    }
}
