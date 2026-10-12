/* Host (tablet) screen: shows the code, plays the tracks, drives the game */
(function () {
  'use strict';
  var KK = window.KK, t = KK.t, esc = KK.esc;
  var app = document.getElementById('app');
  var hdr = document.getElementById('hdr');
  var langsEl = document.getElementById('langs');
  var CSRF = app.getAttribute('data-csrf');

  var S = {
    gameId: +app.getAttribute('data-game') || 0,
    state: null,
    key: '',
    offset: 0,
    pollTimer: null,
    audio: new Audio(),
    audioPos: null,
    audioStart: 0,
    toAnswerSent: null,
    sumSent: null,
    sumSentAt: 0,
    onlyClaimed: false
  };
  S.audio.preload = 'auto';

  KK.lang = KK.store.get('kk_host_lang') || 'en';

  function api(action, body) {
    var b = body || {};
    b.game_id = S.gameId;
    if (S.state && b.position == null) { b.position = S.state.game.position; }
    return KK.api(action, b, { 'X-CSRF': CSRF }).then(function (j) {
      if (j._status === 401) { location.reload(); }
      if (j.ok && j.game) { apply(j); }
      return j;
    });
  }

  function drawLangs() {
    KK.langSwitch(langsEl, KK.lang, function (l) {
      KK.lang = l;
      KK.store.set('kk_host_lang', l);
      drawLangs();
      if (S.gameId) { api('host_lang', { lang: l }); }
      S.key = '';
      S.state ? render() : renderOpen();
    });
  }
  drawLangs();

  // ------------------------------------------------------------ polling
  function poll() {
    clearTimeout(S.pollTimer);
    if (!S.gameId) { renderOpen(); return; }
    api('host_state').then(function (j) {
      if (!j.ok && j.error === 'no_game') { S.gameId = 0; renderOpen(); return; }
      S.pollTimer = setTimeout(poll, 1000);
    }).catch(function () {
      KK.toast(t('offline'));
      S.pollTimer = setTimeout(poll, 2500);
    });
  }

  function apply(j) {
    S.state = j;
    S.offset = j.now - Date.now() / 1000;
    if (j.game.host_lang && j.game.host_lang !== KK.lang && !S.langInit) {
      KK.lang = j.game.host_lang; drawLangs();
    }
    S.langInit = true;
    render();
    audioControl();
  }

  // ------------------------------------------------------------ open a table
  function renderOpen() {
    hdr.innerHTML = '<a class="btn btn--ghost btn--s" href="admin.php">' + esc(t('h_admin')) + '</a>';
    app.innerHTML =
      '<div class="phone" style="padding-top:40px"><div class="panel stack-l center">' +
        '<h1 class="h-l">' + esc(t('h_open_table')) + '</h1>' +
        '<p class="muted" style="margin:0">' + esc(t('h_open_sub')) + '</p>' +
        '<button class="btn btn--primary btn--large" id="openBtn" type="button">' + esc(t('h_open_table')) + '</button>' +
      '</div></div>';
    document.getElementById('openBtn').onclick = function () {
      KK.api('host_create', { lang: KK.lang }, { 'X-CSRF': CSRF }).then(function (j) {
        if (!j.ok) { KK.toast(t('err_generic')); return; }
        S.gameId = j.game_id; S.key = ''; S.audioPos = null; S.toAnswerSent = null; S.sumSent = null;
        try { history.replaceState(null, '', 'host.php?game=' + j.game_id); } catch (e) { /* ignore */ }
        unlockAudio();
        poll();
      });
    };
  }

  // ------------------------------------------------------------ render
  function render() {
    var s = S.state, g = s.game;
    var key = [KK.lang, g.phase, g.position, s.summary ? 1 : 0,
      g.phase === 'lobby' ? s.cards.map(function (c) { return c.taken ? 1 : 0; }).join('') : ''].join('|');
    if (key !== S.key) {
      S.key = key;
      header();
      var html = '';
      if (g.phase === 'lobby') { html = viewLobby(); }
      else if (g.phase === 'listen') { html = viewListen(); }
      else if (g.phase === 'answer') { html = viewAnswer(); }
      else if (g.phase === 'summarizing') { html = viewThinking(); }
      else if (g.phase === 'summary') { html = viewSummary(); }
      else if (g.phase === 'finished') { html = viewFinished(); }
      app.innerHTML = html;
      bind();
    }
    live();
  }

  function header() {
    var g = S.state.game;
    var playing = g.phase !== 'lobby' && g.phase !== 'finished';
    hdr.innerHTML =
      (playing ? '<span class="badge"><span class="badge__dot"></span>' + esc(t('h_code')) + ': ' + esc(g.code) + '</span>' : '') +
      (playing ? ' <button class="btn btn--ghost btn--s" type="button" data-act="finish">' + esc(t('h_finish')) + '</button>' : '') +
      (g.phase === 'lobby' || g.phase === 'finished' ? '<a class="btn btn--ghost btn--s" href="admin.php">' + esc(t('h_admin')) + '</a>' : '');
    var fin = hdr.querySelector('[data-act="finish"]');
    if (fin) {
      fin.onclick = function () {
        if (confirm(t('h_confirm_finish'))) { fadeOut(); api('host_finish'); }
      };
    }
  }

  function cardsGrid() {
    return '<div class="host-cards">' + S.state.cards.map(function (c) {
      return '<div class="tcard' + (c.taken ? ' is-taken' : '') + '" data-taken="✓ ' + esc(t('taken')) + '">' +
        KK.art(c.cover, c.title) + '<div class="tcard__title">' + esc(c.title) + '</div></div>';
    }).join('') + '</div>';
  }

  function viewLobby() {
    var s = S.state;
    return '<div class="host-grid">' +
      '<section class="stack-l">' +
        '<span class="badge badge--live"><span class="badge__dot"></span>KuKu\'Ku Coffee</span>' +
        '<h1 class="h-xl">' + esc(t('h_join_title')).replace(/(\S+)$/, '<span class="gradient-text">$1</span>') + '</h1>' +
        '<p class="muted" style="margin:0;font-size:18px">' + esc(t('h_join_sub')) + '</p>' +
        '<div class="codebox panel">' +
          '<div class="qr" id="qrJoin"></div>' +
          '<div class="stack"><span class="tag">' + esc(t('h_code')) + '</span>' +
          '<div class="code-digits gradient-text">' + esc(s.game.code) + '</div>' +
          '<div class="muted" data-live="players"></div></div>' +
        '</div>' +
        '<div class="actions">' +
          '<button class="btn btn--primary btn--large" type="button" data-act="start">' + esc(t('h_start')) + ' →</button>' +
          '<label class="check"><input type="checkbox" id="onlyClaimed"' + (S.onlyClaimed ? ' checked' : '') + '><span>' + esc(t('h_only_claimed')) + '</span></label>' +
        '</div>' +
        '<div class="muted" data-live="need"></div>' +
      '</section>' +
      '<section class="stack"><span class="tag">' + esc(t('h_cards')) + '</span>' + cardsGrid() + '</section>' +
    '</div>';
  }

  function nowBlock(rightHtml) {
    var cur = S.state.current || {};
    return '<div class="now">' +
      '<div class="cover-xl">' + KK.art(cur.cover, cur.title, 'x') + '</div>' +
      '<section class="stack-l">' +
        '<span class="tag">' + esc(t('track_of', { i: S.state.game.position + 1, n: S.state.game.total })) + '</span>' +
        '<h1 class="h-xl">' + esc(cur.title) + '</h1>' +
        rightHtml +
      '</section></div>';
  }

  function viewListen() {
    var cur = S.state.current || {};
    return nowBlock(
      '<div class="note' + (cur.has_owner ? ' note--mine' : '') + '">' + (cur.has_owner ? '⭐ ' + esc(t('h_owner')) : esc(t('h_free'))) + '</div>' +
      (cur.audio ? '<div class="progress"><i data-live="progress"></i></div>' : '<div class="note">' + esc(t('h_no_audio')) + '</div>') +
      '<div class="actions">' +
        (cur.audio ? '<button class="btn btn--outline" type="button" data-act="toggle">' + esc(t('h_pause')) + '</button>' : '') +
        '<button class="btn btn--primary" type="button" data-act="to_answer">' + esc(t('h_to_answer')) + ' →</button>' +
      '</div>'
    );
  }

  function viewAnswer() {
    return nowBlock(
      '<h2 class="h-l">' + esc(t('h_write_now')) + '</h2>' +
      '<div style="display:flex;gap:28px;align-items:center;flex-wrap:wrap">' +
        '<div class="timer gradient-text" data-timer></div>' +
        '<div class="stack"><div class="answered" data-live="dots"></div><div class="muted" data-live="answered"></div></div>' +
      '</div>' +
      '<div class="actions"><button class="btn btn--primary" type="button" data-act="summarize">' + esc(t('h_summarize')) + ' ✨</button></div>'
    );
  }

  function viewThinking() {
    return nowBlock('<div class="stack"><span class="dots"><i></i><i></i><i></i></span><h2 class="h-l">' + esc(t('thinking')) + '</h2></div>');
  }

  function viewSummary() {
    var g = S.state.game;
    var last = g.position + 1 >= g.total;
    return nowBlock(
      summaryHtml(S.state.summary, KK.lang) +
      '<div class="actions"><button class="btn btn--primary btn--large" type="button" data-act="next">' +
        esc(last ? t('h_finish') : t('h_next')) + ' →</button></div>'
    );
  }

  function summaryHtml(summary, lang) {
    if (!summary) { return ''; }
    var d = summary.data || {};
    if (summary.source === 'ai' && d[lang]) {
      return '<div class="summary-box stack">' +
        '<p class="summary-text">' + esc(d[lang].summary) + '</p>' +
        (d[lang].talk ? '<div class="talk"><b>' + esc(t('talk_label')) + '</b>' + esc(d[lang].talk) + '</div>' : '') +
        '<span class="ai-label">' + esc(t('ai_label')) + '</span></div>';
    }
    var quotes = (d.quotes || []).filter(Boolean);
    return '<div class="summary-box stack"><span class="tag">' + esc(t('fallback_title')) + '</span>' +
      (quotes.length ? quotes.map(function (q) { return '<p class="quote">«' + esc(q) + '»</p>'; }).join('')
        : '<p class="summary-text">' + esc(t('quiet')) + '</p>') + '</div>';
  }

  function viewFinished() {
    var l = S.state.links || {};
    return '<div class="stack-l center" style="padding-top:20px">' +
      '<h1 class="h-xl">' + esc(t('over_title')).replace(/(\S+)$/, '<span class="gradient-text">$1</span>') + '</h1>' +
      '<p class="muted" style="font-size:20px;margin:0">' + esc(t('h_over_sub')) + '</p>' +
      '<div class="qr-row">' +
        (l.music ? '<div class="qr-item"><div class="qr" id="qrMusic"></div>🎵 ' + esc(t('h_qr_music')) + '</div>' : '') +
        (l.site ? '<div class="qr-item"><div class="qr" id="qrSite"></div>' + esc(t('h_qr_site')) + '</div>' : '') +
      '</div>' +
      '<div><button class="btn btn--outline" type="button" data-act="new">' + esc(t('h_new_table')) + '</button></div>' +
    '</div>';
  }

  // ------------------------------------------------------------ events
  function bind() {
    var s = S.state;
    if (s.game.phase === 'lobby') { KK.qr(document.getElementById('qrJoin'), s.join_url); }
    if (s.game.phase === 'finished') {
      KK.qr(document.getElementById('qrMusic'), s.links.music);
      KK.qr(document.getElementById('qrSite'), s.links.site);
    }
    on('start', function () {
      var cb = document.getElementById('onlyClaimed');
      S.onlyClaimed = cb && cb.checked;
      unlockAudio();
      api('host_start', { only_claimed: S.onlyClaimed ? 1 : 0 });
    });
    var cb = document.getElementById('onlyClaimed');
    if (cb) { cb.onchange = function () { S.onlyClaimed = cb.checked; }; }
    on('to_answer', function () { fadeOut(); toAnswer(); });
    on('toggle', function (b) {
      if (S.audio.paused) { S.audio.play().catch(gate); b.textContent = t('h_pause'); }
      else { S.audio.pause(); b.textContent = t('h_play'); }
    });
    on('summarize', function () { summarize(true); });
    on('next', function () { unlockAudio(); api('host_next'); });
    on('new', function () { S.gameId = 0; S.state = null; S.key = ''; history.replaceState(null, '', 'host.php'); renderOpen(); });
  }

  function on(act, fn) {
    var b = app.querySelector('[data-act="' + act + '"]');
    if (b) { b.onclick = function () { fn(b); }; }
  }

  function toAnswer() {
    var pos = S.state.game.position;
    if (S.toAnswerSent === pos) { return; }
    S.toAnswerSent = pos;
    api('host_to_answer', { position: pos });
  }

  function summarize(force) {
    var pos = S.state.game.position;
    if (!force && S.sumSent === pos) { return; }
    S.sumSent = pos; S.sumSentAt = Date.now();
    api('host_summarize', { position: pos });
  }

  // ------------------------------------------------------------ live: timer, counters, auto-advance
  function live() {
    var s = S.state;
    if (!s) { return; }
    var g = s.game;
    var p = app.querySelector('[data-live="players"]');
    if (p) { p.textContent = t('at_table', { n: s.players.total }); }

    var startBtn = app.querySelector('[data-act="start"]');
    if (startBtn) {
      var enough = s.players.total >= s.min_players;
      startBtn.disabled = !enough;
      var need = app.querySelector('[data-live="need"]');
      if (need) { need.textContent = enough ? '' : t('h_need', { n: s.min_players }); }
    }

    var n = Math.max(s.players.active, 1);
    var a = s.answered || 0;
    var dots = app.querySelector('[data-live="dots"]');
    if (dots) {
      var h = '';
      for (var i = 0; i < Math.max(n, a); i++) { h += '<i class="' + (i < a ? 'on' : '') + '"></i>'; }
      dots.innerHTML = h;
    }
    var an = app.querySelector('[data-live="answered"]');
    if (an) { an.textContent = t('h_answers', { a: a, n: s.players.active }); }

    if (g.phase === 'answer' && s.players.active > 0 && a >= s.players.active) { summarize(false); }
  }

  setInterval(function () {
    var s = S.state;
    if (!s) { return; }
    var g = s.game;
    var now = Date.now() / 1000 + S.offset;
    if (g.phase === 'answer') {
      var left = Math.max(0, Math.round(g.answer_deadline - now));
      var el = app.querySelector('[data-timer]');
      if (el) { el.textContent = left; }
      if (left <= 0) { summarize(false); }
    }
    if (g.phase === 'summarizing' && S.sumSent === g.position && Date.now() - S.sumSentAt > 45000) {
      summarize(true); // retry a stuck AI call
    }
    if (g.phase === 'summarizing' && S.sumSent !== g.position && now - g.phase_started > 45) {
      summarize(true); // another tablet started it and vanished
    }
  }, 250);

  // ------------------------------------------------------------ audio
  var unlocked = false;
  function unlockAudio() {
    if (unlocked) { return; }
    unlocked = true;
    // A silent play inside a click lets the browser play the tracks later without a tap.
    try {
      if (S.audio.paused) {
        S.audio.src = silentWav();
        S.audio.play().catch(function () { /* ignore */ });
      }
    } catch (e) { /* ignore */ }
  }

  function silentWav() {
    // 0.2 s of silence, 8 kHz, 8-bit mono
    var n = 1600, buf = new Uint8Array(44 + n), v = new DataView(buf.buffer);
    function str(o, s) { for (var i = 0; i < s.length; i++) { buf[o + i] = s.charCodeAt(i); } }
    str(0, 'RIFF'); v.setUint32(4, 36 + n, true); str(8, 'WAVE'); str(12, 'fmt ');
    v.setUint32(16, 16, true); v.setUint16(20, 1, true); v.setUint16(22, 1, true);
    v.setUint32(24, 8000, true); v.setUint32(28, 8000, true); v.setUint16(32, 1, true); v.setUint16(34, 8, true);
    str(36, 'data'); v.setUint32(40, n, true);
    for (var i = 44; i < buf.length; i++) { buf[i] = 128; }
    var bin = ''; for (var k = 0; k < buf.length; k++) { bin += String.fromCharCode(buf[k]); }
    return 'data:audio/wav;base64,' + btoa(bin);
  }

  function gate() {
    if (document.querySelector('.sound-gate')) { return; }
    var d = document.createElement('div');
    d.className = 'sound-gate';
    d.innerHTML = '<div style="font-size:64px">🔊</div><button class="btn btn--primary btn--large" type="button">' + esc(t('h_sound')) + '</button>';
    document.body.appendChild(d);
    d.onclick = function () { d.remove(); S.audio.play().catch(gate); };
  }

  function audioControl() {
    var s = S.state, g = s.game, cur = s.current;
    if (g.phase === 'listen' && cur && cur.audio && S.audioPos !== g.position) {
      S.audioPos = g.position;
      S.audioStart = cur.start_sec || 0;
      var a = S.audio;
      a.volume = 1;
      a.src = cur.audio;
      a.onloadedmetadata = function () {
        if (S.audioStart > 0 && S.audioStart < a.duration - 5) { a.currentTime = S.audioStart; }
      };
      a.play().catch(gate);
    }
    if (g.phase !== 'listen' && !S.audio.paused && S.audioPos !== null) { fadeOut(); }
  }

  S.audio.addEventListener('timeupdate', function () {
    var s = S.state;
    if (!s || s.game.phase !== 'listen' || S.audioPos !== s.game.position) { return; }
    var a = S.audio, lim = s.game.listen_seconds;
    var total = Math.min(lim, (a.duration || lim) - S.audioStart);
    var played = Math.max(0, a.currentTime - S.audioStart);
    var bar = app.querySelector('[data-live="progress"]');
    if (bar && total > 0) { bar.style.width = Math.min(100, played / total * 100) + '%'; }
    if (played >= lim) { fadeOut(); toAnswer(); }
  });
  S.audio.addEventListener('ended', function () {
    var s = S.state;
    if (s && s.game.phase === 'listen' && S.audioPos === s.game.position) { toAnswer(); }
  });

  var fading = null;
  function fadeOut() {
    var a = S.audio;
    if (a.paused || fading) { return; }
    fading = setInterval(function () {
      if (a.volume > 0.08) { a.volume = Math.max(0, a.volume - 0.08); }
      else { clearInterval(fading); fading = null; a.pause(); a.volume = 1; }
    }, 80);
  }

  poll();
})();
