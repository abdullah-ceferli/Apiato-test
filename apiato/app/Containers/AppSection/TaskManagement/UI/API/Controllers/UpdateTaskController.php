<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\AppSection\TaskManagement\Actions\UpdateTaskAction;
use App\Containers\AppSection\TaskManagement\UI\API\Requests\UpdateTaskRequest;
use App\Containers\AppSection\TaskManagement\UI\API\Transformers\TaskTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class UpdateTaskController extends ApiController
{
    public function __invoke(UpdateTaskRequest $request, UpdateTaskAction $action): JsonResponse
    {
        $result = $action->run($request->user('api'), (int) $request->route('id'), $request->validated());

        return Response::create($result, TaskTransformer::class)->ok();
    }
}
