<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tapşırıqlarım') · Task Manager</title>
    <link rel="stylesheet" href="{{ asset('task-manager.css') }}">
    @auth('web')
        <script>
            window.taskManager = {{ Illuminate\Support\Js::from([
                'accessToken' => session('passport.access_token'),
                'apiUrl' => route('api.tasks.index'),
                'loginUrl' => route('login.form'),
                'tasksUrl' => route('tasks.index'),
            ]) }};
        </script>
        <script src="{{ asset('task-manager.js') }}" defer></script>
    @endauth
</head>
<body>
    <header class="site-header">
        <a class="brand" href="{{ route('tasks.index') }}"><span class="brand-icon" aria-hidden="true">✓</span> Task Manager</a>
        @auth('web')
            <div class="account"><span>{{ auth('web')->user()->name }}</span><form method="post" action="{{ route('logout') }}">@csrf<button class="button ghost" type="submit">Çıxış</button></form></div>
        @else
        @endauth
    </header>
    <main class="shell @yield('shell-class')">
        @if(session('status'))<p class="notice success" role="status">{{ session('status') }}</p>@endif
        @yield('content')
    </main>
</body>
</html>
