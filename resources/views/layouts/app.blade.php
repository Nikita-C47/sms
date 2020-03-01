<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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

<body class="fixed-navbar fixed-layout">
<div class="page-wrapper" id="app">
    <!-- START HEADER-->
    <header class="header">
        <div class="page-brand">
            <a href="{{ route('home') }}">
                <img src="{{ asset('img/logo/logo-horizontal-xs.png') }}" alt="{{ config('app.name', 'Laravel') }}">
            </a>
        </div>
        <div class="flexbox flex-1">
            <!-- START TOP-LEFT TOOLBAR-->
            <ul class="nav navbar-toolbar">
                <li>
                    <a class="nav-link sidebar-toggler js-sidebar-toggler"><i class="ti-menu"></i></a>
                </li>
                @auth
                <li class="d-sm-none d-none d-lg-inline d-md-inline d-xl-inline">
                    <form class="navbar-search" method="post" action="{{ route('find-event') }}">
                        <div class="rel">
                            <span class="search-icon"><i class="ti-search"></i></span>
                            <input class="form-control" placeholder="Номер события..." name="query">
                        </div>
                    </form>
                </li>
                @unless(Route::currentRouteName() === 'search-event')
                    @error('query')
                        @include('widgets.notification', ['alert' => ['type' => 'danger', 'text' => $message]])
                    @enderror
                @endunless
                @endauth
            </ul>
            <!-- END TOP-LEFT TOOLBAR-->
            <!-- START TOP-RIGHT TOOLBAR-->
            <ul class="nav navbar-toolbar">
                @auth
                <li class="dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle link" data-toggle="dropdown">
                        <img src="{{ asset('img/admin-avatar.png') }}" alt="{{ Auth::user()->name }}" />
                        <span></span>
                        {{ Auth::user()->name }}
                        <i class="fa fa-angle-down m-l-5"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <!--
                        <a class="dropdown-item" href="profile.html"><i class="fa fa-user"></i>Profile</a>
                        <a class="dropdown-item" href="profile.html"><i class="fa fa-cog"></i>Settings</a>
                        <a class="dropdown-item" href="javascript:;"><i class="fa fa-support"></i>Support</a>
                        <li class="dropdown-divider"></li>
                        -->
                        <a class="dropdown-item" href="{{ route('logout-get') }}">
                            <i class="fa fa-power-off"></i>
                            Выйти
                        </a>
                    </ul>
                </li>
                @endauth
                @guest
                    <li>
                        <a class="nav-link" href="{{ route('login') }}">
                            Войти
                        </a>
                    </li>
                @endguest
            </ul>
            <!-- END TOP-RIGHT TOOLBAR-->
        </div>
    </header>
    <!-- END HEADER-->
    <!-- START SIDEBAR-->
    <nav class="page-sidebar" id="sidebar">
        <div id="sidebar-collapse">
            <ul class="side-menu metismenu">
                @auth
                    <li class="mt-2">
                        <a href="{{ route('home') }}">
                            <i class="sidebar-item-icon fas fa-list-alt"></i>
                            <span class="nav-label">
                                Список событий
                            </span>
                        </a>
                    </li>
                @if(Gate::allows('manager'))
                    <li>
                        <a href="javascript:;">
                            <i class="sidebar-item-icon fas fa-list"></i>
                            <span class="nav-label">События</span>
                            <i class="fa fa-angle-left arrow"></i>
                        </a>
                        <ul class="nav-2-level collapse">
                            <li>
                                <a href="{{ route('events-needs-approval') }}">
                                    Не обработанные
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('events-not-approved') }}">
                                    Отклонённые
                                </a>
                            </li>
                            @if(Gate::allows('admin'))
                            <li>
                                <a href="{{ route('events-trashed') }}">
                                    Удаленные
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(Gate::allows('admin'))
                    <li>
                        <a href="{{ route('users') }}">
                            <i class="sidebar-item-icon fas fa-users"></i>
                            <span class="nav-label">
                                Пользователи
                            </span>
                        </a>
                    </li>
                @endif
                @if(Gate::allows('manager'))
                    <li>
                        <a href="javascript:;">
                            <i class="sidebar-item-icon fas fa-book"></i>
                            <span class="nav-label">Справочники</span>
                            <i class="fa fa-angle-left arrow"></i>
                        </a>
                        <ul class="nav-2-level collapse">
                            @if(Gate::allows('admin'))
                                <li>
                                    <a href="{{ route('departments') }}">
                                        Отделы
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('event-types') }}">
                                        Типы событий
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('event-relations') }}">
                                        Типы мероприятий
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('flights') }}">
                                        Рейсы
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('event-categories') }}">
                                    Категории событий
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                    <li class="d-block d-sm-block d-md-none d-lg-none d-xl-none">
                        <a href="{{ route('search-event') }}">
                            <i class="sidebar-item-icon fas fa-search"></i>
                            <span class="nav-label">
                                Поиск события
                            </span>
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
    <!-- END SIDEBAR-->
    <div class="content-wrapper">
        <div class="page-heading pt-1">
            <h1 class="page-title">
                @yield('title')
            </h1>
        </div>
        @includeWhen(session('alert'), 'widgets.alert', ['alert' => session('alert')])
        <!-- START PAGE CONTENT-->
        <div class="page-content fade-in-up pt-0">
            <div class="row">
                <div class="col-12">
                    <div class="ibox">
                        <div class="ibox-body">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END THEME CONFIG PANEL-->
<!-- BEGIN PAGA BACKDROPS-->
<div class="sidenav-backdrop backdrop"></div>
<div class="preloader-backdrop">
    <div class="page-preloader">Загрузка</div>
</div>
<!-- END PAGA BACKDROPS-->
<!-- CORE PLUGINS-->
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
</body>

</html>
