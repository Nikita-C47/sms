@extends('layouts.app')

@section('title', 'Просмотр события №'.$event->id)

@section('content')
    <div class="row">
        <div class="col-lg-6 col-sm-12">
            <table class="table table-bordered table-hover">
                <tr>
                    <th>Номер</th>
                    <td>{{ $event->id }}</td>
                </tr>
                <tr>
                    <th>Дата</th>
                    <td>{{ $event->date->format('d,m.Y') }}</td>
                </tr>
                <tr>
                    <th>Рейс</th>
                    <td>
                    @if(filled($event->flight_id))
                        <!-- Button trigger modal -->
                            <a href="#" data-toggle="modal" data-target="#flightModal">
                                {{ $event->flight->number }}
                            </a>
                            <!-- Modal -->
                            <div class="modal fade" id="flightModal" tabindex="-1" role="dialog" aria-labelledby="flightModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="flightModalLabel">Рейс {{ $event->flight->number }}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered-bd-primary table-hover table-striped">
                                                <tr>
                                                    <th>Дата вылета</th>
                                                    <td>{{ $event->flight->departure_datetime->format('d.m.Y H:i:s') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Дата прилета</th>
                                                    <td>{{ $event->flight->arrival_datetime->format('d.m.Y H:i:s') }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Борт</th>
                                                    <td>{{ $event->flight->board }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Код ВС</th>
                                                    <td>{{ $event->flight->aircraft_code }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Откуда</th>
                                                    <td>{{ $event->flight->departure_airport }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Куда</th>
                                                    <td>{{ $event->flight->arrival_airport }}</td>
                                                </tr>
                                                <tr>
                                                    <th>КВС</th>
                                                    <td>{{ $event->flight->captain }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Второй КВС</th>
                                                    <td>{{ $event->flight->extra_captain }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Подразделение</th>
                    <td>
                        @if(filled($event->department_id))
                            {{ $event->department->name }}
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Относится к</th>
                    <td>
                        @if(filled($event->relation_id))
                            {{ $event->relation->name }}
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Тип</th>
                    <td>
                        @if(filled($event->type_id))
                            {{ $event->type->name }}
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Категория</th>
                    <td>
                        @if(filled($event->category_id))
                            {{ $event->category->name }} ({{ $event->category->code }})
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Анонимное</th>
                    <td>
                        @include('widgets.boolean', ['value' => $event->anonymous])
                    </td>
                </tr>
                <tr>
                    <th>Статус обработки</th>
                    <td>{{ $event->approval_status }}</td>
                </tr>
                <tr>
                    <th>От кого сообщение</th>
                    <td>
                        @include('widgets.filled-or-none', ['value' => $event->initiator])
                    </td>
                </tr>
                <tr>
                    <th>Статус</th>
                    <td>
                        <span class="badge badge-pill {{ $event->status_badge_class }}">
                            {{ $event->status_text }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Сообщение</th>
                    <td>{{ $event->message }}</td>
                </tr>
                <tr>
                    <th>Комментарий</th>
                    <td>
                        @include('widgets.filled-or-none', ['value' => $event->commentary])
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-lg-6 col-sm-12">
            <table class="table table-bordered table-hover">
                <tr>
                    <th>Выявленная причина</th>
                    <td>@include('widgets.filled-or-none', ['value' => $event->reason])</td>
                </tr>
                <tr>
                    <th>Принятое решение</th>
                    <td>@include('widgets.filled-or-none', ['value' => $event->decision])</td>
                </tr>
                <tr>
                    <th>Дата устранения</th>
                    <td>
                        @if(filled($event->fix_date))
                            {{ $event->fix_date->format('d.m.Y') }}
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Ответственные подразделения</th>
                    <td>
                        @if(count($event->responsible_departments) > 0)
                            @foreach($event->responsible_departments as $responsibleDepartment)
                                <div>{{ $responsibleDepartment->name }}</div>
                            @endforeach
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Мероприятия</th>
                    <td>
                        @if(count($event->measures) > 0)
                            @foreach($event->measures as $measure)
                                <div class="card border-primary @if($loop->iteration > 1) mt-3 @endif">
                                    <div class="card-header bg-primary text-light">
                                        Мероприятие №{{ $loop->iteration }}
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            {{ $measure->text }}
                                        </p>
                                    </div>
                                    <div class="card-footer border-primary text-muted">
                                        {{ $measure->user_created_by->name }} ({{ $measure->created_at->format('d.m.Y H:i:s') }})
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Прикрепленные файлы</th>
                    <td>
                        @if(count($event->attachments) > 0)
                            @foreach($event->attachments as $attachment)
                                <div class="card border-dark @if($loop->iteration > 1) mt-3 @endif">
                                    <div class="card-body">
                                        <p class="card-text">
                                            <a href="{{ $attachment->link }}" target="_blank">
                                                {{ $attachment->original_name }} ({{ $attachment->size_text }})
                                            </a>
                                        </p>
                                    </div>
                                    <div class="card-footer border-dark text-muted">
                                        {{ $attachment->user_created_by->name }} ({{ $attachment->created_at->format('d.m.Y H:i:s') }})
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <i class="fas fa-times text-danger"></i>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Добавлено</th>
                    <td>
                        @if(filled($event->created_by))
                            {{ $event->user_created_by->name }} ({{ $event->created_at->format('d.m.Y H:i:s') }})
                        @else
                            {{  $event->created_at->format('d.m.Y H:i:s') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Изменено</th>
                    <td>
                        @if(filled($event->updated_by))
                            {{ $event->user_updated_by->name }} ({{ $event->updated_at->format('d.m.Y H:i:s') }})
                        @else
                            {{ $event->updated_at->format('d.m.Y H:i:s') }}
                        @endif
                    </td>
                </tr>
                @if($event->trashed())
                    <tr>
                        <th>Удалено</th>
                        <td>
                            @if(filled($event->deleted_by))
                                {{ $event->user_deleted_by->name }} ({{ $event->deleted_at->format('d.m.Y H:i:s') }})
                            @else
                                {{ $event->deleted_at->format('d.m.Y H:i:s') }}
                            @endif
                        </td>
                    </tr>
                @endif
            </table>
            <div class="row mt-3">
                <div class="col-lg col-sm-12">
                    <a href="{{ route('edit-event', ['id' => $event->id]) }}" class="btn btn-primary btn-block">
                        Редактировать
                    </a>
                </div>
                <div class="col-lg col-sm-12">
                    <a href="{{ route('home') }}" class="btn btn-info btn-block">
                        К списку событий
                    </a>
                </div>
                @if($event->trashed())
                    @if(Gate::allows('admin'))
                        <div class="col-lg col-sm-12">
                            <button data-toggle="modal" data-target="#eventRestoreModal" class="btn btn-outline-danger btn-block" title="Восстановить">
                                Восстановить
                            </button>
                            <div class="modal fade" id="eventRestoreModal" tabindex="-1" role="dialog" aria-labelledby="eventRestoreModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header p-0">
                                            <h5 class="modal-title pt-3 pl-3" id="eventRestoreModalLabel">Восстановить событие</h5>
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
                        <div class="col-lg col-sm-12">
                            <button data-toggle="modal" data-target="#eventDestroyModal" class="btn btn-danger btn-block" title="Уничтожить">
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
                    @endif
                @else
                    @if(Gate::allows('manager'))
                        <div class="col-lg col-sm-12">
                            <button data-toggle="modal" data-target="#eventDeleteModal" class="btn btn-danger btn-block" title="Удалить">
                                Удалить
                            </button>
                            <div class="modal fade" id="eventDeleteModal" tabindex="-1" role="dialog" aria-labelledby="eventDeleteModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header p-0">
                                            <h5 class="modal-title pt-3 pl-3" id="eventDeleteModalLabel">Подтверждение удаления</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-wrap">
                                                <span class="text-danger">ВНИМАНИЕ!</span>
                                                <span>
                                                    Вы действительно хотите удалить событие №{{ $event->id }}?
                                                    Событие не будет физически удалено из базы данных и его можно будет восстановить
                                                    при необходимости.
                                                </span>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                                            <form method="post" action="{{ route('delete-event', ['id' => $event->id]) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">Удалить</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
