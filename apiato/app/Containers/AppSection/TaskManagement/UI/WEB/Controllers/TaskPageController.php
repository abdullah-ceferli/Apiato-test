<?php

namespace App\Containers\AppSection\TaskManagement\UI\WEB\Controllers;

use App\Ship\Parents\Controllers\WebController;
use Illuminate\Contracts\View\View;

final class TaskPageController extends WebController
{
    public function index(): View
    {
        return view('appSection@taskManagement::index');
    }

    public function create(): View
    {
        return view('appSection@taskManagement::form', ['taskId' => null]);
    }

    public function edit(string $id): View
    {
        // The browser fetches this task from the owner-scoped API after loading.
        return view('appSection@taskManagement::form', ['taskId' => $id]);
    }
}
