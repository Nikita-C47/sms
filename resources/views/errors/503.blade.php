@extends('errors::layout')

@if(app()->isDownForMaintenance())
    @section('title', __('errors.maintenance'))
    @section('code', '503')
    @section('description', "В данный момент мы проводим на сайте технические работы. Попробуйте обновить страницу позже, возможно мы уже закончим к этому моменту.")
@else
    @section('title', __('Service Unavailable'))
    @section('code', '503')
    @section('message', __($exception->getMessage() ?: 'errors.503'))
@endif
