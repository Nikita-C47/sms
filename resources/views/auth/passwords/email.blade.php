@extends('layouts.auth')

@section('title', 'Восстановить пароль')

@section('content')
    <div class="alert alert-info m-2" role="alert">
        <i class="fas fa-info-circle"></i> Чтобы восстановить пароль, укажите email в поле ниже. На него будет отправлена ссылка для сброса пароля.
    </div>
    <!-- TODO: Русификация уведомлений о паролях -->
    @if (session('status'))
        <div class="alert alert-success m-2" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">
                Email
            </label>
            <input id="email"
                   type="email"
                   class="form-control input-solid @error('email') is-invalid @enderror"
                   name="email"
                   value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                Отправить ссылку
            </button>
        </div>
    </form>
@endsection
