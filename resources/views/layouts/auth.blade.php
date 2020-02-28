<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-32x32.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#1269db">
    <meta name="msapplication-TileColor" content="#1269db">
    <meta name="theme-color" content="#1269db">
    <meta property="og:title" content="@yield('title') - {{ config('app.name', 'Laravel') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('img/logo/logo-lg-bordered.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:description" content="Safety Management System - База событий для авиакомпаний">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
</head>
<body data-background-color="bg3">
<div class="container-fluid" id="app" style="height: 100vh;">
<div class="row justify-content-center align-items-center" style="height: 100%;">
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center mb-3">
                    <img src="{{ asset('img/logo/logo-horizontal-lg.png') }}" alt="{{ config('app.name', 'Laravel') }}" style="width: 150px;">
                </div>
                @yield('content')
            </div>
        </div>
    </div>
</div>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
