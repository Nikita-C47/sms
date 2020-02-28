@extends('layouts.auth')

@section('title', 'Войти')

@section('content')
    <div class="text-center">
        <h4>Для продолжения необходимо авторизоваться</h4>
    </div>
    <form method="post" action="{{ route('login') }}" class="pt-3">
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
        <div class="form-group row">
            <div class="col text-left">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"
                           class="custom-control-input"
                           id="remember"
                           name="remember"
                           value="1" {{ old('remember') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="remember">Запомнить меня</label>
                </div>
            </div>
            <div class="col text-right">
                @if (Route::has('password.request'))
                    <a class="auth-link text-black" href="{{ route('password.request') }}">
                        Забыли пароль?
                    </a>
                @endif
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                Войти
            </button>
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
