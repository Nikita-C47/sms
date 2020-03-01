@extends('layouts.app')

@section('title', 'Добавить событие анонимно')

@section('content')
    <form id="anonymousEventForm" method="post">
        @csrf
        <div class="form-group px-0">
            <label for="date" class="font-weight-bold">
                Дата события: <span class="text-danger">*</span>
            </label>
            <input type="text"
                   readonly
                   name="date"
                   id="date"
                   class="form-control @error('date') is-invalid @enderror"
                   value="{{ old('date') }}">
            <event-datepicker v-bind:field="'date'"></event-datepicker>
            @error('date')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <label for="airport" class="font-weight-bold">Где произошло:</label>
            <input type="text"
                   name="airport"
                   id="airport"
                   class="form-control @error('airport') is-invalid @enderror"
                   value="{{ old('airport') }}">
            @error('airport')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <label for="initiator" class="font-weight-bold">От кого сообщение:</label>
            <input type="text"
                   name="initiator"
                   id="initiator"
                   class="form-control @error('initiator') is-invalid @enderror"
                   value="{{ old('initiator') }}">
            @error('initiator')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <label for="message" class="font-weight-bold">
                Сообщение: <span class="text-danger">*</span>
            </label>
            <textarea rows="6"
                      class="form-control"
                      id="message"
                      name="message"
                      placeholder="Опишите случившееся событие"></textarea>
            @error('message')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <label for="commentary" class="font-weight-bold">
                Комментарий:
            </label>
            <textarea rows="6"
                      class="form-control"
                      id="commentary"
                      name="commentary"
                      placeholder="Опишите случившееся событие"></textarea>
            @error('commentary')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group px-0">
            <button type="submit" class="btn btn-success">
                Добавить событие
            </button>
        </div>
    </form>
@endsection
