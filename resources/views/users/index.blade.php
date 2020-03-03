@extends('layouts.app')

@section('title', 'Список пользователей')

@section('content')
    <a href="{{ route('create-user') }}" class="btn btn-success">
        Добавить
    </a>
    <hr>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>ФИО</th>
            <th>Email</th>
            <th>Доступ</th>
            <th>Подразделение</th>
            <th>Создано</th>
            <th>Обновлено</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    @if(filled($user->department_id))
                        {{ $user->department->name }}
                    @else
                        <i class="text-danger fas fa-times"></i>
                    @endif
                </td>
                <td>{{ $user->created_at->format('d.m.Y H:i:s') }}</td>
                <td>{{ $user->updated_at->format('d.m.Y H:i:s') }}</td>
                <td>
                    <div class="row">
                        <div class="col pr-0">
                            <form method="post" action="{{ route('auth', ['id' => $user->id]) }}">
                                @csrf
                                <button class="btn btn-info btn-sm" type="submit" title="Авторизоваться">
                                    <i class="fas fa-sign-in-alt"></i>
                                </button>
                            </form>
                        </div>
                        <div class="col px-0">
                            <a href="{{ route('edit-user', ['id' => $user->id]) }}" class="btn btn-sm btn-primary" title="Редактировать">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div class="col pl-0">
                            <confirmation-modal v-bind:id="{{ $user->id }}"
                                                v-bind:entity_name="'{{ $user->name }}'"
                                                v-bind:action="'{{ route('delete-user', ['id' => $user->id]) }}'"></confirmation-modal>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    {{ $users->links() }}
@endsection
