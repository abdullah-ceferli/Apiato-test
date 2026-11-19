<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Requests;

final class ListTasksRequest extends TaskRequest
{
    public function rules(): array
    {
        return [
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }
}
