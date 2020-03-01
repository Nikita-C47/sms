@extends('layouts.app')

@section('title', 'Список категорий событий')

@section('content')
    <a href="{{ route('create-event-category') }}" class="btn btn-success">
        Добавить
    </a>
    <hr>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>Код</th>
            <th>Название</th>
            <th>Отдел</th>
            <th>Создано</th>
            <th>Обновлено</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entities as $entity)
            <tr>
                <td>
                    {{ $entity->id }}
                </td>
                <td>
                    {{ $entity->code }}
                </td>
                <td>
                    {{ $entity->name }}
                </td>
                <td>
                    @if(filled($entity->department_id))
                        {{ $entity->department->name }}
                    @endif
                </td>
                <td>
                    {{ $entity->created_at->format('d.m.Y H:i:s') }}
                </td>
                <td>
                    {{ $entity->updated_at->format('d.m.Y H:i:s') }}
                </td>
                <td>
                    <a href="{{ route('edit-event-category', ['id' => $entity->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <confirmation-modal v-bind:id="{{ $entity->id }}"
                                        v-bind:entity_name="'{{ $entity->name }}'"
                                        v-bind:action="'{{ route('delete-event-category', ['id' => $entity->id]) }}'"></confirmation-modal>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    {{ $entities->links() }}
@endsection
