/* DWP Arabia — site behaviour. No dependencies. */
(function () {
  'use strict';
  var d = document, w = window, cfg = w.DWP || {};
  d.documentElement.classList.add('js');

  /* ---------- Storage helpers (may throw in private mode) ---------- */
  function get(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
  function set(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }
  function sget(k) { try { return sessionStorage.getItem(k); } catch (e) { return null; } }
  function sset(k, v) { try { sessionStorage.setItem(k, v); } catch (e) {} }

  /* ---------- dataLayer events (queued; GTM reads them once consent is given) ---------- */
  function track(event, extra) {
    var p = { event: event, page_language: cfg.lang, page_path: location.pathname };
    for (var k in extra || {}) p[k] = extra[k];
    w.dataLayer.push(p);
  }
  w.dwpTrack = track;

  d.addEventListener('click', function (e) {
    var a = e.target.closest('a');
    if (!a) return;
    var ev = a.getAttribute('data-track');
    var href = a.getAttribute('href') || '';
    if (!ev) {
      if (href.indexOf('tel:') === 0) ev = 'phone_click';
      else if (href.indexOf('mailto:') === 0) ev = 'email_click';
      else if (href.indexOf('wa.me') !== -1) ev = 'whatsapp_click';
    }
    if (ev) track(ev, { link_url: href });
  });

  // Events declared by the page (thank-you, assessment result).
  d.querySelectorAll('[data-events]').forEach(function (el) {
    var key = 'dwp_ev_' + location.pathname + location.search;
    if (sget(key)) return; // don't double-count on reload
    sset(key, '1');
    (el.getAttribute('data-events') || '').split(',').filter(Boolean).forEach(function (ev) {
      track(ev.trim(), { interest: el.getAttribute('data-interest') || undefined });
    });
  });

  /* ---------- UTM / click-ID capture (first touch, this session) ---------- */
  var params = new URLSearchParams(location.search);
  var keys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'li_fat_id', 'fbclid'];
  var stored = {};
  try { stored = JSON.parse(sget('dwp_attr') || '{}'); } catch (e) {}
  var changed = false;
  keys.forEach(function (k) { if (params.get(k)) { stored[k] = params.get(k); changed = true; } });
  if (!stored.landing_page) { stored.landing_page = location.href; changed = true; }
  if (changed) sset('dwp_attr', JSON.stringify(stored));
  d.querySelectorAll('[data-track-field]').forEach(function (inp) {
    var k = inp.getAttribute('data-track-field');
    if (stored[k]) inp.value = stored[k];
  });

  /* ---------- Navigation: mega menu (desktop hover/click/keyboard) + drawer (mobile) ---------- */
  var toggle = d.querySelector('[data-nav-toggle]');
  var nav = d.querySelector('[data-nav]');
  var header = d.querySelector('[data-header]');
  var desktop = w.matchMedia('(min-width: 1100px)');
  var items = Array.prototype.slice.call(d.querySelectorAll('[data-menu-item]'));
  var openTimer, closeTimer;
  function setOpen(li, open) {
    li.classList.toggle('is-open', open);
    li.querySelector('[data-sub-toggle]').setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  function closeAll(except) { items.forEach(function (o) { if (o !== except && o.classList.contains('is-open')) setOpen(o, false); }); }
  function openOnly(li) { closeAll(li); setOpen(li, true); }
  function setDrawer(open) {
    if (!nav || !toggle) return;
    nav.classList.toggle('is-open', open);
    header && header.classList.toggle('nav-is-open', open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    d.body.style.overflow = open ? 'hidden' : '';
    if (!open) closeAll();
  }
  if (toggle) toggle.addEventListener('click', function () { setDrawer(!nav.classList.contains('is-open')); });
  desktop.addEventListener && desktop.addEventListener('change', function () { setDrawer(false); });

  items.forEach(function (li) {
    var btn = li.querySelector('[data-sub-toggle]');
    btn.addEventListener('click', function () {
      // A click straight after hover-open should not close the panel the pointer just opened
      if (li.classList.contains('is-open') && Date.now() - (li._hoverOpenedAt || 0) < 600) return;
      var open = !li.classList.contains('is-open');
      if (open) openOnly(li); else setOpen(li, false);
    });
    // Hover intent (mouse only): small delays stop panels flickering while the pointer crosses the bar
    li.addEventListener('pointerenter', function (e) {
      if (e.pointerType !== 'mouse' || !desktop.matches) return;
      clearTimeout(closeTimer); clearTimeout(openTimer);
      var anyOpen = items.some(function (o) { return o.classList.contains('is-open'); });
      openTimer = setTimeout(function () { if (!li.classList.contains('is-open')) li._hoverOpenedAt = Date.now(); openOnly(li); }, anyOpen ? 0 : 90);
    });
    li.addEventListener('pointerleave', function (e) {
      if (e.pointerType !== 'mouse' || !desktop.matches) return;
      clearTimeout(openTimer);
      closeTimer = setTimeout(function () { setOpen(li, false); }, 160);
    });
    // Arrow down from the button moves into the panel
    btn.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowDown') return;
      e.preventDefault(); openOnly(li);
      var first = li.querySelector('.mega a'); first && first.focus();
    });
  });
  d.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    var openLi = items.filter(function (o) { return o.classList.contains('is-open'); })[0];
    if (openLi) { setOpen(openLi, false); openLi.querySelector('[data-sub-toggle]').focus(); }
    else if (nav && nav.classList.contains('is-open')) { setDrawer(false); toggle.focus(); }
  });
  d.addEventListener('click', function (e) { if (desktop.matches && !e.target.closest('[data-menu-item]')) closeAll(); });
  // Keyboard: leaving a panel with Tab closes it
  d.addEventListener('focusin', function (e) { if (desktop.matches && !e.target.closest('[data-menu-item]')) closeAll(); });

  /* ---------- Cookie consent (PDPL) → load GTM only after "Accept" ---------- */
  var banner = d.querySelector('[data-cookie-banner]');
  function loadGTM() {
    if (!cfg.gtm || w.__gtmLoaded) return;
    w.__gtmLoaded = true;
    w.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
    var s = d.createElement('script');
    s.async = true;
    s.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(cfg.gtm);
    d.head.appendChild(s);
  }
  var consent = get('dwp_consent_v2');
  if (consent === 'accepted') loadGTM();
  d.querySelectorAll('[data-cookie]').forEach(function (b) {
    b.addEventListener('click', function () {
      var v = b.getAttribute('data-cookie') === 'accept' ? 'accepted' : 'rejected';
      set('dwp_consent_v2', v);
      document.documentElement.setAttribute('data-consent', v);
      banner.hidden = true;
      if (v === 'accepted') loadGTM();
    });
  });
  d.querySelectorAll('[data-cookie-settings]').forEach(function (b) {
    b.addEventListener('click', function () { if (banner) { document.documentElement.removeAttribute('data-consent'); banner.hidden = false; } });
  });

  /* ---------- Forms: light client-side validation (server validates too) ---------- */
  d.querySelectorAll('[data-lead-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var bad = null;
      form.querySelectorAll('[required]').forEach(function (f) {
        var ok = f.type === 'checkbox' ? f.checked : (f.type === 'file' ? f.files.length > 0 : f.value.trim() !== '');
        if (ok && f.type === 'email') ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.value.trim());
        if (ok && f.type === 'file' && f.dataset.maxSize) ok = f.files[0].size <= +f.dataset.maxSize && /\.pdf$/i.test(f.files[0].name);
        f.setAttribute('aria-invalid', ok ? 'false' : 'true');
        if (!ok && !bad) bad = f;
      });
      if (bad) { e.preventDefault(); bad.focus(); return; }
      var btn = form.querySelector('[type="submit"]');
      if (btn) { btn.disabled = true; btn.setAttribute('aria-busy', 'true'); }
    });
  });

  /* ---------- Free ERP Assessment stepper ---------- */
  var af = d.querySelector('[data-assessment]');
  if (af) {
    var steps = [].slice.call(af.querySelectorAll('.a-step'));
    var bar = af.querySelector('[data-progress-bar]');
    var label = af.querySelector('[data-step-label]');
    var prev = af.querySelector('[data-prev]'), next = af.querySelector('[data-next]'), submit = af.querySelector('[data-submit]');
    var stepTpl = cfg.lang === 'ar' ? 'الخطوة %1 من %2' : 'Step %1 of %2';
    var firstErr = af.querySelector('[aria-invalid="true"], .field-error');
    var cur = 0;
    if (firstErr) { var s = firstErr.closest('.a-step'); cur = Math.max(0, steps.indexOf(s)); }
    function answered(i) {
      var st = steps[i];
      var req = st.querySelectorAll('input[required]');
      if (!req.length) return true;
      if (req[0].type === 'radio') return !!st.querySelector('input:checked');
      return [].every.call(req, function (f) { return f.type === 'checkbox' ? f.checked : f.value.trim() !== ''; });
    }
    function show(i) {
      cur = i;
      steps.forEach(function (s, n) { s.classList.toggle('is-current', n === i); });
      bar.style.width = Math.round((i + 1) / steps.length * 100) + '%';
      label.textContent = stepTpl.replace('%1', i + 1).replace('%2', steps.length);
      prev.hidden = i === 0;
      next.hidden = i === steps.length - 1;
      submit.hidden = i !== steps.length - 1;
      var focusable = steps[i].querySelector('legend');
      if (focusable && i > 0) { focusable.setAttribute('tabindex', '-1'); focusable.focus({ preventScroll: true }); }
    }
    next.addEventListener('click', function () { if (answered(cur)) show(cur + 1); else steps[cur].querySelector('input').focus(); });
    prev.addEventListener('click', function () { show(cur - 1); });
    af.addEventListener('change', function (e) {
      // Auto-advance on single-choice questions.
      if (e.target.type === 'radio' && cur < steps.length - 1) setTimeout(function () { show(cur + 1); }, 180);
    });
    af.addEventListener('submit', function (e) {
      for (var i = 0; i < steps.length; i++) { if (!answered(i)) { e.preventDefault(); show(i); return; } }
      submit.disabled = true;
    });
    show(cur);
  }

  /* ---------- Map: load the iframe on demand ---------- */
  d.querySelectorAll('[data-map-load]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var box = btn.closest('[data-map-src]');
      var f = d.createElement('iframe');
      f.src = box.getAttribute('data-map-src');
      f.loading = 'lazy';
      f.referrerPolicy = 'no-referrer-when-downgrade';
      f.title = cfg.lang === 'ar' ? 'موقع مكتب DWP Arabia على الخريطة' : 'DWP Arabia office location on Google Maps';
      box.innerHTML = '';
      box.appendChild(f);
      box.classList.add('is-loaded');
    });
  });
})();

