<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\AppSection\TaskManagement\Actions\CreateTaskAction;
use App\Containers\AppSection\TaskManagement\UI\API\Requests\CreateTaskRequest;
use App\Containers\AppSection\TaskManagement\UI\API\Transformers\TaskTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class CreateTaskController extends ApiController
{
    public function __invoke(CreateTaskRequest $request, CreateTaskAction $action): JsonResponse
    {
        $result = $action->run($request->user('api'), $request->validated());

        return Response::create($result, TaskTransformer::class)->created();
    }
}
