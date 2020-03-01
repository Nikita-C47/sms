@extends('layouts.app')

@section('title', 'Редактировать пользователя "'.$user->name.'"')

@section('content')
    <form method="post">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <div class="form-group px-0">
            <label for="name">ФИО: <span class="text-danger">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ $user->name }}">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <label for="email">Email: <span class="text-danger">*</span></label>
            <input type="text"
                   name="email"
                   id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ $user->email }}">
            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <label for="access_level">Доступ: <span class="text-danger">*</span></label>
            <select id="access_level"
                    name="access_level"
                    aria-describedby="rolesHelp"
                    class="form-control @error('access_level') is-invalid @enderror">
                <option value="">- Укажите уровень доступа -</option>
                @foreach($roles as $code => $name)
                    <option value="{{ $code }}" @if($code == $user->access_level) selected @endif>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            @error('access_level')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <small id="rolesHelp" class="form-text text-muted">
                <a tabindex="0"
                   data-placement="right"
                   data-toggle="popover"
                   data-trigger="focus"
                   title="Подробнее о ролях"
                   data-html="true"
                   data-content="@include('partial.roles-info')">Подробнее о ролях</a>
            </small>
        </div>
        <div class="form-group px-0">
            <label for="department_id">Подразделение:</label>
            <select id="department_id"
                    name="department_id"
                    class="form-control @error('department_id') is-invalid @enderror">
                <option value="">- Укажите подразделение -</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @if($department->id == $user->department_id) selected @endif>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0 pb-0">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="reset_password" name="reset_password" value="1">
                <label class="custom-control-label" for="reset_password">Сбросить пароль</label>
            </div>
        </div>
        <div class="form-group px-0">
            <button type="submit" class="btn btn-success">
                Сохранить
            </button>
            <a href="{{ route('users') }}" class="btn btn-primary">
                К списку
            </a>
        </div>
    </form>
@endsection