/* Hero aurora video: desktop only, respects reduced motion and data saver (spec A4: no autoplay hero video on mobile). */
(function () {
  var v = document.querySelector('[data-hero-video]');
  if (!v) return;
  var conn = navigator.connection || {};
  if (!window.matchMedia('(min-width: 768px)').matches || window.matchMedia('(prefers-reduced-motion: reduce)').matches || conn.saveData) return;
  // Large or high-density screens get the 2560px version when it exists
  var hd = window.innerWidth * (window.devicePixelRatio || 1) >= 2200 && !conn.saveData && !/^(slow-)?2g|3g$/.test(conn.effectiveType || '');
  [['webm', 'video/webm'], ['mp4', 'video/mp4']].forEach(function (s) {
    var url = (hd && v.getAttribute('data-' + s[0] + '-hd')) || v.getAttribute('data-' + s[0]);
    if (!url) return;
    var src = document.createElement('source');
    src.src = url;
    src.type = s[1];
    v.appendChild(src);
  });
  v.load();
  v.addEventListener('playing', function () { v.classList.add('is-playing'); });
  var p = v.play();
  if (p && p.catch) p.catch(function () {});
})();

/* Brand film: load and play only when the visitor clicks. */
document.querySelectorAll('[data-film]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var v = document.createElement('video');
    v.src = btn.getAttribute('data-film');
    v.controls = true;
    v.autoplay = true;
    v.playsInline = true;
    btn.replaceWith(v);
    v.className = 'film';
    v.focus();
  });
});

/* Header: transparent over the hero, solid glass after scrolling */
(function () {
  var h = document.querySelector('[data-header]');
  if (!h) return;
  var onScroll = function () { h.classList.toggle('is-scrolled', window.scrollY > 24); };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
})();

