@extends('layouts.auth')

@section('title', 'Восстановить пароль')

@section('content')
    <!-- TODO: Русификация уведомлений о паролях -->
    <form method="POST" action="{{ route('password.email') }}">
        <h3 class="m-t-10 m-b-10">Сбросить пароль</h3>
        <p class="m-b-20">
            Чтобы восстановить пароль, укажите email в поле ниже. На него будет отправлена ссылка для сброса пароля.
        </p>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        @csrf
        <div class="form-group">
            <input id="email"
                   type="email"
                   class="form-control input-solid @error('email') is-invalid @enderror"
                   name="email"
                   placeholder="Email"
                   value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <button class="btn btn-info btn-block" type="submit">
                Отправить ссылку
            </button>
        </div>
    </form>
@endsection
