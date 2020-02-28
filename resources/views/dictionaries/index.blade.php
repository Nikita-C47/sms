@extends('layouts.app')

@section('title', 'Список '.$entitiesName)

@section('content')
    <a href="{{ route('create-'.$entityType) }}" class="btn btn-success">
        Добавить
    </a>
    <hr>
    <table class="table table-bordered-bd-primary table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>Название</th>
            <th>Добавлено</th>
            <th>Изменено</th>
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
                    {{ $entity->name }}
                </td>
                <td>
                    {{ $entity->created_at->format('d.m.Y H:i:s') }}
                </td>
                <td>
                    {{ $entity->updated_at->format('d.m.Y H:i:s') }}
                </td>
                <td>
                    <a href="{{ route('edit-'.$entityType, ['id' => $entity->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    <confirmation-modal v-bind:id="{{ $entity->id }}"
                                        v-bind:entity_name="'{{ $entity->name }}'"
                                        v-bind:action="'{{ route('delete-'.$entityType, ['id' => $entity->id]) }}'"></confirmation-modal>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    {{ $entities->links() }}
@endsection