/* Scroll reveal (skipped for reduced motion; content is visible without JS) */
(function () {
  if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var stagger = '.cards > li, .svc-grid > li, .steps > li, .logo-grid > li, .stats > li, .checklist > li, .faq details, .sap-pillars > li, .partner-band-logos > li, .ft-col, .sap-partner-copy > :not(ul)';
  var sel = '.section-head, .cta-box, .prose, .form-intro, .form-card, .table-wrap, .closing, .film, .assessment, .partner-band-label, .ft-cta, .ft-brand, ' + stagger;
  var media = '.split-media, .sap-partner-media .framed, .cards-aside-media';
  document.documentElement.classList.add('js-reveal');
  var els = [];
  document.querySelectorAll(sel + ', ' + media).forEach(function (el) {
    if (el.closest('.hero') || el.classList.contains('reveal')) return;
    if (el.matches(media)) { el.classList.add('reveal-media'); els.push(el); return; }
    el.classList.add('reveal');
    if (el.matches(stagger)) {
      var i = Array.prototype.indexOf.call(el.parentElement.children, el);
      el.style.setProperty('--d', Math.min(i, 8) * 0.08 + 's');
    }
    if (el.matches('.form-intro')) el.classList.add('from-start');
    els.push(el);
  });
  // Once an element has finished entering, drop the reveal classes so hover effects respond instantly.
  function settle(el) {
    setTimeout(function () { el.classList.remove('reveal', 'reveal-media', 'is-visible', 'from-start'); el.style.removeProperty('--d'); }, 2200);
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (!e.isIntersecting) return;
      e.target.classList.add('is-visible'); io.unobserve(e.target); settle(e.target);
      e.target.querySelectorAll('[data-count]').forEach(countUp);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
  els.forEach(function (el) { io.observe(el); });

  // Count-up for plain numeric stats like "1999", "4,500+" or "4".
  function countUp(el) {
    var target = +el.getAttribute('data-count'), suffix = el.getAttribute('data-suffix') || '', from = target > 1000 && target < 2100 && !suffix ? target - 40 : 0, t0 = null;
    function fmt(n) { return (target >= 10000 || el.getAttribute('data-sep') ? n.toLocaleString('en-US') : String(n)) + suffix; }
    function step(t) { t0 = t0 || t; var p = Math.min(1, (t - t0) / 1600), k = 1 - Math.pow(1 - p, 4); el.textContent = fmt(Math.round(from + (target - from) * k)); if (p < 1) requestAnimationFrame(step); }
    requestAnimationFrame(step);
  }
  document.querySelectorAll('.stats strong').forEach(function (el) {
    var m = el.textContent.trim().match(/^(\d{1,3}(?:,\d{3})+|\d+)(\+?)$/);
    if (!m || el.children.length) return;
    el.setAttribute('data-count', m[1].replace(/,/g, ''));
    if (m[2]) el.setAttribute('data-suffix', m[2]);
    if (m[1].indexOf(',') > -1) el.setAttribute('data-sep', '1');
    el.textContent = m[1].indexOf(',') > -1 ? '0' + m[2] : el.textContent;
  });
})();


/* FAQ accordion: one answer open at a time, with a height slide (native <details name> keeps it working without JS) */
(function () {
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function slide(d, open) {
    var body = d.querySelector('.faq-answer');
    clearTimeout(d._faqClose);
    if (!body || reduce || !body.animate) { d.open = open; return; }
    if (open) d.open = true;
    var h = body.scrollHeight;
    body.getAnimations().forEach(function (x) { x.cancel(); });
    body.animate([{ height: open ? '0px' : h + 'px', opacity: open ? 0 : 1 }, { height: open ? h + 'px' : '0px', opacity: open ? 1 : 0 }], { duration: 280, easing: 'cubic-bezier(.2,.7,.2,1)' });
    // Close on a timer, not onfinish: animations pause in background tabs and the panel must still end up closed
    if (!open) d._faqClose = setTimeout(function () { d.open = false; }, 280);
  }
  document.querySelectorAll('[data-accordion]').forEach(function (box) {
    // JS handles exclusivity itself so the closing answer can animate (the native name group would snap it shut)
    box.querySelectorAll('details[name]').forEach(function (d) { d.removeAttribute('name'); });
    box.querySelectorAll('details > summary').forEach(function (sum) {
      sum.addEventListener('click', function (e) {
        e.preventDefault();
        var d = sum.parentElement, opening = !d.open;
        if (opening) box.querySelectorAll('details[open]').forEach(function (o) { if (o !== d) slide(o, false); });
        slide(d, opening);
      });
    });
  });
})();



/* Text over a scroll-revealed background: the fixed image is dark at the top/bottom of the screen and bright in the
   middle, so each text block blends from white to dark navy as it passes the bright band (smooth, every frame). */
(function () {
  var targets = document.querySelectorAll('.has-reveal-bg .section-head > *');
  if (!targets.length) return;
  var ticking = false;
  function update() {
    ticking = false;
    var vh = window.innerHeight;
    targets.forEach(function (el) {
      var r = el.getBoundingClientRect();
      if (r.bottom < -50 || r.top > vh + 50) return;
      var d = Math.abs((r.top + r.height / 2) - vh / 2) / (vh * 0.32); // 0 at the bright centre, 1+ in the dark edges
      var m = Math.max(0, Math.min(1, 1 - d));
      m = m * m * (3 - 2 * m); // smoothstep for a soft blend
      el.style.setProperty('--on-light', m.toFixed(3));
    });
  }
  function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();

/* Cortex demo chat: choose an example; the answer types out, then its chart/list/table builds in */
(function () {
  document.querySelectorAll('[data-demo]').forEach(function (app) {
    var section = app.closest('section');
    var tabs = section.querySelectorAll('[data-demo-tab]');
    var threads = app.querySelectorAll('[data-demo-thread]');
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var timer, started = false;
    function play(n) {
      clearInterval(timer);
      tabs.forEach(function (t, i) { t.setAttribute('aria-selected', i === n ? 'true' : 'false'); });
      threads.forEach(function (th, i) { th.classList.toggle('is-active', i === n); th.classList.remove('is-typed', 'is-asked'); });
      var th = threads[n]; if (!th) return;
      var ans = th.querySelector('.demo-answer'); var full = ans.getAttribute('data-text') || ans.textContent;
      if (reduce) { ans.textContent = full; th.classList.add('is-asked', 'is-typed'); return; }
      ans.textContent = '';
      requestAnimationFrame(function () { th.classList.add('is-asked'); });
      var i = 0;
      setTimeout(function () {
        timer = setInterval(function () {
          i += 2; ans.textContent = full.slice(0, i);
          if (i >= full.length) { clearInterval(timer); th.classList.add('is-typed'); }
        }, 22);
      }, 650);
    }
    tabs.forEach(function (t, i) { t.addEventListener('click', function () { started = true; play(i); }); });
    app.classList.add('is-live');
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting && !started) { started = true; play(0); io.disconnect(); } }); }, { threshold: .35 });
      io.observe(app);
    } else { play(0); }
  });
})();

/* "How it works" flow: the rail fills with scroll progress and steps light up as the fill reaches them */
(function () {
  var flows = document.querySelectorAll('[data-flow]');
  if (!flows.length) return;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches, ticking = false;
  function update() {
    ticking = false;
    var vh = window.innerHeight;
    flows.forEach(function (f) {
      var r = f.getBoundingClientRect();
      var p = reduce ? 1 : Math.max(0, Math.min(1, (vh * 0.85 - r.top) / (vh * 0.65))); // complete once the section top reaches ~20% of the screen
      f.style.setProperty('--flow', p.toFixed(3));
      var steps = f.querySelectorAll('[data-flow-step]');
      steps.forEach(function (s, i) { s.classList.toggle('is-on', p >= (steps.length > 1 ? i / (steps.length - 1) * 0.9 : 0)); });
    });
  }
  function onScroll() { if (!ticking) { ticking = true; requestAnimationFrame(update); } }
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();
})();

/* Intercompany demo: the document posts in one company, travels through Cortex and builds in the other.
   Plays through the examples on its own until a visitor picks one. */
(function () {
  document.querySelectorAll('[data-icm]').forEach(function (box) {
    var tabs = box.querySelectorAll('[data-icm-tab]');
    var pairs = box.querySelectorAll('[data-icm-pair]');
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      tabs.forEach(function (t, i) { t.addEventListener('click', function () { show(i, true); }); });
      return;
    }
    var timers = [], cycle, current = 0, user = false, started = false;
    function clear() { timers.forEach(clearTimeout); timers = []; }
    function show(n, instant) {
      clear(); current = n;
      tabs.forEach(function (t, i) { t.setAttribute('aria-selected', i === n ? 'true' : 'false'); });
      pairs.forEach(function (p, i) { p.classList.toggle('is-active', i === n); p.classList.remove('is-from', 'is-sent', 'is-done'); });
      var p = pairs[n]; if (!p) return;
      if (instant) { p.classList.add('is-from', 'is-sent', 'is-done'); return; }
      timers.push(setTimeout(function () { p.classList.add('is-from'); }, 60));
      timers.push(setTimeout(function () { p.classList.add('is-sent'); }, 900));
      timers.push(setTimeout(function () { p.classList.add('is-done'); }, 1900));
    }
    function auto() { clearInterval(cycle); cycle = setInterval(function () { if (!user) show((current + 1) % pairs.length); }, 7000); }
    tabs.forEach(function (t, i) { t.addEventListener('click', function () { user = started = true; clearInterval(cycle); show(i); }); });
    box.classList.add('is-live');
    pairs[0].classList.remove('is-done');
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting && !started) { started = true; show(0); auto(); io.disconnect(); } });
      }, { threshold: .3 });
      io.observe(box);
    } else { show(0, true); }
  });
})();

/* Production monitor: tabs switch between the dashboard views */
(function () {
  document.querySelectorAll('[data-pm]').forEach(function (box) {
    var tabs = box.querySelectorAll('[data-pm-tab]'), views = box.querySelectorAll('[data-pm-view]');
    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () {
        tabs.forEach(function (x, j) { x.setAttribute('aria-selected', i === j ? 'true' : 'false'); });
        views.forEach(function (v, j) { v.classList.toggle('is-on', i === j); });
      });
    });
  });
})();

