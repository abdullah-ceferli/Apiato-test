<?php

namespace App\Containers\AppSection\TaskManagement\Actions;

use App\Containers\AppSection\TaskManagement\Tasks\DeleteTaskTask;
use App\Containers\AppSection\TaskManagement\Tasks\FindTaskTask;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action;

final class DeleteTaskAction extends Action
{
    public function __construct(private readonly FindTaskTask $findTask, private readonly DeleteTaskTask $deleteTask)
    {
    }

    public function run(User $user, int $id): void
    {
        $this->deleteTask->run($this->findTask->run($user, $id));
    }
}
