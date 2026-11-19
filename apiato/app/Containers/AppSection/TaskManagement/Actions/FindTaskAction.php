<?php

namespace App\Containers\AppSection\TaskManagement\Actions;

use App\Containers\AppSection\TaskManagement\Models\Task;
use App\Containers\AppSection\TaskManagement\Tasks\FindTaskTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action;

final class FindTaskAction extends Action
{
    public function __construct(private readonly FindTaskTask $findTask)
    {
    }

    public function run(User $user, int $id): Task
    {
        return $this->findTask->run($user, $id);
    }
}