/* Production process: a step lights up in turn and shows its functions; clicking a step stops the auto play */
(function () {
  document.querySelectorAll('[data-pp]').forEach(function (pp) {
    var steps = pp.querySelectorAll('[data-pp-step]'), panels = pp.querySelectorAll('[data-pp-panel]');
    var cur = 0, timer = null, user = false;
    function show(n) {
      cur = n;
      steps.forEach(function (s, i) { s.classList.toggle('is-on', i === n); s.classList.toggle('is-done', i < n); s.setAttribute('aria-selected', i === n ? 'true' : 'false'); });
      panels.forEach(function (p, i) { p.classList.toggle('is-on', i === n); });
      pp.style.setProperty('--at', n);
    }
    steps.forEach(function (s, i) { s.addEventListener('click', function () { user = true; clearInterval(timer); show(i); }); });
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    pp.classList.add('is-live');
    function play() { clearInterval(timer); timer = setInterval(function () { if (!user) show((cur + 1) % steps.length); }, 4200); }
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (es) { es.forEach(function (e) { if (user) return; if (e.isIntersecting) play(); else clearInterval(timer); }); }, { threshold: .3 }).observe(pp);
    } else { play(); }
  });
})();

/* Production control room: runs like a live system and reacts to clicks (sample data) */
(function () {
  var room = document.querySelector('[data-pcr]');
  if (!room) return;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  // Words and messages come from the page in its language (data-pcr-i18n); {0} {1} are filled in
  var I = {}; try { I = JSON.parse(room.querySelector('[data-pcr-i18n]').textContent); } catch (e) {}
  var L = function (k) { var a = arguments, s = I[k] || k; return s.replace(/\{(\d)\}/g, function (_, n) { return a[+n + 1]; }); };
  var LABEL = { run: L('Running'), idle: L('Idle'), stop: L('Stopped'), chg: L('Changeover') };
  var fmt = function (n) { return Math.round(n).toLocaleString('en-US'); };
  var machines = Array.prototype.map.call(room.querySelectorAll('[data-pcr-m]'), function (el) {
    return { el: el, id: el.getAttribute('data-pcr-m'), name: el.dataset.name, line: el.dataset.line, lineName: el.dataset.lineName, status: el.dataset.status, rate: +el.dataset.rate,
      count: +el.dataset.count, wo: el.dataset.wo, product: el.dataset.product, op: el.dataset.op, progress: +el.dataset.progress };
  });
  var $ = function (s) { return room.querySelector(s); };
  var fg = 1120, alerts = 3, oee = 78.4, minute = 0, open = null, visible = false;
  var logEl = $('[data-pcr-log]');
  function now() { var d = new Date(); d.setHours(10); return d; }
  var clock = now();
  function hhmm() { return String(clock.getHours()).padStart(2, '0') + ':' + String(clock.getMinutes()).padStart(2, '0'); }
  function log(text, kind) {
    var li = document.createElement('li');
    if (kind) li.className = 'is-' + kind;
    li.innerHTML = '<time>' + hhmm() + '</time>';
    li.appendChild(document.createTextNode(text));
    li.classList.add('is-new');
    logEl.insertBefore(li, logEl.firstChild);
    while (logEl.children.length > 6) logEl.removeChild(logEl.lastChild);
  }
  function setStatus(m, st) {
    m.status = st;
    m.el.className = 'pcr-m is-' + st;
    m.el.querySelector('[data-st]').textContent = LABEL[st];
    room.querySelectorAll('[data-pcr-belt]').forEach(function (b) {
      var line = b.getAttribute('data-pcr-belt');
      b.classList.toggle('is-moving', machines.some(function (x) { return x.line === line && x.status === 'run'; }));
    });
    if (open === m) fillDrawer(m);
    kpis();
  }
  function kpis() {
    var total = machines.reduce(function (a, m) { return a + m.count; }, 0);
    var running = machines.filter(function (m) { return m.status === 'run'; }).length;
    var stopped = machines.filter(function (m) { return m.status === 'stop'; }).length;
    $('[data-pcr-total]').textContent = fmt(total);
    $('[data-pcr-running]').textContent = running + ' / ' + machines.length;
    $('[data-pcr-down]').textContent = stopped ? L('{0} stopped', stopped) : L('None stopped');
    $('[data-pcr-fg]').textContent = fmt(fg);
    $('[data-pcr-alerts]').textContent = alerts;
    $('[data-pcr-oee]').textContent = oee.toFixed(1) + '%';
  }
  function draw(m) {
    m.el.querySelector('[data-count]').textContent = fmt(m.count);
    m.el.querySelector('[data-bar]').style.setProperty('--p', Math.min(100, m.progress) + '%');
    if (open === m) fillDrawer(m);
  }
  // Drawer: machine details and actions
  var drawer = $('[data-pcr-drawer]');
  function fillDrawer(m) {
    drawer.querySelector('[data-d-name]').textContent = L('{0}, line {1}', m.name, m.lineName || m.line);
    var st = drawer.querySelector('[data-d-st]'); st.className = 'pcr-st pcr-d-st is-' + m.status; st.querySelector('span').textContent = LABEL[m.status];
    drawer.querySelector('[data-d-wo]').textContent = m.wo;
    drawer.querySelector('[data-d-product]').textContent = m.product;
    drawer.querySelector('[data-d-op]').textContent = m.op;
    drawer.querySelector('[data-d-count]').textContent = fmt(m.count);
    drawer.querySelector('[data-d-speed]').textContent = m.status === 'run' ? L('{0} units/min', Math.round(m.rate * 60)) : L('{0} units/min', 0);
    var t = drawer.querySelector('[data-d-action="toggle"]');
    t.textContent = m.status === 'run' ? L('Pause') : (m.status === 'stop' ? L('Maintenance in progress') : L('Resume'));
    t.disabled = m.status === 'stop';
  }
  machines.forEach(function (m) {
    m.el.addEventListener('click', function () {
      open = m; fillDrawer(m);
      room.querySelectorAll('.pcr-m.is-open').forEach(function (x) { x.classList.remove('is-open'); });
      m.el.classList.add('is-open');
      drawer.classList.add('is-on'); drawer.setAttribute('aria-hidden', 'false');
    });
  });
  $('[data-pcr-close]').addEventListener('click', function () {
    drawer.classList.remove('is-on'); drawer.setAttribute('aria-hidden', 'true');
    if (open) open.el.classList.remove('is-open'); open = null;
  });
  drawer.querySelectorAll('[data-d-action]').forEach(function (b) {
    b.addEventListener('click', function () {
      var m = open; if (!m) return;
      var a = b.getAttribute('data-d-action');
      if (a === 'toggle') {
        if (m.status === 'run') { setStatus(m, 'idle'); log(L('{0} paused by supervisor', m.name)); }
        else { setStatus(m, 'run'); log(L('{0} resumed on {1}', m.name, m.wo), 'ok'); }
      } else if (a === 'report') {
        m.count += 25; m.progress = Math.min(100, m.progress + 2); draw(m); kpis();
        log(L('25 units of {0} reported on {1}', m.product, m.name), 'ok');
      } else if (a === 'maint') {
        if (m.status === 'stop') return;
        setStatus(m, 'stop'); alerts++; kpis();
        log(L('Maintenance called for {0}', m.name), 'warn');
        setTimeout(function () { if (m.status === 'stop') { setStatus(m, 'run'); alerts = Math.max(0, alerts - 1); kpis(); log(L('{0} back running after maintenance', m.name), 'ok'); } }, 12000);
      }
    });
  });
  // Work orders
  room.querySelectorAll('[data-pcr-wo]').forEach(function (li) {
    var btn = li.querySelector('[data-wo-action]'), st = li.querySelector('[data-wo-st]');
    btn.addEventListener('click', function () {
      var s = li.getAttribute('data-status'), wo = li.getAttribute('data-pcr-wo');
      if (s === 'Scheduled') {
        li.setAttribute('data-status', 'In progress'); st.textContent = L('In progress'); btn.textContent = L('Complete');
        log(L('{0} released and started', wo), 'ok');
        machines.forEach(function (m) { if (m.wo === wo && m.status !== 'stop') setStatus(m, 'run'); });
      } else if (s === 'In progress') {
        li.setAttribute('data-status', 'Done'); st.textContent = L('Done'); btn.textContent = L('Received'); btn.disabled = true; li.classList.add('is-done');
        fg += 240; kpis();
        log(L('{0} completed, 240 units received to finished goods', wo), 'ok');
      }
    });
  });
  // Quality hold
  var hold = $('[data-pcr-hold]');
  hold.querySelectorAll('[data-hold]').forEach(function (b) {
    b.addEventListener('click', function () {
      var ok = b.getAttribute('data-hold') === 'approve';
      hold.classList.add(ok ? 'is-approved' : 'is-rejected');
      hold.querySelector('[data-hold-text]').textContent = ok ? L('Lot L 8840 released to finished goods by the quality supervisor.') : L('Lot L 8840 sent to rework on the paint line. A new inspection is planned.');
      hold.querySelectorAll('[data-hold]').forEach(function (x) { x.disabled = true; });
      alerts = Math.max(0, alerts - 1); kpis();
      log(ok ? L('Lot L 8840 approved and released') : L('Lot L 8840 sent to rework'), ok ? 'ok' : 'warn');
    });
  });
  // Live simulation
  var bars = $('[data-pcr-bars]');
  var events = [
    function () { var m = find('w03'); if (m.status === 'chg') { setStatus(m, 'run'); log(L('Welder W 03 changeover finished, WO 2298 running'), 'ok'); } },
    function () { log(L('Steel coil 2 mm issued to line A, 3.2 t'), ''); },
    function () { var m = find('pk1'); if (m.status === 'idle') { setStatus(m, 'run'); log(L('Cartons arrived, Packing PK 1 running'), 'ok'); } },
    function () { log(L('Lot L 8842 passed in process inspection'), 'ok'); },
    function () { var m = find('l01'); if (m.status === 'run') { setStatus(m, 'idle'); alerts++; kpis(); log(L('Laser L 01 waiting for material: sheet 1.5 mm'), 'warn');
      setTimeout(function () { if (m.status === 'idle') { setStatus(m, 'run'); alerts = Math.max(0, alerts - 1); kpis(); log(L('Sheets delivered, Laser L 01 running'), 'ok'); } }, 9000); } },
    function () { log(L('Planned maintenance for Press P 02 at 13:30'), ''); },
  ];
  function find(id) { return machines.filter(function (m) { return m.id === id; })[0]; }
  var tick = 0, perMinute = 0, ev = 0;
  function step() {
    if (!visible) return;
    tick++;
    clock = new Date(clock.getTime() + 1000);
    $('[data-pcr-clock]').textContent = hhmm() + ':' + String(clock.getSeconds()).padStart(2, '0');
    machines.forEach(function (m) {
      if (m.status !== 'run') return;
      var add = Math.random() < m.rate ? 1 + (Math.random() < .3 ? 1 : 0) : 0;
      if (!add) return;
      m.count += add; perMinute += add; m.progress = Math.min(99, m.progress + add * 0.02);
      draw(m);
    });
    oee = Math.max(76, Math.min(81, oee + (Math.random() - .5) * 0.2));
    kpis();
    if (tick % 3 === 0) {
      var i = document.createElement('i'); i.style.setProperty('--h', Math.min(100, 40 + perMinute * 6) + '%'); i.className = 'is-new';
      bars.appendChild(i); if (bars.children.length > 30) bars.removeChild(bars.firstChild); perMinute = 0;
    }
    if (tick % 7 === 0) { events[ev % events.length](); ev++; }
  }
  kpis();
  if (!reduce) {
    setInterval(step, 1000);
    if ('IntersectionObserver' in window) new IntersectionObserver(function (es) { es.forEach(function (e) { visible = e.isIntersecting; }); }, { threshold: .15 }).observe(room);
    else visible = true;
  }
})();

