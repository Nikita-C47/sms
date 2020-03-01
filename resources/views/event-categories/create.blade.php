@extends('layouts.app')

@section('title', 'Добавить категорию событий')

@section('content')
    <form method="post">
        @csrf
        <div class="form-group px-0">
            <label for="department_id">Подразделение: <span class="text-danger">*</span></label>
            <select id="department_id"
                    name="department_id"
                    class="form-control @error('department_id') is-invalid @enderror">
                <option value="">- Укажите подразделение -</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @if($department->id == old('department_id')) selected @endif>
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
        <div class="form-group px-0">
            <label for="code">Код: <span class="text-danger">*</span></label>
            <input type="text"
                   name="code"
                   id="code"
                   class="form-control @error('code') is-invalid @enderror"
                   aria-describedby="codeHelp"
                   value="{{ old('code') }}">
            @error('code')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            <small id="codeHelp" class="form-text text-muted">
                Кодовое обозначение категории (например Лётный комплекс использует категории вроде ЛК1, ЛК2).
            </small>
        </div>
        <div class="form-group px-0">
            <label for="name">Название: <span class="text-danger">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <button type="submit" class="btn btn-success">
                Добавить
            </button>
            <a href="{{ route('event-categories') }}" class="btn btn-primary">
                К списку
            </a>
        </div>
    </form>
@endsection
