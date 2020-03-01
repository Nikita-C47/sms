@extends('layouts.auth')

@section('title', 'Сбросить пароль')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        <h3 class="m-t-10 m-b-10">Обновить пароль</h3>
        <p class="m-b-20">
            Для продолжения укажите новый пароль в форме ниже.
        </p>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group">
            <input id="email"
                   type="email"
                   class="form-control input-solid @error('email') is-invalid @enderror"
                   name="email"
                   placeholder="Email"
                   value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <input id="password"
                   type="password"
                   placeholder="Новый пароль"
                   class="form-control input-solid @error('password') is-invalid @enderror"
                   name="password" required autocomplete="new-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
            @enderror
        </div>

        <div class="form-group">
            <input id="password-confirm"
                   type="password"
                   placeholder="Подтверждение нового пароля"
                   class="form-control input-solid"
                   name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-info btn-block">
                Сбросить пароль
            </button>
        </div>
    </form>
@endsection
