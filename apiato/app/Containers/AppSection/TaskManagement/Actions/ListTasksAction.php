<?php

namespace App\Containers\AppSection\TaskManagement\Actions;

use App\Containers\AppSection\TaskManagement\Tasks\ListTasksTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action;
use Illuminate\Database\Eloquent\Collection;

final class ListTasksAction extends Action
{
    public function __construct(private readonly ListTasksTask $listTasks)
    {
    }

    public function run(User $user, array $filters): Collection
    {
        return $this->listTasks->run($user, $filters);
    }
}
