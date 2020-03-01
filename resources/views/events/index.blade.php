@extends('layouts.app')

@section('title', 'События')

@section('content')
    @if(count($events) === 0)
        <div class="alert alert-info" role="alert">
            На данный момент в базе нет событий. Вы можете
            <a href="{{ route('create-event') }}" class="alert-link">
                создать новое
            </a>.
        </div>
    @else
        <a href="{{ route('create-event') }}" class="btn btn-success">
            Добавить событие
        </a>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <tr>
                    <th>Номер</th>
                    <th>Дата</th>
                    <th>Ответственные подразделения</th>
                    <th>Рейс</th>
                    <th>Где произошло</th>
                    <th>Относится к</th>
                    <th>Подразделение</th>
                    <th>Добавлено</th>
                </tr>
                @foreach($events as $event)
                    <tr @if(filled($event->status_row_class)) class="{{ $event->status_row_class }}" @endif>
                        <td>
                            <a href="{{ route('view-event', ['id' => $event->id]) }}" class="font-weight-bold">
                                {{ $event->id }}
                            </a>
                        </td>
                        <td>{{ $event->date->format('d.m.Y') }}</td>
                        <td>
                            @if(count($event->responsible_departments) > 0)
                                @foreach($event->responsible_departments as $responsibleDepartment)
                                    <div>{{ $responsibleDepartment->name }}</div>
                                @endforeach
                            @else
                                <i class="fas fa-times text-danger"></i>
                            @endif
                        </td>
                        <td>
                            @if(filled($event->flight_id))
                                <a tabindex="0" data-placement="right" data-toggle="popover" data-trigger="focus"
                                   class="text-primary"
                                   title="Рейс {{ $event->flight->number }}"
                                   data-html="true"
                                   data-content="@include('partial.flight-popover-info', ['flight' => $event->flight])">
                                    {{ $event->flight->number }}
                                </a>
                            @else
                                <i class="fas fa-times text-danger"></i>
                            @endif
                        </td>
                        <td>
                            @include('widgets.filled-or-none', ['value' => $event->airport])
                        </td>
                        <td>
                            @if(filled($event->relation_id))
                                {{ $event->relation->name }}
                            @else
                                <i class="fas fa-times text-danger"></i>
                            @endif
                        </td>
                        <td>
                            @if(filled($event->department_id))
                                {{ $event->department->name }}
                            @else
                                <i class="fas fa-times text-danger"></i>
                            @endif
                        </td>
                        <td>
                            @if(filled($event->created_by))
                                {{ $event->user_created_by->name }}
                                <br>
                            @endif
                            ({{ $event->created_at->format('d.m.Y H:i:s') }})
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <hr>
        {{ $events->links() }}
    @endif
@endsection
