@extends('layouts.app')

@section('title', 'Удаленные события')

@section('content')
    @if(count($events) === 0)
        <div class="alert alert-info" role="alert">
            На данный момент в базе нет удаленныъ событий.
        </div>
    @else
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
                    <th>Кем добавлено</th>
                    <th>Когда добавлено</th>
                    <th>Действия</th>
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
                                <ul>
                                    @foreach($event->responsible_departments as $responsible_department)
                                        <li>{{ $responsible_department->name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <i class="fas fa-times text-danger"></i>
                            @endif
                        </td>
                        <td>
                            @if(filled($event->flight_id))
                                <a tabindex="0" data-placement="right" data-toggle="popover" data-trigger="focus"
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
                            @else
                                <i class="fas fa-times text-danger"></i>
                            @endif
                        </td>
                        <td>
                            {{ $event->created_at->format('d.m.Y H:i:s') }}
                        </td>
                        <td>
                            <div class="my-2">
                                <button data-toggle="modal" data-target="#eventRestoreModal" class="btn btn-sm btn-outline-danger btn-block" title="Восстановить">
                                    Восстановить
                                </button>
                                <div class="modal fade" id="eventRestoreModal" tabindex="-1" role="dialog" aria-labelledby="eventRestoreModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header p-0">
                                                <h5 class="modal-title pt-3 pl-3" id="eventDeleteModalLabel">Восстановить событие</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-wrap">
                                                    <span class="text-danger">ВНИМАНИЕ!</span>
                                                    <span>
                                                                Вы действительно хотите восстановить событие №{{ $event->id }}?
                                                                Событие появится в общем списке и будет видно всем менеджерам событий и администраторам,
                                                                а также автору события, если таковой имеется.
                                                            </span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                                <form method="post" action="{{ route('event-restore', ['id' => $event->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Восстановить</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="my-2">
                                <button data-toggle="modal" data-target="#eventDestroyModal" class="btn btn-sm btn-danger btn-block" title="Уничтожить">
                                    Уничтожить
                                </button>
                                <div class="modal fade" id="eventDestroyModal" tabindex="-1" role="dialog" aria-labelledby="eventDestroyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header p-0">
                                                <h5 class="modal-title pt-3 pl-3" id="eventDestroyModalLabel">Удалить событие безвозвратно</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-wrap">
                                                    <span class="text-danger">ВНИМАНИЕ!</span>
                                                    <span>
                                                        Вы действительно хотите безвозвратно удалить событие №{{ $event->id }}?
                                                        Событие будет удалено из базы данных и его невозможно будет восстановить.
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                                <form method="post" action="{{ route('event-destroy', ['id' => $event->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <hr>
        {{ $events->links() }}
    @endif
@endsection
