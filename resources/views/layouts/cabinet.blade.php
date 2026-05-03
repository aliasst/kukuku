<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--<link rel="shortcut icon" href="favicon.ico?v=1.2" type="image/x-icon" />-->
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}"/>
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
    @stack('styles')
    <meta property="og:type" content="website"/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content="/img/logo.png"/>
    <title>@yield('title')</title>
</head>
<body>
<header>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="header-row c-row">
                    <div class="header-item header-logo-item">
                        КуКу<span>’</span>Ку
                    </div>
                    <div class="header-item header-menu-item">
                        <ul>
                            <li class="{{ request()->routeIs('profile.show') ? 'active' : '' }}"><a href="{{ route('profile.show') }}">Карточка участника</a></li>
                            <li class="{{ request()->routeIs('events.upcoming') ? 'active' : '' }}"><a href="{{ route('events.upcoming') }}">Будущие мероприятия</a></li>
                            <li class="{{ request()->routeIs('events.past') ? 'active' : '' }}"><a href="{{ route('events.past') }}">Прошедшие мероприятия</a></li>
                        </ul>
                    </div>
                    <div class="header-row-right">
                        <div class="action">
                            <div class="profile" onclick="menuToggle()">
                                <img
                                    @if(Auth::user()->profile)
                                        @if(Auth::user()->profile->avatar)
                                            src="{{ Storage::url(Auth::user()->profile->avatar) }}"
                                    @else
                                        src="{{asset('img/avatar.svg') }}"
                                    @endif

                                    @else
                                        src="{{asset('img/avatar.svg') }}"
                                    @endif
                                >

                                <i class="menu-accord-ico"></i>
                            </div>
                            <div class="menu">

                                <ul>
                                    <li><a href="{{ route('profile.show') }}">Профиль</a></li>
                                    <li><a href="{{ route('my.winks') }}">Мои подмигивания</a></li>
                                    <li><a href="{{ route('my.gifts') }}">Мои подарки</a></li>
                                    @if (Route::has('logout'))
                                        <li><a class="btn-link btn-unlogin" href="{{ route('logout') }}"
                                               onclick="event.preventDefault();
                                                                         document.getElementById('logout-form').submit();">
                                                {{ __('Выйти') }}
                                            </a></li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              class="d-none">
                                            @csrf
                                        </form>

                                    @endif



                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

@yield('content')


<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/waypoints.min.js') }}"></script>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/jquery.maskedinput.min.js') }}"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
@stack('scripts')
</body>
</html>

