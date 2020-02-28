@extends('layouts.app')

@section('title', 'Редактировать '.$entityName.' "' . $entity->name . '"')

@section('content')
    <form method="post">
        @csrf
        <div class="form-group px-0">
            <label for="name">Название: <span class="text-danger">*</span></label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control input-solid @error('name') is-invalid @enderror"
                   value="{{ $entity->name }}">
            @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <button type="submit" class="btn btn-success">
                Сохранить
            </button>
            <a href="{{ route($entitiesType) }}" class="btn btn-primary">
                К списку
            </a>
        </div>
    </form>
@endsection
