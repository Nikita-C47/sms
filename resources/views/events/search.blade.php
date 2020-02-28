@extends('layouts.app')

@section('title', 'Результаты поиска для события №'.$id)

@section('content')
    @if(filled($event))
        <div class="table-responsive">
            <table class="table table-bordered-bd-primary table-hover">
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
                <tr @if(filled($event->status_row_class)) class="{{ $event->status_row_class }}" @endif>
                    <td>
                        <a href="{{ route('view-event', ['id' => $event->id]) }}" class="font-weight-bold">
                            {{ $event->id }}
                        </a>
                    </td>
                    <td>{{ $event->date->format('d.m.Y') }}</td>
                    <td>
                        @if(count($event->responsible_departments) > 0)
                            <div class="py-3">
                                @foreach($event->responsible_departments as $responsibleDepartment)
                                    <div>{{ $responsibleDepartment->department->name }}</div>
                                @endforeach
                            </div>
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
                            <br>
                        @endif
                        ({{ $event->created_at->format('d.m.Y H:i:s') }})
                    </td>
                </tr>
            </table>
        </div>
    @else
        <div class="alert alert-info" role="alert">
            По вашему запросу ничего не найдено.
        </div>
    @endif
@endsection
