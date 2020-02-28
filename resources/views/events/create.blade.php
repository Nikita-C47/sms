@extends('layouts.app')

@section('title', 'Добавить событие')

@section('content')
    <event-form v-bind:form_id="'event_form'"
                v-bind:statuses='@json($statuses)'
                v-bind:relations='@json($relations)'
                v-bind:departments='@json($departments)'
                v-bind:types='@json($types)'
                v-bind:flights_url="'{{ route('get-flights') }}'"
                v-bind:categories_url="'{{ route('get-event-categories') }}'"
                v-bind:events_url="'{{ route('home') }}'"></event-form>
@endsection
