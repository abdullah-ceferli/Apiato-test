<?php

namespace App\Containers\AppSection\TaskManagement\UI\API\Requests;

use App\Ship\Parents\Requests\Request;

class TaskRequest extends Request
{
    // TaskManagement deliberately exposes numeric IDs, independently of HASH_ID.
    protected array $decode = [];

    public function rules(): array
    {
        return [];
    }
}