/* Project control room: costs post live, progress moves, today line advances; projects and approvals are clickable */
(function () {
  var room = document.querySelector('[data-pmc]');
  if (!room) return;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var $ = function (s) { return room.querySelector(s); };
  // Words and messages come from the page in its language (data-pmc-i18n); {0} {1} are filled in
  var I = {}; try { I = JSON.parse(room.querySelector('[data-pmc-i18n]').textContent); } catch (e) {}
  var AR = room.getAttribute('lang') === 'ar';
  var L = function (k) { var a = arguments, s = I[k] || k; return s.replace(/\{(\d)\}/g, function (_, n) { return a[+n + 1]; }); };
  var M = function (v) { return AR ? (v / 1e6).toFixed(2) + ' مليون ريال' : 'SAR ' + (v / 1e6).toFixed(2) + 'M'; };
  var K = function (v) { return AR ? Math.round(v).toLocaleString('en-US') + ' ريال' : 'SAR ' + Math.round(v).toLocaleString('en-US'); };
  var projects = Array.prototype.map.call(room.querySelectorAll('[data-pmc-p]'), function (el) {
    var d = el.dataset;
    return { el: el, id: el.getAttribute('data-pmc-p'), name: d.name, contract: +d.contract, budget: +d.budget, cost: +d.cost, committed: +d.committed, progress: +d.progress, billed: +d.billed, variation: +d.variation };
  });
  var postedToday = 0, pending = 4, open = null, visible = false;
  var t = new Date(); t.setHours(10, 42, 0, 0);
  var hhmm = function () { return String(t.getHours()).padStart(2, '0') + ':' + String(t.getMinutes()).padStart(2, '0'); };
  var logEl = $('[data-pmc-log]');
  function log(text, kind) {
    var li = document.createElement('li'); li.className = (kind ? 'is-' + kind + ' ' : '') + 'is-new';
    li.innerHTML = '<time>' + hhmm() + '</time>'; li.appendChild(document.createTextNode(text));
    logEl.insertBefore(li, logEl.firstChild); while (logEl.children.length > 6) logEl.removeChild(logEl.lastChild);
  }
  function health(p) { var used = p.cost / p.budget * 100; return used > p.progress + 6 ? ['stop', L('Over budget')] : (used > p.progress + 2 ? ['idle', L('At risk')] : ['run', L('On track')]); }
  function retention(p) { return p.billed * 0.1; }
  function draw(p) {
    var h = health(p), used = Math.round(p.cost / p.budget * 100);
    p.el.className = 'pcr-m pmc-p is-' + h[0] + (open === p ? ' is-open' : '');
    p.el.querySelector('[data-st]').textContent = h[1];
    p.el.querySelector('[data-prog]').textContent = Math.floor(p.progress) + '%';
    p.el.querySelector('[data-prog-bar]').style.setProperty('--p', p.progress + '%');
    p.el.querySelector('[data-used]').textContent = used + '%';
    p.el.querySelector('[data-used-bar]').style.setProperty('--p', Math.min(100, used) + '%');
    p.el.querySelector('[data-cost]').textContent = M(p.cost);
    p.el.querySelector('[data-billed]').textContent = M(p.billed);
    if (open === p) fill(p);
  }
  function kpis() {
    var sum = function (k) { return projects.reduce(function (a, p) { return a + p[k]; }, 0); };
    var contract = sum('contract'), cost = sum('cost'), billed = sum('billed');
    var forecast = projects.reduce(function (a, p) { return a + (p.progress > 0 ? p.cost / (p.progress / 100) : p.budget); }, 0);
    var vars = projects.filter(function (p) { return p.variation > 0; }).length;
    $('[data-pmc-contract]').textContent = M(contract);
    $('[data-pmc-var]').textContent = vars ? (vars > 1 ? L('{0} variations pending', vars) : L('1 variation pending')) : L('No variations pending');
    $('[data-pmc-cost]').textContent = M(cost);
    $('[data-pmc-today]').textContent = K(postedToday);
    $('[data-pmc-billed]').textContent = M(billed);
    $('[data-pmc-ret]').textContent = L('Retention held {0}', M(billed * 0.1));
    $('[data-pmc-margin]').textContent = Math.max(0, (1 - forecast / contract) * 100).toFixed(1) + '%';
    $('[data-pmc-pending]').textContent = pending;
    $('[data-pmc-pending-tag]').textContent = pending ? L('{0} pending', pending) : L('All done');
  }
  // Drawer
  var drawer = $('[data-pmc-drawer]');
  function fill(p) {
    var h = health(p), q = function (s) { return drawer.querySelector(s); };
    q('[data-d-name]').textContent = p.name;
    var st = q('[data-d-st]'); st.className = 'pcr-st pcr-d-st is-' + h[0]; st.querySelector('span').textContent = h[1];
    q('[data-d-contract]').textContent = M(p.contract); q('[data-d-budget]').textContent = M(p.budget);
    q('[data-d-cost]').textContent = M(p.cost); q('[data-d-committed]').textContent = M(p.committed);
    q('[data-d-progress]').textContent = Math.floor(p.progress) + '%'; q('[data-d-billed]').textContent = M(p.billed);
    q('[data-d-ret]').textContent = M(retention(p)); q('[data-d-var]').textContent = p.variation ? K(p.variation) : L('None');
    var due = p.contract * p.progress / 100 - p.billed;
    var inv = q('[data-d-action="invoice"]'); inv.disabled = due < 50000; inv.textContent = due < 50000 ? L('Billed up to progress') : L('Issue progress invoice {0}', K(due * 0.9));
    var vb = q('[data-d-action="variation"]'); vb.disabled = !p.variation; vb.textContent = p.variation ? L('Approve variation {0}', K(p.variation)) : L('No variation pending');
  }
  projects.forEach(function (p) {
    p.el.addEventListener('click', function () {
      if (open) open.el.classList.remove('is-open');
      open = p; p.el.classList.add('is-open'); fill(p);
      drawer.classList.add('is-on'); drawer.setAttribute('aria-hidden', 'false');
    });
  });
  $('[data-pmc-close]').addEventListener('click', function () { drawer.classList.remove('is-on'); drawer.setAttribute('aria-hidden', 'true'); if (open) open.el.classList.remove('is-open'); open = null; });
  drawer.querySelectorAll('[data-d-action]').forEach(function (b) {
    b.addEventListener('click', function () {
      var p = open; if (!p) return;
      var a = b.getAttribute('data-d-action');
      if (a === 'invoice') {
        var due = p.contract * p.progress / 100 - p.billed; if (due < 50000) return;
        p.billed += due; log(L('Progress invoice {0} issued for {1}, retention {2} held', K(due * 0.9), p.name, K(due * 0.1)), 'ok');
      } else if (a === 'cost') {
        p.cost += 85000; postedToday += 85000; log(L('Cost SAR 85,000 recorded on {0}', p.name));
      } else if (a === 'variation') {
        if (!p.variation) return;
        p.contract += p.variation; p.budget += p.variation * 0.8; log(L('Variation {0} approved on {1}, contract updated', K(p.variation), p.name), 'ok');
        p.variation = 0; if (p.id === 'cf') { var vo = room.querySelector('[data-pmc-ap="vo"]'); if (vo && !vo.classList.contains('is-done')) closeAp(vo, L('Approved')); }
      }
      draw(p); kpis();
    });
  });
  // Approvals inbox
  function closeAp(li, label) {
    li.classList.add('is-done');
    li.querySelector('.pmc-ap-btns').innerHTML = '<span class="pmc-ap-done">' + label + '</span>';
    pending = Math.max(0, pending - 1); kpis();
  }
  function find(id) { return projects.filter(function (p) { return p.id === id; })[0]; }
  room.querySelectorAll('[data-pmc-ap]').forEach(function (li) {
    li.querySelectorAll('[data-ap]').forEach(function (b) {
      b.addEventListener('click', function () {
        var ok = b.getAttribute('data-ap') === 'approve', k = li.getAttribute('data-pmc-ap');
        if (k === 'ts') { var tb = find('tb'); tb.cost += 61800; postedToday += 61800; draw(tb); log(L('Timesheets approved: 412 hours, SAR 61,800 posted to Tower B'), 'ok'); }
        if (k === 'sc') { if (ok) { var t2 = find('tb'); t2.cost += 640000; t2.committed = Math.max(0, t2.committed - 640000); postedToday += 640000; draw(t2); log(L('Certificate 7 approved: SAR 640,000 to Gulf Steel Works'), 'ok'); } else log(L('Certificate 7 returned to Gulf Steel Works for correction'), 'warn'); }
        if (k === 'pr') { var mf = find('mf'); mf.committed += 96000; draw(mf); log(L('PR 1180 approved, PO 4519 issued for ceramic tiles'), 'ok'); }
        if (k === 'vo') { var cf = find('cf'); if (ok && cf.variation) { cf.contract += cf.variation; cf.budget += cf.variation * 0.8; log(L('VO 12 approved: Clinic fit out contract up by SAR 260,000'), 'ok'); } else if (!ok) log(L('VO 12 rejected and returned to the client'), 'warn'); cf.variation = 0; draw(cf); }
        closeAp(li, ok ? L('Approved') : L('Rejected'));
      });
    });
  });
  // Live: costs post, progress creeps, today line advances
  var gantt = $('[data-pmc-gantt]'), today = 24, tick = 0;
  var feed = [
    ['lw', 38500, 'GRN 3321: roof sheets received at Logistics warehouse'],
    ['tb', 112000, 'Concrete C40, 180 m3 delivered to Tower B'],
    ['se', 24600, 'Timesheet: 164 hours posted on School extension'],
    ['mf', 57200, 'Subcontractor invoice from Red Line Interiors on Mall fit out'],
    ['cf', 18900, 'Medical gas pipework issued to Clinic fit out'],
    ['tb', 46300, 'Tower crane rental for October posted to Tower B'],
  ];
  function step() {
    if (!visible) return;
    tick++; t = new Date(t.getTime() + 60000); $('[data-pmc-clock]').textContent = hhmm();
    projects.forEach(function (p) { if (p.progress < 99 && Math.random() < .35) { p.progress = Math.min(99, p.progress + 0.2); draw(p); } });
    if (tick % 4 === 0) { var f = feed[(tick / 4) % feed.length], p = find(f[0]); p.cost += f[1]; postedToday += f[1]; draw(p); log(L(f[2])); }
    if (tick % 2 === 0) { today = Math.min(70, today + 0.15); gantt.style.setProperty('--today', today.toFixed(2)); }
    kpis();
  }
  projects.forEach(draw); kpis();
  if (!reduce) {
    setInterval(step, 1500);
    if ('IntersectionObserver' in window) new IntersectionObserver(function (es) { es.forEach(function (e) { visible = e.isIntersecting; }); }, { threshold: .15 }).observe(room);
    else visible = true;
  }
})();

