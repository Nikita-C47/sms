@extends('layouts.app')

@section('title', 'Фильтры списка событий')

@section('content')
    <div class="alert alert-info" role="alert">
        <span class="font-weight-bold">ВНИМАНИЕ!</span> Если Вы хотите установить пустое значение фильтра (например
        показывать события, у которых нет ответственных подразделений), Вам нужно убрать все галочки в соответствующем
        фильтре!
    </div>
    <form method="post">
        @csrf
        <div class="form-group">
            <label class="font-weight-bold">Дата события:</label>
            <dates-range v-bind:start_date_value="'{{ $filters['date_from'] }}'"
                         v-bind:finish_date_value="'{{ $filters['date_to'] }}'"></dates-range>
            @error('date_from')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
            @error('date_to')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Борт:</label>
            <div>
            <select-all v-bind:name="'boards'" v-bind:checked="{{ filled($filters['boards']) ? 0 : 1 }}"></select-all>
            @foreach($boards as $board)
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox"
                           @if(in_array($board->board, $filters['boards'])) checked @endif
                           id="{{ $board->board }}"
                           name="boards[]"
                           value="{{ $board->board }}"
                           class="custom-control-input">
                    <label class="custom-control-label" for="{{ $board->board }}">
                        {{ $board->board }}
                    </label>
                </div>
            @endforeach
            </div>
            @error('boards')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">КВС:</label>
            <div>
                <select-all v-bind:name="'captains'" v-bind:checked="{{ filled($filters['captains']) ? 0 : 1 }}"></select-all>
                @foreach($captains as $captain)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox"
                               @if(in_array($captain->captain, $filters['captains'])) checked @endif
                               id="{{ $captain->captain }}"
                               name="captains[]"
                               value="{{ $captain->captain }}"
                               class="custom-control-input">
                        <label class="custom-control-label" for="{{ $captain->captain }}">
                            {{ $captain->captain }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('captains')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Где произошло:</label>
            <div>
                <select-all v-bind:name="'airports'" v-bind:checked="{{ filled($filters['airports']) ? 0 : 1 }}"></select-all>
                @foreach($airports as $airport)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox"
                               @if(in_array($airport->airport, $filters['airports'])) checked @endif
                               id="{{ $airport->airport }}"
                               name="airports[]"
                               value="{{ $airport->airport }}"
                               class="custom-control-input">
                        <label class="custom-control-label" for="{{ $airport->airport }}">
                            {{ $airport->airport }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('airports')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Статус события:</label>
            <div>
                <select-all v-bind:name="'statuses'" v-bind:checked="{{ filled($filters['statuses']) ? 0 : 1 }}"></select-all>
                @foreach($statuses as $code => $status)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox"
                               @if(in_array($code, $filters['statuses'])) checked @endif
                               id="{{ $code }}"
                               value="{{ $code }}"
                               name="statuses[]"
                               class="custom-control-input">
                        <label class="custom-control-label" for="{{ $code }}">
                            {{ $status }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('statuses')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Ответственные подразделения:</label>
            <div>
                <select-all v-bind:name="'responsible_departments'" v-bind:checked="{{ filled($filters['responsible_departments']) ? 0 : 1 }}"></select-all>
                @foreach($departments as $department)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox"
                               @if(in_array($department->department_id, $filters['responsible_departments'])) checked @endif
                               id="department_{{ $department->department_id }}"
                               value="{{ $department->department_id }}"
                               name="responsible_departments[]"
                               class="custom-control-input">
                        <label class="custom-control-label" for="department_{{ $department->department_id }}">
                            {{ $department->department->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('responsible_departments')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Кем создано:</label>
            <div>
                <select-all v-bind:name="'users'" v-bind:checked="{{ filled($filters['users']) ? 0 : 1 }}"></select-all>
                @foreach($users as $user)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox"
                               @if(in_array($user->created_by, $filters['users'])) checked @endif
                               id="user_{{ $user->created_by }}"
                               value="{{ $user->created_by }}"
                               name="users[]"
                               class="custom-control-input">
                        <label class="custom-control-label" for="user_{{ $user->created_by }}">
                            {{ $user->user_created_by->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('users')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">С чем связано:</label>
            <div>
                <select-all v-bind:name="'relations'" v-bind:checked="{{ filled($filters['relations']) ? 0 : 1 }}"></select-all>
                @foreach($relations as $relation)
                    <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox"
                               @if(in_array($relation->relation_id, $filters['relations'])) checked @endif
                               id="relation_{{ $relation->relation_id }}"
                               value="{{ $relation->relation_id }}"
                               name="relations[]"
                               class="custom-control-input">
                        <label class="custom-control-label" for="relation_{{ $relation->relation_id }}">
                            {{ $relation->relation->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('relations')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <label class="font-weight-bold">Прикрепленные файлы:</label>
            <div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio"
                           @if(!filled($filters['attachments'])) checked @endif
                           id="empty_attachments"
                           name="attachments"
                           value=""
                           class="custom-control-input">
                    <label class="custom-control-label" for="empty_attachments">
                        Без фильтра
                    </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio"
                           @if($filters['attachments'] === 1) checked @endif
                           id="with_attachments"
                           name="attachments"
                           value="1"
                           class="custom-control-input">
                    <label class="custom-control-label" for="with_attachments">
                        С файлами
                    </label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio"
                           @if($filters['attachments'] === 0) checked @endif
                           id="without_attachments"
                           name="attachments"
                           value="0"
                           class="custom-control-input">
                    <label class="custom-control-label" for="without_attachments">
                        Без файлов
                    </label>
                </div>
            </div>
            @error('attachments')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">
                Сохранить
            </button>
        </div>
    </form>
@endsection
