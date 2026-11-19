<?php

namespace App\Containers\AppSection\TaskManagement\Actions;

use App\Containers\AppSection\TaskManagement\Models\Task;
use App\Containers\AppSection\TaskManagement\Tasks\CreateTaskTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action;

final class CreateTaskAction extends Action
{
    public function __construct(private readonly CreateTaskTask $createTask)
    {
    }

    public function run(User $user, array $data): Task
    {
        return $this->createTask->run($user, $data);
    }
}