/* Project control: Gantt / resources / BOQ tabs, project filter, reassign an overbooked crew, import and analyse a BOQ */
(function () {
  var room = document.querySelector('[data-pmc]');
  if (!room) return;
  var tabs = room.querySelectorAll('[data-pmc-tab]'), panes = room.querySelectorAll('[data-pmc-pane]');
  var I = {}; try { I = JSON.parse(room.querySelector('[data-pmc-i18n]').textContent); } catch (e) {}
  var L = function (k) { return I[k] || k; };
  function tab(n) {
    tabs.forEach(function (t, i) { t.classList.toggle('is-on', i === n); t.setAttribute('aria-selected', i === n ? 'true' : 'false'); });
    panes.forEach(function (p, i) { p.classList.toggle('is-on', i === n); });
  }
  tabs.forEach(function (t, i) { t.addEventListener('click', function () { tab(i); }); });
  // Gantt filter by project; opening a project card shows its Gantt
  var chips = room.querySelectorAll('[data-chip]'), rows = room.querySelectorAll('[data-pmc-gantt] [data-proj]');
  function filter(id) {
    chips.forEach(function (c) { c.classList.toggle('is-on', c.getAttribute('data-chip') === id); });
    rows.forEach(function (r) { r.classList.toggle('is-hidden', id !== 'all' && r.getAttribute('data-proj') !== id); });
  }
  chips.forEach(function (c) { c.addEventListener('click', function () { filter(c.getAttribute('data-chip')); }); });
  room.querySelectorAll('[data-pmc-p]').forEach(function (p) { p.addEventListener('click', function () { tab(0); filter(p.getAttribute('data-pmc-p')); }); });
  var logEl = room.querySelector('[data-pmc-log]');
  function log(text, kind) {
    var li = document.createElement('li'); li.className = (kind ? 'is-' + kind + ' ' : '') + 'is-new';
    li.innerHTML = '<time>' + room.querySelector('[data-pmc-clock]').textContent + '</time>'; li.appendChild(document.createTextNode(text));
    logEl.insertBefore(li, logEl.firstChild); while (logEl.children.length > 6) logEl.removeChild(logEl.lastChild);
  }
  // Resources: reassign the overbooked crew
  room.querySelectorAll('[data-res-fix]').forEach(function (b) {
    b.addEventListener('click', function () {
      var li = b.closest('li');
      li.classList.remove('is-over'); li.querySelector('[data-util]').textContent = '96%'; li.querySelector('i').style.setProperty('--p', '96%');
      li.querySelector('div span').textContent = L('Crew, 10 people, Tower B');
      b.replaceWith(Object.assign(document.createElement('span'), { className: 'pmc-ap-done', textContent: L('Reassigned') }));
      var ex = room.querySelector('[data-res="Excavator EX 05"]');
      log(L('Steel fixers split: 3 people moved to School extension from a hired crew, overbooking cleared'), 'ok');
      if (ex) { ex.querySelector('[data-util]').textContent = '58%'; ex.querySelector('i').style.setProperty('--p', '58%'); }
    });
  });
  // BOQ: import a ready BOQ, analyse each item, then create the budget
  var boq = room.querySelector('[data-pmc-boq]');
  if (boq) {
    var imp = boq.querySelector('[data-boq-import]'), bud = boq.querySelector('[data-boq-budget]'), st = boq.querySelector('[data-boq-status]');
    imp.addEventListener('click', function () {
      imp.disabled = true; imp.textContent = L('Reading file...');
      boq.classList.remove('is-loaded', 'is-analysed'); void boq.offsetWidth; boq.classList.add('is-loaded');
      st.textContent = L('Reading 148 items and matching them to cost codes');
      setTimeout(function () {
        boq.classList.add('is-analysed'); imp.textContent = L('Analysed');
        st.textContent = L('148 items analysed: material 46%, labour 21%, equipment 6%, subcontract 27%');
        bud.disabled = false;
        log(L('BOQ_Tower_C.xlsx imported: 148 items, SAR 24.6M, analysed into cost types'), 'ok');
      }, 1900);
    });
    bud.addEventListener('click', function () {
      bud.disabled = true; bud.textContent = L('Budget created');
      st.textContent = L('Budget Tower C created: 148 lines in 9 phases, linked to cost codes');
      log(L('Budget for Tower C created from the BOQ: SAR 24.6M in 9 phases'), 'ok');
    });
  }
})();

