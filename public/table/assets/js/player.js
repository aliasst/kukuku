/* Player (phone) screen */
(function () {
  'use strict';
  var KK = window.KK, t = KK.t, esc = KK.esc;
  var app = document.getElementById('app');
  var langsEl = document.getElementById('langs');

  var S = {
    mode: 'loading',     // loading | join | game
    state: null,
    key: '',
    offset: 0,           // server time - local time, seconds
    picking: false,      // user pressed "change card"
    draft: '',
    busy: false,
    pollTimer: null,
    failures: 0
  };

  KK.lang = KK.detectLang();
  document.documentElement.lang = KK.lang;
  drawLangs();

  function drawLangs() {
    KK.langSwitch(langsEl, KK.lang, function (l) {
      KK.lang = l;
      document.documentElement.lang = l;
      KK.store.set('kk_lang', l);
      drawLangs();
      if (S.mode === 'game') {
        KK.api('lang', { lang: l });
        if (S.state) { S.state.me.lang = l; }
        S.key = '';
        render();
      } else if (S.mode === 'join') {
        renderJoin();
      }
    });
  }

  // ------------------------------------------------------------ polling
  function poll() {
    clearTimeout(S.pollTimer);
    KK.api('state').then(function (j) {
      S.failures = 0;
      if (j._status === 401 || j.error === 'no_player' || j.error === 'no_game') {
        if (S.mode !== 'join') { renderJoin(); }
        return; // stop polling until the guest joins
      }
      if (!j.ok) { schedule(2500); return; }
      S.mode = 'game';
      S.state = j;
      S.offset = j.now - Date.now() / 1000;
      if (j.me && j.me.lang && j.me.lang !== KK.lang) {
        KK.lang = j.me.lang;
        document.documentElement.lang = KK.lang;
        drawLangs();
      }
      render();
      schedule(1500);
    }).catch(function () {
      S.failures++;
      if (S.failures === 3) { KK.toast(t('offline')); }
      schedule(3000);
    });
  }
  function schedule(ms) { clearTimeout(S.pollTimer); S.pollTimer = setTimeout(poll, ms); }

  // ------------------------------------------------------------ join
  function renderJoin() {
    S.mode = 'join';
    var code = app.getAttribute('data-code') || '';
    app.innerHTML =
      '<div class="stack-l" style="padding-top:20px">' +
        '<div class="stack">' +
          '<span class="badge badge--live"><span class="badge__dot"></span>KuKu\'Ku Coffee</span>' +
          '<h1 class="h-l">' + esc(t('join_title')).replace(/(\S+)$/, '<span class="gradient-text">$1</span>') + '</h1>' +
          '<p class="muted" style="margin:0">' + esc(t('join_sub')) + '</p>' +
        '</div>' +
        '<form class="panel stack" id="joinForm" autocomplete="off">' +
          '<input class="input code-input" id="code" inputmode="numeric" pattern="[0-9]*" maxlength="4" placeholder="••••" value="' + esc(code) + '" aria-label="Code">' +
          '<div class="stack" style="margin-top:18px"><span class="muted" style="font-size:14px">' + esc(t('lang_label')) + '</span>' +
          '<div class="langs" id="langs2" style="display:flex;width:max-content"></div></div>' +
          '<div class="error" id="err"></div>' +
          '<button class="btn btn--primary btn--block" type="submit">' + esc(t('join_btn')) + '</button>' +
        '</form>' +
      '</div>';

    KK.langSwitch(document.getElementById('langs2'), KK.lang, function (l) {
      KK.lang = l; KK.store.set('kk_lang', l); document.documentElement.lang = l;
      var c = document.getElementById('code').value;
      app.setAttribute('data-code', c);
      drawLangs(); renderJoin();
    });

    var codeEl = document.getElementById('code');
    if (!code) { codeEl.focus(); }
    codeEl.addEventListener('input', function () { codeEl.value = codeEl.value.replace(/\D/g, '').slice(0, 4); });

    document.getElementById('joinForm').addEventListener('submit', function (e) {
      e.preventDefault();
      var err = document.getElementById('err');
      var c = codeEl.value.trim();
      if (c.length !== 4) { err.textContent = t('err_bad_code'); return; }
      if (S.busy) { return; }
      S.busy = true;
      KK.api('join', { code: c, lang: KK.lang }).then(function (j) {
        S.busy = false;
        if (!j.ok) { err.textContent = t('err_' + j.error) !== 'err_' + j.error ? t('err_' + j.error) : t('err_generic'); return; }
        KK.store.set('kk_lang', KK.lang);
        try { history.replaceState(null, '', './'); } catch (x) { /* ignore */ }
        S.key = '';
        poll();
      }).catch(function () { S.busy = false; err.textContent = t('err_generic'); });
    });
  }

  // ------------------------------------------------------------ game
  function render() {
    var s = S.state, g = s.game, me = s.me;
    var anyFree = s.cards.some(function (c) { return !c.taken; });
    var needCard = !me.card && g.phase !== 'finished' && anyFree;
    var picking = (g.phase === 'lobby' && S.picking) || needCard;

    var key = [KK.lang, g.phase, g.position, me.card ? me.card.track_id : 0, me.answered, me.lead_done,
      picking ? s.cards.map(function (c) { return c.taken ? 1 : 0; }).join('') : '',
      s.summary ? 1 : 0].join('|');

    if (key === S.key) { live(); return; }
    S.key = key;

    var html = '';
    if (picking) { html = viewPick(); }
    else if (g.phase === 'lobby') { html = viewLobby(); }
    else if (g.phase === 'listen') { html = viewListen(); }
    else if (g.phase === 'answer') { html = me.answered ? viewSent() : viewAnswer(); }
    else if (g.phase === 'summarizing') { html = viewThinking(); }
    else if (g.phase === 'summary') { html = viewSummary(); }
    else if (g.phase === 'finished') { html = me.lead_done ? viewThanks() : viewForm(); }

    app.innerHTML = html;
    bind();
    live();
  }

  function trackTag() {
    var g = S.state.game;
    return '<span class="tag">' + esc(t('track_of', { i: g.position + 1, n: g.total })) + '</span>';
  }

  function viewPick() {
    var s = S.state, me = s.me;
    var cards = s.cards.map(function (c) {
      var mine = me.card && me.card.track_id === c.track_id;
      var taken = c.taken && !mine;
      return '<button type="button" class="tcard' + (taken ? ' is-taken' : '') + (mine ? ' is-mine' : '') + '"' +
        ' data-card="' + c.track_id + '" data-taken="' + esc(t('taken')) + '"' + (taken ? ' disabled' : '') + '>' +
        KK.art(c.cover, c.title) + '<div class="tcard__title">' + esc(c.title) + '</div></button>';
    }).join('');
    return '<div class="stack-l">' +
      '<div class="stack"><h1 class="h-l">' + esc(t('pick_title')) + '</h1>' +
      '<p class="muted" style="margin:0">' + esc(t('pick_sub')) + '</p></div>' +
      '<div class="cards">' + cards + '</div></div>';
  }

  function viewLobby() {
    var c = S.state.me.card;
    return '<div class="stack-l" style="padding-top:8px">' +
      (c ? '<div class="panel stack">' +
        '<span class="tag">' + esc(t('your_card')) + '</span>' +
        '<div class="mycard"><div class="mycard__art">' + KK.art(c.cover, c.title, 'x') + '</div>' +
        '<div class="h-m">' + esc(c.title) + '</div></div>' +
        '<button class="btn btn--ghost" type="button" data-act="change" style="padding-left:0">' + esc(t('change_card')) + '</button>' +
      '</div>' : '') +
      '<div class="center stack" style="padding-top:12px">' +
        '<span class="dots"><i></i><i></i><i></i></span>' +
        '<p class="h-m" style="margin:0">' + esc(t('waiting_start')) + '</p>' +
        '<p class="muted" style="margin:0" data-live="players"></p>' +
      '</div></div>';
  }

  function viewListen() {
    var cur = S.state.current || {};
    return '<div class="stack-l center" style="padding-top:8px">' +
      trackTag() +
      '<div class="cover-xl">' + KK.art(cur.cover, cur.title, 'x') + '</div>' +
      '<div class="stack"><h1 class="h-l">' + esc(cur.title) + '</h1>' +
      '<p class="muted" style="margin:0">🎧 ' + esc(t('listen_sub')) + '</p></div>' +
      (cur.is_mine ? '<div class="note note--mine">⭐ ' + esc(t('your_track')) + '</div>' : '') +
      '</div>';
  }

  function viewAnswer() {
    var cur = S.state.current || {};
    return '<div class="stack-l" style="padding-top:8px">' +
      '<div class="mycard"><div class="mycard__art" style="width:64px;height:64px;border-radius:14px">' + KK.art(cur.cover, cur.title, 'x') + '</div>' +
      '<div>' + trackTag() + '<div class="h-m">' + esc(cur.title) + '</div></div></div>' +
      (cur.is_mine ? '<div class="note note--mine">⭐ ' + esc(t('your_track')) + '</div>' : '') +
      '<form class="panel stack" id="answerForm">' +
        '<div style="display:flex;justify-content:space-between;align-items:baseline;gap:10px">' +
          '<h2 class="h-m">' + esc(t('answer_title')) + '</h2><b class="gradient-text" data-timer style="font-size:22px"></b></div>' +
        '<textarea class="textarea" id="answer" maxlength="200" placeholder="' + esc(t('answer_ph')) + '">' + esc(S.draft) + '</textarea>' +
        '<div class="counter"><span id="cnt">' + S.draft.length + '</span>/200</div>' +
        '<button class="btn btn--primary btn--block" type="submit">' + esc(t('send')) + '</button>' +
        '<button class="btn btn--ghost btn--block" type="button" data-act="skip">' + esc(t('skip')) + '</button>' +
        '<p class="muted center" style="font-size:13px;margin:4px 0 0">🔒 ' + esc(t('anonymous_note')) + '</p>' +
      '</form></div>';
  }

  function viewSent() {
    return '<div class="center stack-l" style="padding-top:50px">' +
      '<div style="font-size:56px">✅</div>' +
      '<h2 class="h-m">' + esc(t('sent')) + '</h2>' +
      '<span class="dots"><i></i><i></i><i></i></span>' +
      '<p class="muted" data-live="answered"></p></div>';
  }

  function viewThinking() {
    return '<div class="center stack-l" style="padding-top:60px">' +
      '<div style="font-size:56px">✨</div>' +
      '<h2 class="h-m">' + esc(t('thinking')) + '</h2>' +
      '<span class="dots"><i></i><i></i><i></i></span></div>';
  }

  function summaryHtml(summary, lang) {
    if (!summary) { return ''; }
    var d = summary.data || {};
    if (summary.source === 'ai' && d[lang]) {
      return '<div class="summary-box stack">' +
        '<span class="tag">' + esc(t('summary_title')) + '</span>' +
        '<p class="summary-text">' + esc(d[lang].summary) + '</p>' +
        (d[lang].talk ? '<div class="talk"><b>' + esc(t('talk_label')) + '</b>' + esc(d[lang].talk) + '</div>' : '') +
        '<span class="ai-label">' + esc(t('ai_label')) + '</span></div>';
    }
    var quotes = (d.quotes || []).filter(Boolean);
    return '<div class="summary-box stack">' +
      '<span class="tag">' + esc(t('fallback_title')) + '</span>' +
      (quotes.length ? quotes.map(function (q) { return '<p class="quote">«' + esc(q) + '»</p>'; }).join('')
        : '<p class="summary-text">' + esc(t('quiet')) + '</p>') +
      '</div>';
  }
  KK.summaryHtml = summaryHtml;

  function viewSummary() {
    var cur = S.state.current || {};
    return '<div class="stack-l" style="padding-top:8px">' +
      '<div class="mycard"><div class="mycard__art" style="width:64px;height:64px;border-radius:14px">' + KK.art(cur.cover, cur.title, 'x') + '</div>' +
      '<div>' + trackTag() + '<div class="h-m">' + esc(cur.title) + '</div></div></div>' +
      summaryHtml(S.state.summary, KK.lang) +
      '<p class="muted center">' + esc(t('next_soon')) + '</p></div>';
  }

  function viewForm() {
    return '<div class="stack-l" style="padding-top:8px">' +
      '<div class="stack"><h1 class="h-l">' + esc(t('over_title')) + '</h1>' +
      '<p class="muted" style="margin:0">' + esc(t('over_sub')) + '</p></div>' +
      '<form class="panel stack" id="leadForm" autocomplete="on">' +
        field('name', t('f_name'), 'text', 'name') +
        field('phone', t('f_phone'), 'tel', 'tel') +
        field('email', t('f_email'), 'email', 'email') +
        field('instagram', t('f_insta'), 'text', 'off', '@') +
        '<label class="check"><input type="checkbox" name="consent" value="1"><span>' + esc(t('f_consent')) + '</span></label>' +
        '<div class="error" id="err"></div>' +
        '<button class="btn btn--primary btn--block" type="submit">' + esc(t('f_send')) + '</button>' +
      '</form>' + linksHtml() + '</div>';
  }

  function field(name, label, type, ac, ph) {
    return '<label class="field"><span>' + esc(label) + '</span>' +
      '<input class="input" name="' + name + '" type="' + type + '" autocomplete="' + ac + '"' +
      (ph ? ' placeholder="' + esc(ph) + '"' : '') + ' maxlength="120"></label>';
  }

  function linksHtml() {
    var l = S.state.links || {};
    return '<div class="links">' +
      (l.music ? '<a class="btn btn--primary btn--block" href="' + esc(l.music) + '" target="_blank" rel="noopener">🎵 ' + esc(t('listen_music')) + '</a>' : '') +
      (l.site ? '<a class="btn btn--outline btn--block" href="' + esc(l.site) + '" target="_blank" rel="noopener">' + esc(t('open_site')) + '</a>' : '') +
      '</div>';
  }

  function viewThanks() {
    return '<div class="stack-l center" style="padding-top:30px">' +
      '<div style="font-size:56px">🧡</div>' +
      '<h1 class="h-l">' + esc(t('thanks')) + '</h1>' +
      linksHtml() +
      '<button class="btn btn--ghost" type="button" data-act="leave">' + esc(t('play_again')) + '</button></div>';
  }

  // ------------------------------------------------------------ events
  function bind() {
    app.querySelectorAll('[data-card]').forEach(function (b) {
      b.addEventListener('click', function () {
        if (S.busy) { return; }
        S.busy = true;
        KK.api('claim', { track_id: +b.getAttribute('data-card') }).then(function (j) {
          S.busy = false;
          if (!j.ok) { KK.toast(j.error === 'card_taken' ? t('err_card_taken') : t('err_generic')); }
          S.picking = false; S.key = ''; poll();
        }).catch(function () { S.busy = false; KK.toast(t('err_generic')); });
      });
    });

    var change = app.querySelector('[data-act="change"]');
    if (change) { change.addEventListener('click', function () { S.picking = true; S.key = ''; render(); }); }

    var ans = document.getElementById('answer');
    if (ans) {
      ans.addEventListener('input', function () {
        S.draft = ans.value;
        document.getElementById('cnt').textContent = ans.value.length;
      });
      document.getElementById('answerForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var text = ans.value.trim();
        if (!text) { ans.focus(); return; }
        sendAnswer({ text: text });
      });
      app.querySelector('[data-act="skip"]').addEventListener('click', function () { sendAnswer({ skip: 1 }); });
    }

    var lead = document.getElementById('leadForm');
    if (lead) {
      lead.addEventListener('submit', function (e) {
        e.preventDefault();
        var f = lead.elements, err = document.getElementById('err');
        var data = {
          name: f.name.value, phone: f.phone.value, email: f.email.value,
          instagram: f.instagram.value, consent: f.consent.checked ? 1 : 0
        };
        if (!data.name.trim()) { err.textContent = t('err_need_name'); return; }
        if (!data.phone.trim() && !data.email.trim() && !data.instagram.trim()) { err.textContent = t('err_need_contact'); return; }
        if (!data.consent) { err.textContent = t('err_need_consent'); return; }
        if (S.busy) { return; }
        S.busy = true;
        KK.api('lead', data).then(function (j) {
          S.busy = false;
          if (!j.ok) { err.textContent = t('err_' + j.error) !== 'err_' + j.error ? t('err_' + j.error) : t('err_generic'); return; }
          S.key = ''; poll();
        }).catch(function () { S.busy = false; err.textContent = t('err_generic'); });
      });
    }

    var leave = app.querySelector('[data-act="leave"]');
    if (leave) {
      leave.addEventListener('click', function () {
        KK.api('leave').then(function () { S.state = null; renderJoin(); });
      });
    }
  }

  function sendAnswer(payload) {
    if (S.busy) { return; }
    S.busy = true;
    KK.api('answer', payload).then(function (j) {
      S.busy = false;
      if (j.ok || j.error === 'already') { S.draft = ''; }
      else if (j.error !== 'not_now') { KK.toast(t('err_generic')); }
      S.key = ''; poll();
    }).catch(function () { S.busy = false; KK.toast(t('err_generic')); });
  }

  // ------------------------------------------------------------ live bits (no re-render)
  function live() {
    var s = S.state;
    if (!s) { return; }
    var p = app.querySelector('[data-live="players"]');
    if (p) { p.textContent = t('at_table', { n: s.players.total }); }
    var a = app.querySelector('[data-live="answered"]');
    if (a) { a.textContent = t('h_answers', { a: s.answered || 0, n: s.players.active || s.players.total }); }
    tickTimer();
  }

  function tickTimer() {
    var el = app.querySelector('[data-timer]');
    if (!el || !S.state) { return; }
    var left = Math.max(0, Math.round(S.state.game.answer_deadline - (Date.now() / 1000 + S.offset)));
    el.textContent = left + 's';
  }
  setInterval(tickTimer, 500);

  // Reset the draft when a new track starts
  var lastPos = null;
  setInterval(function () {
    if (S.state && S.state.game.position !== lastPos) { lastPos = S.state.game.position; S.draft = ''; }
  }, 300);

  poll();
})();
