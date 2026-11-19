@extends('appSection@taskManagement::layout')
@section('title', 'Daxil ol')
@section('shell-class', 'auth-shell')
@section('content')
<section class="panel auth-panel">
    <h1>Daxil ol</h1>
    <form action="{{ route('login') }}" method="post">
        @csrf
        <div class="field"><label for="email">E-poçt</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email">@error('email')<p class="field-error" role="alert">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="password">Şifrə</label><input id="password" name="password" type="password" required autocomplete="current-password">@error('password')<p class="field-error" role="alert">{{ $message }}</p>@enderror</div>
        <button class="button primary full-width" type="submit">Daxil ol →</button>
    </form>
    <p class="auth-switch">Hesabınız yoxdur? <a href="{{ route('register') }}">Qeydiyyatdan keçin</a></p>
</section>
@endsection
