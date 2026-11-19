<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Controllers;

use Apiato\Support\Facades\Response;
use App\Containers\AppSection\TaskManagement\Actions\FindTaskAction;
use App\Containers\AppSection\TaskManagement\UI\API\Requests\TaskRequest;
use App\Containers\AppSection\TaskManagement\UI\API\Transformers\TaskTransformer;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class FindTaskController extends ApiController
{
    public function __invoke(TaskRequest $request, FindTaskAction $action): JsonResponse
    {
        $result = $action->run($request->user('api'), (int) $request->route('id'));

        return Response::create($result, TaskTransformer::class)->ok();
    }
}
