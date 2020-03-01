@extends('layouts.auth')

@section('title', 'Войти')

@section('content')
    <form method="post" action="{{ route('login') }}" class="pt-3">
        <h2 class="login-title">Войти</h2>
        @csrf
        <div class="form-group">
            <input type="email"
                   class="form-control input-solid @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   required
                   autocomplete="email"
                   autofocus
                   id="email"
                   name="email"
                   placeholder="Введите email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <input type="password"
                   class="form-control input-solid @error('password') is-invalid @enderror"
                   id="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="Введите пароль">
        </div>
        <div class="form-group d-flex justify-content-between">
            <label class="ui-checkbox ui-checkbox-info">
                <input type="checkbox"
                       id="remember"
                       name="remember"
                       value="1" {{ old('remember') ? 'checked' : '' }}>
                <span class="input-span"></span>
                Запомнить меня
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    Забыли пароль?
                </a>
            @endif
        </div>
        <div class="form-group">
            <button class="btn btn-info btn-block" type="submit">Войти</button>
        </div>
        <div class="form-group text-center">
            <a href="{{ route('create-anonymous-event') }}">
                Добавить событие анонимно
            </a>
        </div>
        <!--
        <div class="text-center mt-4 font-weight-light">
            Don't have an account? <a href="register.html" class="text-primary">Create</a>
        </div>
        -->
    </form>
@endsection
