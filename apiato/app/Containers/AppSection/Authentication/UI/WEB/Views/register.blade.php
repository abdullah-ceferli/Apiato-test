@extends('appSection@taskManagement::layout')
@section('title', 'Qeydiyyat')
@section('shell-class', 'auth-shell')
@section('content')
<section class="panel auth-panel">
    <h1>Qeydiyyat</h1>
    <form action="{{ route('register.store') }}" method="post">
        @csrf
        <div class="field"><label for="name">Adınız</label><input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="100" autocomplete="name" autofocus>@error('name')<p class="field-error" role="alert">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="email">E-poçt</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">@error('email')<p class="field-error" role="alert">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="password">Şifrə (ən azı 8 simvol)</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">@error('password')<p class="field-error" role="alert">{{ $message }}</p>@enderror</div>
        <div class="field"><label for="password_confirmation">Şifrəni təkrarlayın</label><input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"></div>
        <button class="button primary" type="submit">Qeydiyyatdan keç</button>
    </form>
    <p><a href="{{ route('login.form') }}">Daxil ol</a></p>
</section>
@endsection
