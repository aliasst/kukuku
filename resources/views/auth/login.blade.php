@extends('layouts.auth')

@section('title', 'Форма входа')

@section('content')
    <section id="" class="cabinet-section cabinet-section-auth">
        <div class="bg-inner"></div>
        <div class="container">
            <div class="row">
                <div class="col-12 mb-5">

                </div>
            </div>

            <div class="row justify-content-center">


                <div class="col-xs-12">
                    <div class="cabinet-title-section cabinet-title-section-auth cabinet-title-section-sub">
                        <H1>Вход в личный кабинет</H1>
                        <div class="auth-subtitle">Заполните данные, чтобы попасть в личный кабинет</div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-8">
                    <div class="request-card card auth-card mb-4">
                        <div class="card-content">

                            <form method="POST" action="{{ route('login') }}">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                @endif
                                @csrf

                                <div class="row mb-3">


                                    <div class="col-12">
                                        <div class="form-input">
                                            <input id="email" type="email" class=" " name="email"
                                                   value="{{ old('email') }}" required=""
                                                   autocomplete="off" autofocus="" placeholder="Email *">
                                        </div>

                                    </div>
                                </div>


                                <div class="row mb-5 justify-content-between">


                                    <div class="col-12">
                                        <div class="form-input">
                                            <input id="password" type="password" class="" name="password"
                                                   required="" autocomplete="current-password" placeholder="Пароль *">
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="btn-row btn-row-login">
                                            <button type="submit" class="orange-btn orange-btn-small">
                                                Войти
                                            </button>
                                        </div>


                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-2">

                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="btn-row btn-row-login text-center">
                                <a class="black-link" href="{{ route('register') }}">
                                    Регистрация аккаунта
                                </a>


                            </div>
                        </div>

                        <div class="col-md-4 col-xs-12">
                            <div style="" class="forget-link text-center">
                                <a class="black-link" href="{{ route('password.request') }}">
                                    Восстановить
                                </a>
                            </div>
                        </div>


                    </div>


                </div>
            </div>
        </div>
    </section>

@endsection
