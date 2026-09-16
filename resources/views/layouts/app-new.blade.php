<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>
    <meta name="description" content="KuKu'Ku is where the night comes alive in Dubai. Dance to unique beats, meet incredible people, and stay connected through our AI-powered web app + QR. Limited capacity — get your ticket now!" />
    <meta name="keywords" content="KuKu'Ku, Dubai party, dance party Dubai, AI matching party, QR connect event, Dubai nightlife, meet people Dubai, Dubai Marina beach club, Dance Connect Keep the Vibe, KuKu'Ku Dubai tickets, AI Heart Matching, party with AI Dubai" />
    <meta name="author" content="KuKu'Ku / Ku Events" />
    <meta name="robots" content="index, follow" />

    <!-- ===== Open Graph (для соцсетей) ===== -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="KuKu'Ku — Dance, Connect & Keep the Vibe in Dubai" />
    <meta property="og:description" content="Dance to unique beats, meet incredible people, and stay connected through our AI-powered web app + QR. Dubai Marina Beach Club · August 29, 2026." />
{{--    <meta property="og:image" content="img/hero.png" />--}}
    <meta property="og:locale" content="en_US" />

    <!-- ===== Twitter Card ===== -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="KuKu'Ku — Dance, Connect & Keep the Vibe in Dubai" />
    <meta name="twitter:description" content="Dance to unique beats, meet incredible people, and stay connected through our AI-powered web app + QR." />
{{--    <meta name="twitter:image" content="img/hero.png" />--}}

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
                ©2026 KuKu’Ku Events <br>
                Dubai Marina Beach Club • October 17, 2026 • 75–120 guests <br>
                Made with ❤️ in Dubai
            </p>
        </div>
    </div>
</footer>

<script src="{{ asset('land-assets/js/script.js') }}"></script>
</body>
</html>
