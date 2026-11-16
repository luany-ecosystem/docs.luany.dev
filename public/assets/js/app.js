(function () {
    'use strict';

    var root = document.documentElement;
    var $ = function (s, c) { return (c || document).querySelector(s); };
    var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };
    var script = document.currentScript || $('script[data-copy]');
    var T = {
        copy: (script && script.dataset.copy) || 'Copy',
        copied: (script && script.dataset.copied) || 'Copied'
    };
    var isMac = /Mac|iPhone|iPad/.test(navigator.platform || navigator.userAgent);

    // ── Theme (sun / moon) ────────────────────────────────────────────────
    function applyHighlightTheme(theme) {
        var light = $('#hl-light'), dark = $('#hl-dark');
        if (light) light.media = theme === 'dark' ? 'not all' : 'all';
        if (dark) dark.media = theme === 'dark' ? 'all' : 'not all';
    }
    function labelThemeButtons(theme) {
        $$('[data-toggle-theme]').forEach(function (btn) {
            btn.setAttribute('aria-label', theme === 'dark' ? btn.dataset.labelLight : btn.dataset.labelDark);
            btn.title = btn.getAttribute('aria-label');
        });
    }
    applyHighlightTheme(root.dataset.theme);
    labelThemeButtons(root.dataset.theme);
    $$('[data-toggle-theme]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            applyHighlightTheme(next);
            labelThemeButtons(next);
            try { localStorage.setItem('luany-docs-theme', next); } catch (e) {}
        });
    });

    // ── Top bar: border once the page scrolls ─────────────────────────────
    var topbar = $('[data-topbar]');
    function onScroll() { if (topbar) topbar.classList.toggle('is-scrolled', window.scrollY > 8); }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Shortcut hint: ⌘K on macOS
    $$('[data-shortcut]').forEach(function (k) { k.textContent = isMac ? '⌘ K' : 'Ctrl K'; });

    // ── Mobile sidebar ────────────────────────────────────────────────────
    var sidebar = $('[data-sidebar]');
    $$('[data-toggle-sidebar]').forEach(function (btn) {
        btn.addEventListener('click', function () { if (sidebar) sidebar.classList.toggle('open'); });
    });

    // ── Code highlighting (```lte → php-template, ```env → ini) ──────────
    function highlight() {
        if (!window.hljs) return;
        if (hljs.getLanguage('php-template')) hljs.registerAliases(['lte'], { languageName: 'php-template' });
        if (hljs.getLanguage('ini')) hljs.registerAliases(['env'], { languageName: 'ini' });
        $$('pre code').forEach(function (block) {
            if (!block.dataset.highlighted) hljs.highlightElement(block);
        });
    }
    if (window.hljs) highlight(); else window.addEventListener('load', highlight);

    // ── Copy ──────────────────────────────────────────────────────────────
    function copy(text, el, done) {
        var finish = function () {
            el.classList.add('is-copied');
            if (done) done(true);
            setTimeout(function () { el.classList.remove('is-copied'); if (done) done(false); }, 1600);
        };
        if (navigator.clipboard) navigator.clipboard.writeText(text).then(finish);
    }
    $$('.prose pre').forEach(function (pre) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'copy-btn';
        btn.textContent = T.copy;
        btn.addEventListener('click', function () {
            var code = $('code', pre);
            copy((code || pre).innerText, btn, function (on) { btn.textContent = on ? T.copied : T.copy; });
        });
        pre.appendChild(btn);
    });
    $$('[data-copy-text]').forEach(function (btn) {
        btn.addEventListener('click', function () { copy(btn.dataset.copyText, btn); });
    });

    // ── Code tabs (landing) ───────────────────────────────────────────────
    $$('[data-tabs]').forEach(function (box) {
        var tabs = $$('[role="tab"]', box);
        var indicator = $('[data-tab-indicator]', box);
        function moveIndicator(tab) {
            if (!indicator) return;
            indicator.style.width = tab.offsetWidth + 'px';
            indicator.style.transform = 'translateX(' + (tab.offsetLeft - 5) + 'px)';
            indicator.style.left = '5px';
        }
        function select(tab, focus) {
            tabs.forEach(function (t) {
                var on = t === tab;
                t.setAttribute('aria-selected', on ? 'true' : 'false');
                t.tabIndex = on ? 0 : -1;
                var pane = document.getElementById(t.getAttribute('aria-controls'));
                if (pane) pane.hidden = !on;
            });
            moveIndicator(tab);
            if (focus) tab.focus();
            var pane = document.getElementById(tab.getAttribute('aria-controls'));
            if (pane && window.gsap && root.classList.contains('motion')) {
                gsap.fromTo($$('pre', pane), { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: .45, ease: 'power3.out' });
            }
        }
        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { select(tab); });
            tab.addEventListener('keydown', function (e) {
                if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
                e.preventDefault();
                select(tabs[(i + (e.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length], true);
            });
        });
        var current = tabs.filter(function (t) { return t.getAttribute('aria-selected') === 'true'; })[0] || tabs[0];
        if (current) {
            requestAnimationFrame(function () { moveIndicator(current); });
            window.addEventListener('resize', function () {
                moveIndicator(tabs.filter(function (t) { return t.getAttribute('aria-selected') === 'true'; })[0]);
            });
            if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { moveIndicator(current); });
        }
    });

    // ── "On this page": current section ───────────────────────────────────
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

    // ── Search ────────────────────────────────────────────────────────────
    (function () {
        var modal = $('[data-search-modal]');
        if (!modal) return;
        var input = $('[data-search-input]', modal);
        var list = $('[data-search-results]', modal);
        var locale = modal.dataset.locale || 'en';
        var index = null, selected = 0;

        function load() {
            if (index) return Promise.resolve(index);
            return fetch('/' + locale + '/search.json').then(function (r) { return r.json(); }).then(function (data) {
                index = data.map(function (e) {
                    e.hay = (e.title + ' ' + e.page + ' ' + e.text).toLowerCase();
                    e.t = e.title.toLowerCase();
                    return e;
                });
                return index;
            });
        }
        function esc(s) {
            return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
        }
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
            var results = index.filter(function (e) {
                return terms.every(function (t) { return e.hay.indexOf(t) !== -1; });
            }).map(function (e) {
                return { e: e, score: (e.t.indexOf(q) === 0 ? 10 : 0) + (e.t.indexOf(q) !== -1 ? 5 : 0) + (e.title === e.page ? 2 : 0) };
            }).sort(function (a, b) { return b.score - a.score; }).slice(0, 12).map(function (x) { return x.e; });

            selected = 0;
            list.innerHTML = results.length ? results.map(function (e, i) {
                return '<li><a href="' + e.url + '"' + (i === 0 ? ' class="selected"' : '') + '>'
                    + '<small>' + esc(e.section + ' › ' + e.page) + '</small>'
                    + mark(e.title, terms)
                    + (e.text ? '<span>' + mark(snippet(e.text, terms[0]), terms) + '</span>' : '')
                    + '</a></li>';
            }).join('') : '<li class="empty">' + esc(modal.dataset.empty || 'No results for') + ' “' + esc(input.value.trim()) + '”</li>';
        }
        function open() {
            modal.hidden = false;
            input.value = '';
            list.innerHTML = '';
            input.focus();
            load();
        }
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
            var typing = /input|textarea|select/i.test((e.target || {}).tagName || '') || (e.target && e.target.isContentEditable);
            if (e.key === '/' && !typing) { e.preventDefault(); open(); }
            else if ((e.key === 'k' || e.key === 'K') && (e.ctrlKey || e.metaKey)) { e.preventDefault(); modal.hidden ? open() : close(); }
            else if (e.key === 'Escape' && !modal.hidden) { close(); }
        });
    }());

    // ── Landing animations (GSAP) ─────────────────────────────────────────
    function revealAll() {
        $$('[data-reveal]').forEach(function (el) { el.style.opacity = 1; });
        $$('[data-split]').forEach(function (el) { el.style.visibility = 'visible'; });
        root.classList.remove('motion');
    }

    function animate() {
        if (!root.classList.contains('motion')) return;
        if (!window.gsap || !window.ScrollTrigger) { revealAll(); return; }

        gsap.registerPlugin(ScrollTrigger);
        if (window.SplitText) gsap.registerPlugin(SplitText);

        // Headlines: words rise out of a mask
        var fontsReady = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
        fontsReady.then(function () {
            $$('[data-split]').forEach(function (el) {
                if (!window.SplitText) { el.style.visibility = 'visible'; return; }
                var split = SplitText.create(el, { type: 'lines,words', mask: 'lines', linesClass: 'split-line' });
                el.style.visibility = 'visible';
                var tl = gsap.from(split.words, {
                    yPercent: 115, duration: 1.05, stagger: .045, ease: 'expo.out',
                    paused: el.hasAttribute('data-split-scroll'), delay: el.hasAttribute('data-split-scroll') ? 0 : .1
                });
                if (el.hasAttribute('data-split-scroll')) {
                    ScrollTrigger.create({ trigger: el, start: 'top 86%', once: true, onEnter: function () { tl.play(); } });
                }
            });
        });

        // Batched reveals
        gsap.set('[data-reveal]', { y: 34 });
        ScrollTrigger.batch('[data-reveal]', {
            start: 'top 88%', once: true,
            onEnter: function (els) { gsap.to(els, { opacity: 1, y: 0, duration: .9, stagger: .08, ease: 'power3.out', overwrite: true }); }
        });

        // Hero copy
        gsap.from('[data-hero-fade]', { y: 22, opacity: 0, duration: 1, stagger: .12, ease: 'power3.out', delay: .45 });

        // Hero stage: entrance, idle float, pointer parallax
        var stage = $('[data-hero-stage]');
        if (stage) {
            var layers = $$('[data-depth]', stage);
            gsap.from($('.stage-logo', stage), { scale: .82, opacity: 0, duration: 1.6, ease: 'expo.out', delay: .15 });
            gsap.from($$('.float-card', stage), { y: 70, opacity: 0, rotateX: 14, duration: 1.4, stagger: .16, ease: 'expo.out', delay: .55 });
            gsap.from($$('.stage-orbit', stage), { scale: .7, opacity: 0, duration: 2, stagger: .2, ease: 'expo.out', delay: .3 });
            $$('.float-card', stage).forEach(function (card, i) {
                gsap.to(card, { y: '+=' + (10 + i * 4), duration: 3.2 + i * .7, ease: 'sine.inOut', yoyo: true, repeat: -1, delay: 2 });
            });
            if (window.matchMedia('(hover: hover)').matches) {
                var qx = layers.map(function (l) { return gsap.quickTo(l, 'x', { duration: 1, ease: 'power3' }); });
                var qy = layers.map(function (l) { return gsap.quickTo(l, 'rotationY', { duration: 1, ease: 'power3' }); });
                stage.closest('section').addEventListener('pointermove', function (e) {
                    var nx = e.clientX / window.innerWidth - .5;
                    layers.forEach(function (l, i) {
                        var d = parseFloat(l.getAttribute('data-depth')) || 1;
                        qx[i](nx * 26 * d);
                        qy[i](nx * 7 * d);
                    });
                });
            }
        }

        // Glow parallax
        $$('[data-parallax]').forEach(function (el) {
            var amount = parseFloat(el.getAttribute('data-parallax')) || .15;
            gsap.to(el, { yPercent: amount * 100, ease: 'none', scrollTrigger: { trigger: el.closest('section'), start: 'top top', end: 'bottom top', scrub: true } });
        });

        // Code window tilts into place
        $$('[data-tilt]').forEach(function (el) {
            gsap.fromTo(el, { rotateX: 9, y: 30 }, { rotateX: 0, y: 0, ease: 'none', scrollTrigger: { trigger: el, start: 'top bottom', end: 'center center', scrub: true } });
        });

        // Terminal lines type in
        $$('[data-typer]').forEach(function (el) {
            gsap.from($$('.t-line', el), {
                opacity: 0, x: -8, duration: .35, stagger: .22, ease: 'power2.out',
                scrollTrigger: { trigger: el, start: 'top 85%', once: true }
            });
        });

        // Steps: line draws with scroll, steps light up
        $$('[data-process]').forEach(function (proc) {
            var line = $('[data-process-line]', proc);
            var steps = $$('[data-process-step]', proc);
            var tl = gsap.timeline({ scrollTrigger: { trigger: proc, start: 'top 75%', end: 'bottom 60%', scrub: .6 } });
            if (line) tl.fromTo(line, { scaleX: 0 }, { scaleX: 1, ease: 'none', duration: steps.length }, 0);
            steps.forEach(function (s, i) {
                tl.call(function () { s.classList.toggle('is-active', tl.scrollTrigger.direction > 0 || tl.progress() * steps.length > i); }, null, i + .01);
                tl.fromTo(s, { opacity: .4 }, { opacity: 1, duration: .5 }, i);
            });
        });

        window.addEventListener('load', function () { ScrollTrigger.refresh(); });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', animate);
    else animate();
    // Safety net: never leave content hidden if something failed to load.
    setTimeout(function () { if (root.classList.contains('motion') && !window.gsap) revealAll(); }, 2500);
}());
