@extends('layouts.app-new')

@section('title', "KuKu'Ku — Dance, Connect & Keep the Vibe in Dubai | AI-Powered Party")

@section('content')

    <!-- ===== HERO ===== -->
    <section class="hero">
        <div class="container hero__inner">
            <div class="hero__content">
                <div class="hero__badge">
                <span class="hero__badge-icon">
                        <img src="{{ asset('land-assets/img/icons/calend.png') }}" alt="">
                    </span>
                    DUBAI <span class="hero__badge-dot"></span> October 17, 2026
                </div>

                <h1 class="hero__title">
                    Dance. Connect
                    <span class="hero__title-highlight">Keep the Vibe</span>
                </h1>

                <h2 class="hero__desc">
                    A place to dance, meet new people, and spark real connections — all powered by our unique web app + qr.
                </h2>

                <div class="hero__info">
                    <div class="hero__info-item hero__info-item--date">
                    <span class="hero__info-icon">
                        <img src="{{ asset('land-assets/img/icons/info-icon_1.png') }}" alt="">
                    </span>
                        <div>
                            <span class="hero__info-label">October 17, 2026</span>
                            <span class="hero__info-sub">Saturday</span>
                        </div>
                    </div>
                    <div class="hero__info-divider"></div>
                    <div class="hero__info-item hero__info-item--location">
                    <span class="hero__info-icon">
                        <img src="{{ asset('land-assets/img/icons/info-icon_2.png') }}" alt="">
                    </span>
                        <div>
                            <span class="hero__info-label">Dubai Marina <br>Beach Club</span>
                            <span class="hero__info-sub">Dubai, UAE</span>
                        </div>
                    </div>
                    <div class="hero__info-divider"></div>
                    <div class="hero__info-item hero__info-item--guests">
                    <span class="hero__info-icon">
                        <img src="{{ asset('land-assets/img/icons/info-icon_3.png') }}" alt="">
                    </span>
                        <div>
                            <span class="hero__info-label"> 75–120 guests</span>
                            <span class="hero__info-sub">Limited capacity</span>
                        </div>
                    </div>
                </div>

                <div class="hero__actions">
                    <a href="#tickets" class="btn btn--primary btn--large">
                        <span class="btn__icon"> <img src="{{ asset('land-assets/img/icons/by.png') }}" alt=""></span> Buy Ticket
                    </a>
                    <a href="#experience" class="btn btn--outline btn--large"><span class="btn__icon"> <img src="{{ asset('land-assets/img/icons/play.png') }}" alt=""></span>Learn More</a>
                </div>
            </div>

            <div class="hero__image">

            </div>
        </div>
    </section>

    <!-- ===== EXPERIENCE ===== -->
    <section class="experience" id="experience">
        <div class="container experience__inner">
            <div class="experience__visual">
                <picture>
                    <source media="(max-width: 480px)" srcset="{{ asset('land-assets/img/experience2.png') }}">
                    <img src="{{ asset('land-assets/img/experience.png') }}" alt="Experience scene" />
                </picture>
            </div>

            <div class="experience__content">
                <span class="section-tag">The Experience</span>
                <h2 class="section-title experience__title">
                    Dance. Meet <span class="gradient-text">Stay Connected</span>
                </h2>
                <p class="experience__desc">
                    KuKu’Ku is where the night comes alive. Dance to unique beats, meet incredible people, and when the night ends, the connection lives on through our web app + qr.
                </p>
                <p class="experience__desc">
                    Our platform lets you match, chat, and continue building relationships long after the last track fades.
                </p>

                <div class="experience__tags">
                    <span class="experience__tag"><span class="experience__icon"> <img src="{{ asset('land-assets/img/icons/experience_1.svg') }}" alt=""></span>DJ</span>
                    <a target="_blank" href="https://music.apple.com/ru/artist/funkafunka/6791572586" class="experience__tag"><span class="experience__icon"> <img src="{{ asset('land-assets/img/icons/experience_2.svg') }}" alt=""></span>FunkaFunka Tracks</a>
                    <span class="experience__tag"><span class="experience__icon"> <img src="{{ asset('land-assets/img/icons/experience_3.svg') }}" alt=""></span>Live Vocals</span>
                    <span class="experience__tag"><span class="experience__icon"> <img src="{{ asset('land-assets/img/icons/experience_4.svg') }}" alt=""></span>App &amp; QR Codes</span>
                    <span class="experience__tag"><span class="experience__icon"> <img src="{{ asset('land-assets/img/icons/experience_5.svg') }}" alt=""></span>AI Heart Matching</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== MUSIC CARDS ===== -->
    <section class="music">
        <div class="container music__inner">
            <div class="music__card" data-audio="{{ asset('land-assets/music/Dancing_With_Tomorrow.mp3') }}">
                <div class="music__card-art">
                    <img src="{{ asset('land-assets/img/icons/mus_1.png') }}" alt="Dancing With Tomorrow" />
                </div>
                <div class="music__card-body">
                    <span class="music__card-title">Dancing With Tomorrow - Single</span>
                    <div class="music__card-play">
                    <span class="music__play-btn">
                        <img class="music__icon music__icon--play" src="{{ asset('land-assets/img/icons/play.png') }}" alt="Play" />
                        <img class="music__icon music__icon--pause" src="{{ asset('land-assets/img/icons/pause.svg') }}" alt="Pause" />
                    </span>
                        <span class="music__play-label">Play Music</span>
                    </div>
                </div>
            </div>

            <div class="music__card" data-audio="{{ asset('land-assets/music/summer_shine.wav') }}">
                <div class="music__card-art">
                    <img src="{{ asset('land-assets/img/icons/mus_2.png') }}" alt="Summer Shine" />
                </div>
                <div class="music__card-body">
                    <span class="music__card-title">Summer Shine - Single</span>
                    <div class="music__card-play">
                    <span class="music__play-btn">
                        <img class="music__icon music__icon--play" src="{{ asset('land-assets/img/icons/play.png') }}" alt="Play" />
                        <img class="music__icon music__icon--pause" src="{{ asset('land-assets/img/icons/pause.svg') }}" alt="Pause" />
                    </span>
                        <span class="music__play-label">Play Music</span>
                    </div>
                </div>
            </div>

            <div class="music__card" data-audio="{{ asset('land-assets/music/Sunshine_and_Soul.mp3') }}">
                <div class="music__card-art">
                    <img src="{{ asset('land-assets/img/icons/mus_3.png') }}" alt="Sunshine and Soul" />
                </div>
                <div class="music__card-body">
                    <span class="music__card-title">Sunshine and Soul - FunkaFunka</span>
                    <div class="music__card-play">
                    <span class="music__play-btn">
                        <img class="music__icon music__icon--play" src="{{ asset('land-assets/img/icons/play.png') }}" alt="Play" />
                        <img class="music__icon music__icon--pause" src="{{ asset('land-assets/img/icons/pause.svg') }}" alt="Pause" />
                    </span>
                        <span class="music__play-label">Play Music</span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ===== TECHNOLOGY ===== -->
    <section class="technology" id="technology">
        <div class="container technology__inner">
            <span class="section-tag section-tag--center">Powered by</span>
            <h2 class="section-title section-title--center technology__main-title">
                Technology That Connects
            </h2>
            <p class="technology__subtitle">
                We use cutting-edge tech to bring people together in the real world.
            </p>

            <div class="technology__cards">
                <div class="technology__card">
                    <div class="technology__card-icon">
                        <img src="{{ asset('land-assets/img/technology_1.png') }}" alt="AI Heart Matching" />
                    </div>
                    <h3 class="technology__card-title">AI Heart Matching</h3>
                    <p class="technology__card-desc">
                        After the party, our AI analyzes connections and suggests your perfect matches based on real interactions. If you want and approve this option.
                    </p>
                </div>

                <div class="technology__card">
                    <div class="technology__card-icon">
                        <img src="{{ asset('land-assets/img/technology_2.png') }}" alt="App & QR Codes" />
                    </div>
                    <h3 class="technology__card-title">App &amp; QR Codes</h3>
                    <p class="technology__card-desc">
                        Scan QR codes at the event to connect instantly. Continue the conversation online and offline.
                    </p>
                </div>

                <div class="technology__card">
                    <div class="technology__card-icon">
                        <img src="{{ asset('land-assets/img/technology_3.png') }}" alt="Web3.0 Rights" />
                    </div>
                    <h3 class="technology__card-title">Web3.0 Rights</h3>
                    <p class="technology__card-desc">
                        Full control over your content and participation.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== TICKETS ===== -->
    <section class="tickets" id="tickets">
        <div class="container tickets__inner">
            <h2 class="section-title section-title--center tickets__title">Get Your Ticket</h2>
            <p class="tickets__subtitle">
                Choose your ticket type. Limited spots available — secure yours now!
            </p>

            <div class="tickets__grid">
                <!-- LADIES -->
                <div class="tickets__card">
                    <div class="tickets__card-image">
                        <img src="{{ asset('land-assets/img/ticket_1.png') }}" alt="Ladies ticket" />
                    </div>
                    <h3 class="tickets__card-title">LADIES</h3>
                    <div class="tickets__card-price">$9.0</div>
                    <ul class="tickets__card-features">
                        <li>Full event access</li>
                        <li>DJ &amp; live vocals</li>
                        <li>Access to main stage</li>
                        <li>AI matching profile</li>
                        <li>App &amp; QR connect</li>
                    </ul>
                    <a href="#" class="btn btn--primary btn--block">Buy Ticket</a>
                </div>

                <!-- GENTLEMEN -->
                <div class="tickets__card">
                    <div class="tickets__card-image">
                        <img src="{{ asset('land-assets/img/ticket_2.png') }}" alt="Gentlemen ticket" />
                    </div>
                    <h3 class="tickets__card-title">GENTLEMEN</h3>
                    <div class="tickets__card-price">$12.0</div>
                    <ul class="tickets__card-features">
                        <li>Full event access</li>
                        <li>DJ &amp; live vocals</li>
                        <li>Access to main stage</li>
                        <li>AI matching profile</li>
                        <li>App &amp; QR connect</li>
                    </ul>
                    <a href="#" class="btn btn--primary btn--block">Buy Ticket</a>
                </div>

                <!-- VIP TABLE -->
                <div class="tickets__card">
                    <div class="tickets__card-image">
                        <img src="{{ asset('land-assets/img/ticket_3.png') }}" alt="VIP table ticket" />
                    </div>
                    <h3 class="tickets__card-title">VIP TABLE</h3>
                    <div class="tickets__card-price">$99</div>
                    <ul class="tickets__card-features">
                        <li>Priority check-in</li>
                        <li>Premium seating area</li>
                        <li>Exclusive lounge access</li>
                        <li>Personalized AI matching</li>
                    </ul>
                    <a href="#" class="btn btn--primary btn--block">Buy Ticket</a>
                </div>
            </div>
        </div>
    </section>

@endsection
