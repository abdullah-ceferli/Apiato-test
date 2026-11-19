<?php

use App\Containers\AppSection\TaskManagement\UI\WEB\Controllers\TaskPageController;
use App\Containers\AppSection\TaskManagement\UI\WEB\Middleware\RequireSessionToken;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', RequireSessionToken::class])->group(static function (): void {
    Route::get('tasks', [TaskPageController::class, 'index'])->name('tasks.index');
    Route::get('tasks/create', [TaskPageController::class, 'create'])->name('tasks.create');
    Route::get('tasks/{id}/edit', [TaskPageController::class, 'edit'])->whereNumber('id')->name('tasks.edit');
});