/* Shared add-on control room (sections/ops_control.php): figures tick, events arrive, cards and approvals react */
(function () {
  document.querySelectorAll('[data-ops]').forEach(function (room) {
    var cfg = JSON.parse(room.querySelector('[data-ops-cfg]').textContent);
    var meta = JSON.parse(room.querySelector('[data-ops-meta]').textContent);
    var $ = function (s) { return room.querySelector(s); };
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var AR = cfg.lang === 'ar', W = cfg.words || {};
    function fmt(v, f) {
      if (AR) switch (f) {
        case 'sar': return Math.round(v).toLocaleString('en-US') + ' ريال';
        case 'sarm': return (v / 1e6).toFixed(2) + ' مليون ريال';
        case 'sark': return Math.round(v / 1e3).toLocaleString('en-US') + ' ألف ريال';
      }
      switch (f) {
        case 'sar': return 'SAR ' + Math.round(v).toLocaleString('en-US');
        case 'sarm': return 'SAR ' + (v / 1e6).toFixed(2) + 'M';
        case 'sark': return 'SAR ' + Math.round(v / 1e3).toLocaleString('en-US') + 'K';
        case 'pct': return v.toFixed(1) + '%';
        case 'pct0': return Math.round(v) + '%';
        case 'text': return String(v);
        default: return Math.round(v).toLocaleString('en-US');
      }
    }
    var t = new Date(); t.setHours(10, 42, 0, 0);
    var hhmm = function () { return String(t.getHours()).padStart(2, '0') + ':' + String(t.getMinutes()).padStart(2, '0'); };
    var logEl = $('[data-ops-log]'), pending = cfg.inbox.length, open = null, visible = false, chartAcc = 0;
    function log(text, kind) {
      if (!text) return;
      var li = document.createElement('li'); li.className = (kind ? 'is-' + kind + ' ' : '') + 'is-new';
      li.innerHTML = '<time>' + hhmm() + '</time>'; li.appendChild(document.createTextNode(text));
      logEl.insertBefore(li, logEl.firstChild); while (logEl.children.length > 6) logEl.removeChild(logEl.lastChild);
    }
    function kpi(k, d) {
      var x = cfg.kpis[k]; if (!x) return;
      if (x.f === 'text') x.v = d; else { x.v = Math.max(0, x.v + d); if (x.f === 'pct' || x.f === 'pct0') x.v = Math.min(100, x.v); if (k === cfg.chart && d > 0) chartAcc += d; }
      var el = room.querySelector('[data-kpi="' + k + '"]'); if (el) el.textContent = fmt(x.v, x.f);
    }
    function fx(map) { if (map) Object.keys(map).forEach(function (k) { kpi(k, map[k]); }); }
    function card(id) { return room.querySelector('[data-ops-card="' + id + '"]'); }
    function drawCard(id) {
      var c = cfg.cards[id], el = card(id); if (!el) return;
      el.className = 'pcr-m ops-card is-' + c.status + (open === id ? ' is-open' : '');
      el.querySelector('[data-st]').textContent = c.label;
      c.rows.forEach(function (r, i) { var s = el.querySelector('[data-row="' + i + '"]'); if (s) s.textContent = fmt(r.v, r.f); });
      if (c.bar !== null && c.bar !== undefined) { var b = el.querySelector('[data-bar]'); if (b) b.style.setProperty('--p', Math.min(100, c.rows[c.bar].v / c.max * 100) + '%'); }
      if (open === id) fill(id);
    }
    function apply(a, id) {
      fx(a.fx);
      if (id && a.rows) Object.keys(a.rows).forEach(function (i) { var r = cfg.cards[id].rows[i]; if (r.f === 'text') r.v = a.rows[i]; else r.v = Math.max(0, r.v + a.rows[i]); });
      var target = a.card || id;
      if (target && a.status) { cfg.cards[target].status = a.status; cfg.cards[target].label = a.state || cfg.cards[target].label; }
      if (target && a.card && a.rows && a.card !== id) Object.keys(a.rows).forEach(function (i) { var r = cfg.cards[target].rows[i]; if (r.f === 'text') r.v = a.rows[i]; else r.v = Math.max(0, r.v + a.rows[i]); });
      if (target) drawCard(target);
      log(a.log, a.logkind || (a.kind === 'bad' ? 'warn' : (a.kind === 'ok' ? 'ok' : '')));
    }
    // Drawer
    var drawer = $('[data-ops-drawer]');
    function fill(id) {
      var c = cfg.cards[id], m = meta[id];
      drawer.querySelector('[data-d-name]').textContent = m.title;
      var st = drawer.querySelector('[data-d-st]'); st.className = 'pcr-st pcr-d-st is-' + c.status; st.querySelector('span').textContent = c.label;
      drawer.querySelector('[data-d-facts]').innerHTML = c.rows.map(function (r, i) { return '<div><dt>' + m.rows[i] + '</dt><dd>' + fmt(r.v, r.f) + '</dd></div>'; }).join('');
      var box = drawer.querySelector('[data-d-actions]'); box.innerHTML = '';
      c.actions.forEach(function (a) {
        var b = document.createElement('button'); b.type = 'button'; b.className = 'pcr-btn' + (a.kind ? ' is-' + a.kind : '');
        b.textContent = a.done_label && a.used ? a.done_label : a.label; b.disabled = !!(a.once && a.used);
        b.addEventListener('click', function () { if (a.once && a.used) return; a.used = true; apply(a, id); fill(id); });
        box.appendChild(b);
      });
    }
    room.querySelectorAll('[data-ops-card]').forEach(function (el) {
      el.addEventListener('click', function () {
        var id = el.getAttribute('data-ops-card');
        if (open && card(open)) card(open).classList.remove('is-open');
        open = id; el.classList.add('is-open'); fill(id);
        drawer.classList.add('is-on'); drawer.setAttribute('aria-hidden', 'false');
      });
    });
    $('[data-ops-close]').addEventListener('click', function () { drawer.classList.remove('is-on'); drawer.setAttribute('aria-hidden', 'true'); if (open && card(open)) card(open).classList.remove('is-open'); open = null; });
    // Approvals
    room.querySelectorAll('[data-ops-ap]').forEach(function (li) {
      var acts = cfg.inbox[+li.getAttribute('data-ops-ap')];
      li.querySelectorAll('[data-ap]').forEach(function (b) {
        b.addEventListener('click', function () {
          var a = acts[+b.getAttribute('data-ap')];
          apply(a, null);
          li.classList.add('is-done'); li.querySelector('.pmc-ap-btns').innerHTML = '<span class="pmc-ap-done">' + (a.done || W.done || 'Done') + '</span>';
          pending = Math.max(0, pending - 1); $('[data-ops-pending]').textContent = pending ? pending + ' ' + (W.pending || 'pending') : (W.all || 'All done');
        });
      });
    });
    // Live
    var bars = $('[data-ops-bars]'), tick = 0, fi = 0, tk = cfg.tick || {};
    function rnd(a, b) { return a + Math.random() * (b - a); }
    function step() {
      if (!visible) return;
      tick++; t = new Date(t.getTime() + 60000); $('[data-ops-clock]').textContent = hhmm();
      (tk.kpis || []).forEach(function (k) {
        if (Math.random() >= (k[3] || 1)) return;
        var d = Math.round(rnd(k[1], k[2]) * 10) / 10, x = cfg.kpis[k[0]];
        if (k[4] !== undefined && x) d = Math.min(d, k[4] - x.v);   // optional cap, e.g. 100 for percentages
        if (d > 0) kpi(k[0], d);
      });
      (tk.cards || []).forEach(function (c) {
        var cd = cfg.cards[c[0]]; if (!cd || cd.status === 'stop') return;
        var d = Math.round(rnd(c[2], c[3])); if (!d) return;
        if (c[5] !== undefined && c[5] !== null) { d = Math.min(d, cd.rows[c[5]].v - cd.rows[c[1]].v); if (d <= 0) return; }
        cd.rows[c[1]].v += d; if (c[4]) kpi(c[4], d); drawCard(c[0]);
      });
      if (tick % 3 === 0 && bars) {
        var max = tk.chartMax || 1, i = document.createElement('i');
        i.style.setProperty('--h', Math.max(8, Math.min(100, chartAcc / max * 100)) + '%'); i.className = 'is-new';
        bars.appendChild(i); if (bars.children.length > 30) bars.removeChild(bars.firstChild); chartAcc = 0;
      }
      if (tick % (tk.feedEvery || 4) === 0 && cfg.feed.length) { apply(cfg.feed[fi % cfg.feed.length], null); fi++; }
    }
    if (!reduce) {
      setInterval(step, tk.ms || 1500);
      if ('IntersectionObserver' in window) new IntersectionObserver(function (es) { es.forEach(function (e) { visible = e.isIntersecting; }); }, { threshold: .15 }).observe(room);
      else visible = true;
    }
  });
})();

