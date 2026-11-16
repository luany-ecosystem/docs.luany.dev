(function () {
    'use strict';

    // ── Theme ─────────────────────────────────────────────────────────────
    var root = document.documentElement;
    function setHighlightTheme(theme) {
        var light = document.getElementById('hl-light'), dark = document.getElementById('hl-dark');
        if (!light || !dark) return;
        light.media = theme === 'dark' ? 'not all' : theme === 'light' ? 'all' : '(prefers-color-scheme: light)';
        dark.media = theme === 'dark' ? 'all' : theme === 'light' ? 'not all' : '(prefers-color-scheme: dark)';
    }
    setHighlightTheme(root.dataset.theme);
    document.querySelectorAll('[data-toggle-theme]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var current = root.dataset.theme || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            var next = current === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            setHighlightTheme(next);
            try { localStorage.setItem('luany-docs-theme', next); } catch (e) {}
        });
    });

    // ── Mobile sidebar ────────────────────────────────────────────────────
    var sidebar = document.querySelector('[data-sidebar]');
    document.querySelectorAll('[data-toggle-sidebar]').forEach(function (btn) {
        btn.addEventListener('click', function () { if (sidebar) sidebar.classList.toggle('open'); });
    });

    // ── Code: highlighting (```lte → php-template) and copy buttons ───────
    function highlight() {
        if (!window.hljs) return;
        if (hljs.getLanguage('php-template')) hljs.registerAliases(['lte'], { languageName: 'php-template' });
        if (hljs.getLanguage('ini')) hljs.registerAliases(['env'], { languageName: 'ini' });
        document.querySelectorAll('pre code').forEach(function (block) { hljs.highlightElement(block); });
    }
    window.addEventListener('load', highlight);

    document.querySelectorAll('.prose pre').forEach(function (pre) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'copy-btn';
        btn.textContent = 'Copy';
        btn.addEventListener('click', function () {
            var code = pre.querySelector('code');
            navigator.clipboard.writeText(code ? code.innerText : pre.innerText).then(function () {
                btn.textContent = 'Copied';
                setTimeout(function () { btn.textContent = 'Copy'; }, 1500);
            });
        });
        pre.appendChild(btn);
    });

    // ── "On this page": highlight the current section ─────────────────────
    var links = Array.prototype.slice.call(document.querySelectorAll('.outline a'));
    if (links.length && 'IntersectionObserver' in window) {
        var map = {};
        links.forEach(function (a) { map[a.getAttribute('href').slice(1)] = a; });
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && map[entry.target.id]) {
                    links.forEach(function (a) { a.classList.remove('current'); });
                    map[entry.target.id].classList.add('current');
                }
            });
        }, { rootMargin: '0px 0px -70% 0px' });
        Object.keys(map).forEach(function (id) { var el = document.getElementById(id); if (el) observer.observe(el); });
    }

    // ── Search ────────────────────────────────────────────────────────────
    var modal = document.querySelector('[data-search-modal]');
    if (!modal) return;
    var input = modal.querySelector('[data-search-input]');
    var list = modal.querySelector('[data-search-results]');
    var locale = modal.dataset.locale || 'en';
    var index = null, selected = 0, current = [];

    function load() {
        if (index) return Promise.resolve(index);
        return fetch('/' + locale + '/search.json').then(function (r) { return r.json(); }).then(function (data) {
            index = data.map(function (e) {
                return Object.assign(e, { hay: (e.title + ' ' + e.page + ' ' + e.text).toLowerCase(), t: e.title.toLowerCase() });
            });
            return index;
        });
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; });
    }

    function render() {
        var q = input.value.trim().toLowerCase();
        if (!q) { list.innerHTML = ''; current = []; return; }
        var terms = q.split(/\s+/);
        current = index.filter(function (e) {
            return terms.every(function (t) { return e.hay.indexOf(t) !== -1; });
        }).map(function (e) {
            var score = (e.t.indexOf(q) === 0 ? 10 : 0) + (e.t.indexOf(q) !== -1 ? 5 : 0) + (e.title === e.page ? 2 : 0);
            return { e: e, score: score };
        }).sort(function (a, b) { return b.score - a.score; }).slice(0, 12).map(function (x) { return x.e; });

        selected = 0;
        list.innerHTML = current.length ? current.map(function (e, i) {
            return '<li><a href="' + e.url + '"' + (i === 0 ? ' class="selected"' : '') + '>' + escapeHtml(e.title)
                + '<small>' + escapeHtml(e.section + ' › ' + e.page) + '</small></a></li>';
        }).join('') : '<li class="empty">' + (locale === 'pt' ? 'Sem resultados' : 'No results') + '</li>';
    }

    function open() {
        modal.hidden = false;
        input.value = '';
        list.innerHTML = '';
        load().then(function () { input.focus(); });
        input.focus();
    }
    function close() { modal.hidden = true; }

    document.querySelectorAll('[data-open-search]').forEach(function (b) { b.addEventListener('click', open); });
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    input.addEventListener('input', function () { load().then(render); });
    input.addEventListener('keydown', function (e) {
        var items = list.querySelectorAll('a');
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
        var typing = /input|textarea/i.test((e.target || {}).tagName || '');
        if (e.key === '/' && !typing) { e.preventDefault(); open(); }
        else if ((e.key === 'k' || e.key === 'K') && (e.ctrlKey || e.metaKey)) { e.preventDefault(); open(); }
        else if (e.key === 'Escape' && !modal.hidden) { close(); }
    });
}());
