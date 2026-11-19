<?php

use App\Containers\AppSection\TaskManagement\UI\API\Controllers\FindTaskController;
use Illuminate\Support\Facades\Route;

Route::get('tasks/{id}', FindTaskController::class)
    ->middleware('auth:api')->whereNumber('id')
    ->name('api.tasks.show');
