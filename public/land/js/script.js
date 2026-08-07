// Minimal interactivity for "Play Music" buttons
document.addEventListener('DOMContentLoaded', function() {
    const playButtons = document.querySelectorAll('.music__play-btn, .music__play-label');

    playButtons.forEach(function(el) {
        el.addEventListener('click', function(e) {
            // Find the closest music card and get the track title
            const card = this.closest('.music__card');
            if (card) {
                const title = card.querySelector('.music__card-title')?.textContent || 'Track';
                alert(`🎵 Now playing: ${title}`);
            } else {
                alert('🎵 Play music feature (demo)');
            }
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});



document.addEventListener('DOMContentLoaded', function() {

    // ===== БУРГЕР-МЕНЮ =====
    const burger = document.querySelector('.burger-btn');
    const nav = document.querySelector('.nav');

    if (burger && nav) {
        burger.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('open');
            nav.classList.toggle('open');
        });

        // Закрываем меню при клике на любой пункт меню или кнопку внутри
        const links = nav.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                burger.classList.remove('open');
                nav.classList.remove('open');
            });
        });

        // Закрываем при клике вне меню и бургера
        document.addEventListener('click', function(e) {
            if (!nav.contains(e.target) && !burger.contains(e.target)) {
                burger.classList.remove('open');
                nav.classList.remove('open');
            }
        });
    }

    // ===== PLAY MUSIC (ваш существующий код) =====
    const playButtons = document.querySelectorAll('.music__play-btn, .music__play-label');
    playButtons.forEach(function(el) {
        el.addEventListener('click', function(e) {
            const card = this.closest('.music__card');
            if (card) {
                const title = card.querySelector('.music__card-title')?.textContent || 'Track';
                alert(`🎵 Now playing: ${title}`);
            } else {
                alert('🎵 Play music feature (demo)');
            }
        });
    });

    // ===== ПЛАВНЫЙ СКРОЛЛ (ваш существующий код) =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});