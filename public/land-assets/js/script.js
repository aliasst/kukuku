document.addEventListener('DOMContentLoaded', function () {

    // ===== МУЗЫКАЛЬНЫЙ ПЛЕЕР =====
    const cards = document.querySelectorAll('.music__card');

    let currentAudio = null;  // текущий объект Audio
    let currentCard  = null;  // текущая карточка

    // Сброс состояния карточки (вернуть play-иконку и текст)
    function resetCard(card) {
        if (!card) return;
        card.classList.remove('playing');
        const label = card.querySelector('.music__play-label');
        if (label) label.textContent = 'Play Music';
    }

    cards.forEach(card => {
        const playBtn   = card.querySelector('.music__play-btn');
        const playLabel = card.querySelector('.music__play-label');
        const src       = card.dataset.audio;

        if (!src) return;

        // Создаём Audio для каждой карточки отдельно
        const audio = new Audio(src);
        audio.preload = 'none'; // подгружаем только при клике

        function togglePlay(e) {
            e.preventDefault();

            // Если играет другой трек — останавливаем его и сбрасываем
            if (currentAudio && currentAudio !== audio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
                resetCard(currentCard);
            }

            if (audio.paused) {
                // Запускаем текущий
                audio.play().then(() => {
                    card.classList.add('playing');
                    playLabel.textContent = 'Pause';
                    currentAudio = audio;
                    currentCard  = card;
                }).catch(err => {
                    console.warn('Не удалось воспроизвести трек:', err);
                });
            } else {
                // Пауза для текущего
                audio.pause();
                resetCard(card);
                currentAudio = null;
                currentCard  = null;
            }
        }

        playBtn.addEventListener('click', togglePlay);
        playLabel.addEventListener('click', togglePlay);

        // Когда трек доиграл — возвращаем кнопку в исходное состояние
        audio.addEventListener('ended', function () {
            resetCard(card);
            currentAudio = null;
            currentCard  = null;
        });
    });


    // ===== БУРГЕР-МЕНЮ =====
    const burger = document.querySelector('.burger-btn');
    const nav    = document.querySelector('.nav');

    if (burger && nav) {
        burger.addEventListener('click', function (e) {
            e.stopPropagation();
            this.classList.toggle('open');
            nav.classList.toggle('open');
        });

        const links = nav.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function () {
                burger.classList.remove('open');
                nav.classList.remove('open');
            });
        });

        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !burger.contains(e.target)) {
                burger.classList.remove('open');
                nav.classList.remove('open');
            }
        });
    }


    // ===== ПЛАВНЫЙ СКРОЛЛ =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
