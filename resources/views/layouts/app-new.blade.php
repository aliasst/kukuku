<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('land-assets/css/fonts.css') }}" />
    <link rel="stylesheet" href="{{ asset('land-assets/css/style.css') }}" />
</head>
<body>

<!-- ===== HEADER ===== -->
<header class="header">
    <div class="container header__inner">
        <a href="#" class="logo">KuKu’Ku</a>

        <nav class="nav">
            <ul class="nav__list">
                <li><a href="#experience">Experience</a></li>
                <li><a href="#technology">Technology</a></li>
                <li><a href="#tickets">Tickets</a></li>
                <!--                    <li class="mobile-nav-item nav-contact">-->
                <!--                        <a href="#tickets" class="btn btn&#45;&#45;primary btn&#45;&#45;header">Buy Ticket</a>-->
                <!--                    </li>-->
            </ul>
        </nav>

        <a href="#tickets" class="btn btn--primary btn--header">Buy Ticket</a>


        <button class="burger-btn" aria-label="Toggle menu">
            <span class="burger-line"></span>
            <span class="burger-line"></span>
            <span class="burger-line"></span>
        </button>
    </div>
</header>
@yield('content')
<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container footer__inner">
        <div class="footer__logo">KuKu’Ku</div>
        <div class="footer__info">
            <p>
                ©2026 KuKu’Ku / Ku Events (Ideas Stream Digital Marketing)<br />
                e-Trader License • Trade Name<br />
                Dubai Marina Beach Club • August 29, 2026 • 75–120 guests<br />
                Made with ❤️ in Dubai
            </p>
        </div>
    </div>
</footer>

<script src="{{ asset('land-assets/js/script.js') }}"></script>
</body>
</html>
