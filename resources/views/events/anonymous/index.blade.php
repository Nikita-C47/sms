@extends('layouts.app')

@section('title', 'Новые анонимные события')

@section('content')
    @if(count($events) === 0)
        <div class="alert alert-info" role="alert">
            На данный момент в базе нет анонимных событий, которые нужно обработать.
        </div>
    @else
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>Номер</th>
                <th>Дата</th>
                <th>От кого сообщение</th>
                <th>Где произошло</th>
                <th>Сообщение</th>
                <th>Комментарий</th>
                <th>Обработать</th>
            </tr>
            </thead>
            <tbody>
            @foreach($events as $event)
                <tr>
                    <td>
                        <a href="{{ route('view-event', ['id' => $event->id]) }}" class="font-weight-bold">
                            {{ $event->id }}
                        </a>
                    </td>
                    <td>{{ $event->date->format('d.m.Y') }}</td>
                    <td>
                        @include('widgets.filled-or-none', ['value' => $event->initiator ])
                    </td>
                    <td>
                        @include('widgets.filled-or-none', ['value' => $event->airport ])
                    </td>
                    <td>{{ $event->message }}</td>
                    <td>
                        @include('widgets.filled-or-none', ['value' => $event->commentary ])
                    </td>
                    <td>
                        <form class="form-inline" method="post" action="{{ route('event-process', ['id' => $event->id]) }}">
                            @csrf
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio"
                                       id="approved"
                                       name="approved"
                                       value="1"
                                       checked
                                       class="custom-control-input">
                                <label class="custom-control-label"
                                       for="approved">Одобрено</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio"
                                       id="notApproved"
                                       name="approved"
                                       value="0"
                                       class="custom-control-input">
                                <label class="custom-control-label"
                                       for="notApproved">Отклонено</label>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">
                                Сохранить
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr>
        {{ $events->links() }}
    @endif
@endsection
