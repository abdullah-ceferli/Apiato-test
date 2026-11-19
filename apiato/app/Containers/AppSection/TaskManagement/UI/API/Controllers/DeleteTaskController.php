<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Controllers;

use App\Containers\AppSection\TaskManagement\Actions\DeleteTaskAction;
use App\Containers\AppSection\TaskManagement\UI\API\Requests\TaskRequest;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\Response;

final class DeleteTaskController extends ApiController
{
    public function __invoke(TaskRequest $request, DeleteTaskAction $action): Response
    {
        $action->run($request->user('api'), (int) $request->route('id'));

        return response()->noContent();
    }
}
