@extends('layouts.auth')

@section('title', 'Сбросить пароль')

@section('content')
    <div class="alert alert-info m-2" role="alert">
        Для продолжения укажите новый пароль в форме ниже.
    </div>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email"
                   type="email"
                   class="form-control input-solid @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Новый пароль</label>
            <input id="password"
                   type="password"
                   class="form-control input-solid @error('password') is-invalid @enderror"
                   name="password" required autocomplete="new-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Подтверждение нового пароля</label>
            <input id="password-confirm"
                   type="password"
                   class="form-control input-solid"
                   name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                Сбросить пароль
            </button>
        </div>
    </form>
@endsection
