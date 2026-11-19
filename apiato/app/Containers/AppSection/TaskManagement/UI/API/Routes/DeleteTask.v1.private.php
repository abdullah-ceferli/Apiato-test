<?php

use App\Containers\AppSection\TaskManagement\UI\API\Controllers\DeleteTaskController;
use Illuminate\Support\Facades\Route;

Route::delete('tasks/{id}', DeleteTaskController::class)
    ->middleware('auth:api')->whereNumber('id')
    ->name('api.tasks.destroy');
