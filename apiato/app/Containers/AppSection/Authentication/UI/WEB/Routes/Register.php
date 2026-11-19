<?php

use App\Containers\AppSection\Authentication\UI\WEB\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:web')->group(static function (): void {
    Route::get('register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('register', RegisterController::class)->name('register.store');
});
