<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--<link rel="shortcut icon" href="favicon.ico?v=1.2" type="image/x-icon" />-->
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}" />
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="/img/logo.png" />
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

                    </div>
                    <div class="header-row-right">


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

</body>
</html>
