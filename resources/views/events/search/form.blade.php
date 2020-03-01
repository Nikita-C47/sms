@extends('layouts.app')

@section('title', 'Поиск события по номеру')

@section('content')
    <form method="post">
        @csrf
        <div class="form-group">
            <label class="font-weight-bold" for="query">
                Номер события: <span class="text-danger">*</span>
            </label>
            <input class="form-control @error('query') is-invalid @enderror"
                   placeholder="Укажите номер события"
                   value="{{ old('query') }}"
                   id="query"
                   name="query">
            @error('query')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <button class="btn btn-primary">
                Искать событие
            </button>
        </div>
    </form>
@endsection