/* Outcomes: numbers count up and comparison bars grow when the section comes into view */
(function () {
  document.querySelectorAll('[data-oc]').forEach(function (box) {
    var nums = box.querySelectorAll('[data-oc-n]');
    function run() {
      box.classList.add('is-on');
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      nums.forEach(function (n) {
        var target = parseFloat(n.getAttribute('data-oc-n')), dec = (String(target).split('.')[1] || '').length, t0 = performance.now();
        (function f(now) { var p = Math.min(1, (now - t0) / 1400), e = 1 - Math.pow(1 - p, 3); n.textContent = (target * e).toFixed(dec); if (p < 1) requestAnimationFrame(f); })(t0);
      });
    }
    if ('IntersectionObserver' in window) { var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { run(); io.disconnect(); } }); }, { threshold: .35 }); io.observe(box); }
    else run();
  });
})();

/* Industry first section: the live figures keep moving (sample data) */
(function () {
  document.querySelectorAll('[data-ind]').forEach(function (band) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var nums = band.querySelectorAll('[data-ind-n]'), visible = true;
    if ('IntersectionObserver' in window) new IntersectionObserver(function (es) { es.forEach(function (e) { visible = e.isIntersecting; }); }).observe(band);
    setInterval(function () {
      if (!visible) return;
      nums.forEach(function (n) {
        var step = parseFloat(n.getAttribute('data-step')) || 0, dec = +n.getAttribute('data-dec') || 0;
        if (!step || Math.random() < .35) return;
        var v = parseFloat(n.getAttribute('data-ind-n')) + step * (0.4 + Math.random() * 0.9);
        n.setAttribute('data-ind-n', v);
        n.textContent = v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec });
        var m = n.closest('.ind-metric'); m.classList.remove('is-tick'); void m.offsetWidth; m.classList.add('is-tick');
      });
    }, 1800);
  });
})();
