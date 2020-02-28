@extends('layouts.app')

@section('title', 'Отклоненные события')

@section('content')
    @if(count($events) === 0)
        <div class="alert alert-info" role="alert">
            На данный момент в базе нет отлоненных событий.
        </div>
    @else
        <table class="table table-bordered-bd-primary table-hover">
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
                            <input type="hidden" name="approved" value="1"/>
                            <button type="submit" class="btn btn-success btn-sm">
                                Одобрить
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
