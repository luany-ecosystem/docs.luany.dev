/**
 * docs.luany.dev — Site behaviour
 *
 *  1. Appearance (system | light | dark)
 *  2. Navbar: scrolled state, mega menus, mobile drawer
 *  3. Docs: highlighting, copy buttons, outline, sidebar, search
 *  4. Carousel (ecosystem) and stat slider
 *  5. GSAP: reveals, split headlines, hero stage, process line, tilt
 *
 * Everything degrades gracefully: without JS or with reduced motion,
 * content is fully visible and usable.
 */
(function () {
    'use strict';

    var root    = document.documentElement;
    var reduced = root.classList.contains('reduced-motion');
    var hasGsap = typeof window.gsap !== 'undefined';
    var script  = document.currentScript || document.querySelector('script[data-copy]');
    var T = { copy: (script && script.dataset.copy) || 'Copy', copied: (script && script.dataset.copied) || 'Copied' };

    function $(sel, ctx)  { return (ctx || document).querySelector(sel); }
    function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

    // ── 1. Appearance ─────────────────────────────────────────────────────
    var media = window.matchMedia('(prefers-color-scheme: dark)');

    function applyTheme(pref) {
        var theme = pref === 'system' ? (media.matches ? 'dark' : 'light') : pref;
        root.setAttribute('data-theme', theme);
        root.setAttribute('data-theme-pref', pref);
        $$('[data-theme-set]').forEach(function (b) {
            b.setAttribute('aria-pressed', b.getAttribute('data-theme-set') === pref ? 'true' : 'false');
        });
        var light = $('#hl-light'), dark = $('#hl-dark');
        if (light) light.media = theme === 'dark' ? 'not all' : 'all';
        if (dark) dark.media = theme === 'dark' ? 'all' : 'not all';
    }
    function currentPref() { return root.getAttribute('data-theme-pref') || 'system'; }

    $$('[data-theme-set]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var pref = btn.getAttribute('data-theme-set');
            try { localStorage.setItem('luany-theme', pref); } catch (e) {}
            root.classList.add('theme-transition');
            applyTheme(pref);
            setTimeout(function () { root.classList.remove('theme-transition'); }, 400);
        });
    });
    if (media.addEventListener) media.addEventListener('change', function () { if (currentPref() === 'system') applyTheme('system'); });
    applyTheme(currentPref());

    // ── 2. Navbar ─────────────────────────────────────────────────────────
    var nav = $('#navbar');
    var setDrawer = function () {};
    if (nav) {
        var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 12); };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        var triggers = $$('[data-menu-trigger]', nav);
        var backdrop = $('#navBackdrop');
        var closeTimer = 0;
        var closeAll = function () {
            triggers.forEach(function (t) {
                t.setAttribute('aria-expanded', 'false');
                var p = document.getElementById(t.getAttribute('aria-controls'));
                if (p) p.classList.remove('is-open');
            });
            nav.classList.remove('menu-open');
            if (backdrop) backdrop.classList.remove('is-open');
        };
        var open = function (t) {
            clearTimeout(closeTimer);
            if (t.getAttribute('aria-expanded') === 'true') return;
            closeAll();
            t.setAttribute('aria-expanded', 'true');
            var p = document.getElementById(t.getAttribute('aria-controls'));
            if (p) {
                p.classList.add('is-open');
                if (hasGsap && !reduced) {
                    gsap.fromTo($$('.mega-item', p), { y: 10, opacity: 0 }, { y: 0, opacity: 1, duration: .35, stagger: .02, ease: 'power3.out', overwrite: true });
                }
            }
            nav.classList.add('menu-open');
            if (backdrop) backdrop.classList.add('is-open');
        };
        var scheduleClose = function () { closeTimer = setTimeout(closeAll, 180); };

        triggers.forEach(function (t) {
            var panel = document.getElementById(t.getAttribute('aria-controls'));
            t.addEventListener('click', function () { t.getAttribute('aria-expanded') === 'true' ? closeAll() : open(t); });
            if (window.matchMedia('(hover: hover)').matches) {
                t.addEventListener('mouseenter', function () { open(t); });
                t.addEventListener('mouseleave', scheduleClose);
                if (panel) {
                    panel.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
                    panel.addEventListener('mouseleave', scheduleClose);
                }
            }
        });
        if (backdrop) backdrop.addEventListener('click', closeAll);

        var toggle = $('#navToggle');
        setDrawer = function (openIt) {
            nav.classList.toggle('drawer-open', openIt);
            document.body.style.overflow = openIt ? 'hidden' : '';
            if (toggle) toggle.setAttribute('aria-expanded', openIt ? 'true' : 'false');
        };
        if (toggle) toggle.addEventListener('click', function () { setDrawer(!nav.classList.contains('drawer-open')); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeAll(); setDrawer(false); } });
    }

    var isMac = /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent);
    $$('[data-shortcut]').forEach(function (k) { k.textContent = isMac ? '⌘ K' : 'Ctrl K'; });

    // ── 3. Docs ───────────────────────────────────────────────────────────
    if (window.hljs) {
        if (hljs.getLanguage('php-template')) hljs.registerAliases(['lte'], { languageName: 'php-template' });
        if (hljs.getLanguage('ini')) hljs.registerAliases(['env'], { languageName: 'ini' });
        $$('.prose pre code').forEach(function (block) { hljs.highlightElement(block); });
    }

    function copy(text, el, done) {
        if (!navigator.clipboard) return;
        navigator.clipboard.writeText(text).then(function () {
            el.classList.add('is-copied');
            if (done) done(true);
            setTimeout(function () { el.classList.remove('is-copied'); if (done) done(false); }, 1600);
        });
    }
    $$('.prose pre').forEach(function (pre) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'copy-btn';
        btn.textContent = T.copy;
        btn.addEventListener('click', function () {
            copy(($('code', pre) || pre).innerText, btn, function (on) { btn.textContent = on ? T.copied : T.copy; });
        });
        pre.appendChild(btn);
    });
    $$('[data-copy-text]').forEach(function (btn) { btn.addEventListener('click', function () { copy(btn.dataset.copyText, btn); }); });

    var sidebar = $('[data-sidebar]');
    $$('[data-toggle-sidebar]').forEach(function (btn) {
        btn.addEventListener('click', function () { if (sidebar) sidebar.classList.toggle('open'); });
    });

    var outline = $$('.outline a');
    if (outline.length && 'IntersectionObserver' in window) {
        var map = {};
        outline.forEach(function (a) { map[decodeURIComponent(a.getAttribute('href').slice(1))] = a; });
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && map[entry.target.id]) {
                    outline.forEach(function (a) { a.classList.remove('current'); });
                    map[entry.target.id].classList.add('current');
                }
            });
        }, { rootMargin: '0px 0px -70% 0px' });
        Object.keys(map).forEach(function (id) { var el = document.getElementById(id); if (el) io.observe(el); });
    }

    (function search() {
        var modal = $('[data-search-modal]');
        if (!modal) return;
        var input = $('[data-search-input]', modal), list = $('[data-search-results]', modal);
        var locale = modal.dataset.locale || 'en';
        var index = null, selected = 0;

        function load() {
            if (index) return Promise.resolve(index);
            return fetch('/' + locale + '/search.json').then(function (r) { return r.json(); }).then(function (data) {
                index = data.map(function (e) { e.hay = (e.title + ' ' + e.page + ' ' + e.text).toLowerCase(); e.t = e.title.toLowerCase(); return e; });
                return index;
            });
        }
        function esc(s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
        function mark(text, terms) {
            var out = esc(text);
            terms.forEach(function (t) {
                if (t.length < 2) return;
                out = out.replace(new RegExp('(' + t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig'), '<mark>$1</mark>');
            });
            return out;
        }
        function snippet(text, term) {
            var i = text.toLowerCase().indexOf(term);
            if (i < 0) return text.slice(0, 110);
            var start = Math.max(0, i - 40);
            return (start > 0 ? '…' : '') + text.slice(start, start + 120);
        }
        function render() {
            var q = input.value.trim().toLowerCase();
            if (!q) { list.innerHTML = ''; return; }
            var terms = q.split(/\s+/);
            var results = index.filter(function (e) { return terms.every(function (t) { return e.hay.indexOf(t) !== -1; }); })
                .map(function (e) { return { e: e, s: (e.t.indexOf(q) === 0 ? 10 : 0) + (e.t.indexOf(q) !== -1 ? 5 : 0) + (e.title === e.page ? 2 : 0) }; })
                .sort(function (a, b) { return b.s - a.s; }).slice(0, 12).map(function (x) { return x.e; });
            selected = 0;
            list.innerHTML = results.length ? results.map(function (e, i) {
                return '<li><a href="' + e.url + '"' + (i === 0 ? ' class="selected"' : '') + '><small>' + esc(e.section + ' › ' + e.page) + '</small>'
                    + mark(e.title, terms) + (e.text ? '<span>' + mark(snippet(e.text, terms[0]), terms) + '</span>' : '') + '</a></li>';
            }).join('') : '<li class="empty">' + esc(modal.dataset.empty || 'No results for') + ' “' + esc(input.value.trim()) + '”</li>';
        }
        function open() { setDrawer(false); modal.hidden = false; input.value = ''; list.innerHTML = ''; input.focus(); load(); }
        function close() { modal.hidden = true; }

        $$('[data-open-search]').forEach(function (b) { b.addEventListener('click', open); });
        $$('[data-close-search]', modal).forEach(function (b) { b.addEventListener('click', close); });
        input.addEventListener('input', function () { load().then(render); });
        input.addEventListener('keydown', function (e) {
            var items = $$('a', list);
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                if (!items.length) return;
                items[selected].classList.remove('selected');
                selected = (selected + (e.key === 'ArrowDown' ? 1 : items.length - 1)) % items.length;
                items[selected].classList.add('selected');
                items[selected].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter' && items[selected]) {
                window.location.href = items[selected].getAttribute('href');
            }
        });
        document.addEventListener('keydown', function (e) {
            var typing = /input|textarea|select/i.test((e.target || {}).tagName || '');
            if (e.key === '/' && !typing) { e.preventDefault(); open(); }
            else if ((e.key === 'k' || e.key === 'K') && (e.ctrlKey || e.metaKey)) { e.preventDefault(); modal.hidden ? open() : close(); }
            else if (e.key === 'Escape' && !modal.hidden) { close(); }
        });
    }());

    // ── 4a. Ecosystem carousel (works without GSAP) ───────────────────────
    $$('[data-carousel]').forEach(function (wrap) {
        var track = $('[data-carousel-track]', wrap), prev = $('[data-carousel-prev]', wrap), next = $('[data-carousel-next]', wrap), count = $('[data-carousel-count]', wrap);
        if (!track) return;
        var cards = $$('[data-carousel-card]', track);
        var stepSize = function () { return cards[0] ? cards[0].getBoundingClientRect().width + 24 : 300; };
        var update = function () {
            var i = Math.round(track.scrollLeft / stepSize());
            if (count) count.textContent = String(Math.min(i + 1, cards.length)).padStart(2, '0');
            if (prev) prev.disabled = track.scrollLeft <= 4;
            if (next) next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        };
        if (prev) prev.addEventListener('click', function () { track.scrollBy({ left: -stepSize(), behavior: 'smooth' }); });
        if (next) next.addEventListener('click', function () { track.scrollBy({ left: stepSize(), behavior: 'smooth' }); });
        track.addEventListener('scroll', function () { window.requestAnimationFrame(update); }, { passive: true });
        update();
    });

    // ── 4b. Stat slider ───────────────────────────────────────────────────
    function format(n) { return Math.round(n).toLocaleString('pt-PT').replace(/ | |\./g, ' '); }
    function countUp(el, duration) {
        var target = parseFloat(el.getAttribute('data-count')) || 0;
        if (!hasGsap || reduced) { el.textContent = format(target); return; }
        var o = { v: 0 };
        gsap.to(o, { v: target, duration: duration || 1.6, ease: 'power2.out', onUpdate: function () { el.textContent = format(o.v); } });
    }
    $$('[data-slider]').forEach(function (slider) {
        var slides = $$('[data-slide]', slider), dots = $$('[data-slide-dot]', slider), label = $('[data-slide-count]', slider);
        var index = 0, timer = 0;
        if (slides.length < 2) return;
        var show = function (i, animate) {
            var from = slides[index];
            index = (i + slides.length) % slides.length;
            var to = slides[index];
            slides.forEach(function (s) { s.setAttribute('aria-hidden', s === to ? 'false' : 'true'); });
            dots.forEach(function (d, k) { d.classList.toggle('is-active', k === index); });
            if (label) label.textContent = String(index + 1).padStart(2, '0');
            if (hasGsap && !reduced && animate && from !== to) {
                gsap.to(from, { autoAlpha: 0, y: -24, duration: .45, ease: 'power2.in' });
                gsap.fromTo(to, { autoAlpha: 0, y: 30 }, { autoAlpha: 1, y: 0, duration: .7, delay: .35, ease: 'power3.out' });
                var num = $('[data-count]', to);
                if (num) countUp(num, 1.1);
            } else {
                slides.forEach(function (s) { s.style.opacity = s === to ? 1 : 0; s.style.visibility = s === to ? 'visible' : 'hidden'; });
            }
        };
        var restart = function () { clearInterval(timer); if (!reduced) timer = setInterval(function () { show(index + 1, true); }, 5500); };
        $$('[data-slide-prev]', slider).forEach(function (b) { b.addEventListener('click', function () { show(index - 1, true); restart(); }); });
        $$('[data-slide-next]', slider).forEach(function (b) { b.addEventListener('click', function () { show(index + 1, true); restart(); }); });
        dots.forEach(function (d, k) { d.addEventListener('click', function () { show(k, true); restart(); }); });
        show(0, false);
        restart();
        if (hasGsap && !reduced && window.ScrollTrigger) {
            ScrollTrigger.create({ trigger: slider, start: 'top 80%', once: true, onEnter: function () { var n = $('[data-count]', slides[index]); if (n) countUp(n, 1.4); } });
        }
    });

    // ── 5. GSAP ───────────────────────────────────────────────────────────
    if (!hasGsap || reduced) {
        $$('[data-reveal]').forEach(function (el) { el.style.opacity = 1; });
        $$('[data-split]').forEach(function (el) { el.style.visibility = 'visible'; });
        return;
    }

    gsap.registerPlugin(ScrollTrigger);
    if (window.SplitText) gsap.registerPlugin(SplitText);

    var fontsReady = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
    fontsReady.then(function () {
        $$('[data-split]').forEach(function (el) {
            if (!window.SplitText) { el.style.visibility = 'visible'; return; }
            var split = SplitText.create(el, { type: 'lines,words', mask: 'lines', linesClass: 'split-line', wordsClass: 'split-word' });
            el.classList.add('is-split');
            el.style.visibility = 'visible';
            var tl = gsap.from(split.words, { yPercent: 115, duration: 1.05, stagger: .045, ease: 'expo.out', paused: el.hasAttribute('data-split-scroll') });
            if (el.hasAttribute('data-split-scroll')) {
                ScrollTrigger.create({ trigger: el, start: 'top 85%', once: true, onEnter: function () { tl.play(); } });
            }
        });
    });

    gsap.set('[data-reveal]', { y: 36 });
    ScrollTrigger.batch('[data-reveal]', {
        start: 'top 88%', once: true,
        onEnter: function (els) { gsap.to(els, { opacity: 1, y: 0, duration: .9, stagger: .09, ease: 'power3.out', overwrite: true }); }
    });

    // Hero stage: entrance, idle float and pointer parallax
    var stage = $('[data-hero-stage]');
    if (stage) {
        var layers = $$('[data-depth]', stage).filter(function (l) { return !l.classList.contains('mk-toast'); });
        gsap.from(layers, { y: 80, opacity: 0, rotateX: 12, duration: 1.4, stagger: .14, ease: 'expo.out', delay: .25 });
        layers.forEach(function (layer, i) {
            gsap.to(layer, { y: '+=' + (8 + i * 3), duration: 3 + i * .7, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 1.6 });
        });
        if (window.matchMedia('(hover: hover)').matches) {
            var qx = layers.map(function (l) { return gsap.quickTo(l, 'x', { duration: .9, ease: 'power3' }); });
            var qr = layers.map(function (l) { return gsap.quickTo(l, 'rotationY', { duration: .9, ease: 'power3' }); });
            stage.closest('section').addEventListener('pointermove', function (e) {
                var nx = e.clientX / window.innerWidth - .5;
                layers.forEach(function (l, i) {
                    var d = parseFloat(l.getAttribute('data-depth')) || 1;
                    qx[i](nx * 30 * d);
                    qr[i](nx * 6 * d);
                });
            });
        }
        var toast = $('.mk-toast', stage);
        if (toast) {
            gsap.timeline({ repeat: -1, repeatDelay: 2.5, delay: 3 })
                .fromTo(toast, { y: -16, opacity: 0, scale: .96 }, { y: 0, opacity: 1, scale: 1, duration: .6, ease: 'back.out(1.6)' })
                .to(toast, { y: -10, opacity: 0, duration: .4, ease: 'power2.in' }, '+=3');
        }
    }

    gsap.from('[data-hero-fade]', { y: 24, opacity: 0, duration: 1, stagger: .12, ease: 'power3.out', delay: .5 });

    // Process: the line draws with scroll, steps light up as it passes
    $$('[data-process]').forEach(function (proc) {
        var line = $('[data-process-line]', proc), steps = $$('[data-process-step]', proc);
        var tl = gsap.timeline({ scrollTrigger: { trigger: proc, start: 'top 75%', end: 'bottom 55%', scrub: .6 } });
        if (line) tl.fromTo(line, { scaleX: 0 }, { scaleX: 1, ease: 'none', duration: steps.length }, 0);
        steps.forEach(function (s, i) {
            tl.call(function () { s.classList.toggle('is-active', tl.scrollTrigger.direction > 0 || tl.progress() * steps.length > i); }, null, i + .01);
            tl.fromTo(s, { opacity: .35 }, { opacity: 1, duration: .5 }, i);
        });
    });

    // Product visuals: subtle tilt on scroll
    $$('[data-tilt]').forEach(function (el) {
        gsap.fromTo(el, { rotateX: 10, y: 40 }, { rotateX: 0, y: 0, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'center center', scrub: true } });
    });

    window.addEventListener('load', function () { ScrollTrigger.refresh(); });
}());
