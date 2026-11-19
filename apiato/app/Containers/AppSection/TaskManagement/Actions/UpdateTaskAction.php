<?php

namespace App\Containers\AppSection\TaskManagement\Actions;

use App\Containers\AppSection\TaskManagement\Models\Task;
use App\Containers\AppSection\TaskManagement\Tasks\FindTaskTask;
use App\Containers\AppSection\TaskManagement\Tasks\UpdateTaskTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action;

final class UpdateTaskAction extends Action
{
    public function __construct(private readonly FindTaskTask $findTask, private readonly UpdateTaskTask $updateTask)
    {
    }

    public function run(User $user, int $id, array $data): Task
    {
        return $this->updateTask->run($this->findTask->run($user, $id), $data);
    }
}
