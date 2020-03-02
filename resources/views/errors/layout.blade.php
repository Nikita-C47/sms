<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/error.css') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-32x32.png') }}" type="image/png">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#3498db">
    <meta name="msapplication-TileColor" content="#3498db">
    <meta name="theme-color" content="#ffffff">
    <meta property="og:title" content="@yield('title') - {{ config('app.name', 'Laravel') }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('img/logo/logo-bordered-md.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:description" content="Safety Management System - База событий для авиакомпаний">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
</head>

<body class="bg-silver-100">
<div class="content">
    <h1 class="m-t-20">@yield('code')</h1>
    <p class="error-title">@yield('title')</p>
    <p class="m-b-20">
        @yield('message')
    </p>
    <p class="m-b-20">
        @yield('description')
    </p>
    <div class="text-center">
        <a class="btn btn-success" href="{{ route('home') }}">
            @lang('Go homepage')
        </a>
    </div>
</div>
<!-- BEGIN PAGA BACKDROPS-->
<div class="sidenav-backdrop backdrop"></div>
<div class="preloader-backdrop">
    <div class="page-preloader">Загрузка</div>
</div>
<!-- END PAGA BACKDROPS-->
<!-- CORE SCRIPTS-->
<script src="{{ asset('js/error.js') }}" type="text/javascript"></script>
</body>

</html>
