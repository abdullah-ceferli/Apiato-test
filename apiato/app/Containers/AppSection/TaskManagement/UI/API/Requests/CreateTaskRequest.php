<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Requests;

final class CreateTaskRequest extends TaskRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_completed' => ['sometimes', 'boolean'],
        ];
    }
}
