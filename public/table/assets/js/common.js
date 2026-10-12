/* Shared helpers for player and host screens */
(function () {
  'use strict';

  var KK = window.KK = {};

  KK.lang = 'en';

  KK.t = function (key, vars) {
    var dict = window.KK_I18N[KK.lang] || window.KK_I18N.en;
    var s = dict[key] != null ? dict[key] : (window.KK_I18N.en[key] != null ? window.KK_I18N.en[key] : key);
    if (vars) {
      Object.keys(vars).forEach(function (k) { s = s.split('{' + k + '}').join(vars[k]); });
    }
    return s;
  };

  KK.esc = function (s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  };

  KK.store = {
    get: function (k) { try { return window.localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { window.localStorage.setItem(k, v); } catch (e) { /* private mode */ } }
  };

  KK.api = function (action, body, extraHeaders) {
    var headers = { 'Content-Type': 'application/json' };
    if (extraHeaders) { Object.keys(extraHeaders).forEach(function (k) { headers[k] = extraHeaders[k]; }); }
    return fetch('api.php?a=' + encodeURIComponent(action), {
      method: 'POST',
      credentials: 'same-origin',
      headers: headers,
      body: JSON.stringify(body || {})
    }).then(function (r) {
      return r.json().catch(function () { return { ok: false, error: 'bad_json' }; }).then(function (j) {
        j._status = r.status;
        return j;
      });
    });
  };

  /** Cover image or a gradient placeholder with the first letter of the title. */
  KK.art = function (cover, title, cls) {
    if (cover) {
      return '<img class="' + (cls || 'tcard__art') + '" src="' + KK.esc(cover) + '" alt="" loading="lazy">';
    }
    var letter = (String(title || '?').trim()[0] || '?').toUpperCase();
    return '<div class="tcard__ph">' + KK.esc(letter) + '</div>';
  };

  KK.qr = function (el, text) {
    if (!el || !window.qrcode) { return; }
    var q = window.qrcode(0, 'M');
    q.addData(text);
    q.make();
    el.innerHTML = q.createSvgTag({ cellSize: 6, margin: 2, scalable: true });
  };

  var toastTimer;
  KK.toast = function (msg) {
    var el = document.getElementById('toast');
    if (!el) { return; }
    el.textContent = msg;
    el.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { el.classList.remove('show'); }, 2600);
  };

  KK.langSwitch = function (el, current, onPick) {
    var names = { en: 'EN', ru: 'RU', el: 'ΕΛ' };
    el.innerHTML = ['en', 'ru', 'el'].map(function (l) {
      return '<button type="button" data-l="' + l + '" class="' + (l === current ? 'on' : '') + '">' + names[l] + '</button>';
    }).join('');
    el.onclick = function (e) {
      var b = e.target.closest('button[data-l]');
      if (b) { onPick(b.getAttribute('data-l')); }
    };
  };

  KK.detectLang = function () {
    var saved = KK.store.get('kk_lang');
    if (saved && window.KK_I18N[saved]) { return saved; }
    var n = (navigator.language || 'en').slice(0, 2).toLowerCase();
    return n === 'ru' ? 'ru' : (n === 'el' ? 'el' : 'en');
  };
})();
