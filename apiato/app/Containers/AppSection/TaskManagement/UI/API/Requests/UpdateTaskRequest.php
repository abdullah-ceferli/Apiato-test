<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Requests;

final class UpdateTaskRequest extends TaskRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }
}
