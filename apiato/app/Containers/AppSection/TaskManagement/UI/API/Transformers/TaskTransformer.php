<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Transformers;

use App\Containers\AppSection\TaskManagement\Models\Task;
use App\Ship\Parents\Transformers\Transformer;

final class TaskTransformer extends Transformer
{
    public function transform(Task $task): array
    {
        return [
            'id' => $task->id,
            'user_id' => $task->user_id,
            'title' => $task->title,
            'description' => $task->description,
            'is_completed' => $task->is_completed,
            'created_at' => $task->created_at?->toIso8601String(),
            'updated_at' => $task->updated_at?->toIso8601String(),
        ];
    }
}
