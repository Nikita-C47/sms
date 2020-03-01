@extends('layouts.app')

@section('title', 'Список рейсов')

@section('content')
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>Вылет</th>
            <th>Прилет</th>
            <th>Откуда</th>
            <th>Куда</th>
            <th>Номер</th>
            <th>Борт</th>
            <th>Код ВС</th>
            <th>КВС</th>
            <th>Второй КВС</th>
        </tr>
        </thead>
        <tbody>
        @foreach($flights as $flight)
            <tr>
                <td>
                    {{ $flight->departure_datetime->format('d.m.Y H:i:s') }}
                </td>
                <td>
                    {{ $flight->arrival_datetime->format('d.m.Y H:i:s') }}
                </td>
                <td>
                    {{ $flight->departure_airport }}
                </td>
                <td>
                    {{ $flight->arrival_airport }}
                </td>
                <td>
                    {{ $flight->number }}
                </td>
                <td>
                    {{ $flight->board }}
                </td>
                <td>
                    {{ $flight->aircraft_code }}
                </td>
                <td>
                    {{ $flight->captain }}
                </td>
                <td>
                    {{ $flight->extra_captain }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    {{ $flights->links() }}
@endsection
