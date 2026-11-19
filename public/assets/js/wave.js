/**
 * Luany — Flowing line waves (canvas 2D)
 *
 * <canvas class="wave-canvas" data-wave="hero|band"></canvas>
 *
 * A ribbon of thin sine lines in the colours of the logo's crescent
 * (purple → coral → orange) that slowly flows. Renders only while
 * visible; one static frame when the user prefers reduced motion.
 */
(function () {
    'use strict';

    var PRESETS = {
        // Diagonal ribbon rising from bottom-left to top-right (hero)
        hero: {
            lines: 72, amp: 46, freq: 0.0032, speed: 0.00022, spread: 1.6, width: 1,
            base: function (x) { return 0.98 - 0.78 * Math.pow(x, 0.9); },
            colors: ['#7A45A3', '#E4613E', '#FD962F'], alpha: 0.5
        },
        // Low horizontal dunes (stat band, CTA)
        band: {
            lines: 60, amp: 34, freq: 0.0024, speed: 0.00018, spread: 1.4, width: 1,
            base: function (x) { return 0.78 - 0.08 * Math.sin(x * Math.PI * 1.4); },
            colors: ['#7A45A3', '#E9714D', '#FD962F'], alpha: 0.45
        }
    };

    var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function Wave(canvas) {
        this.canvas = canvas;
        this.ctx = canvas.getContext('2d');
        this.cfg = PRESETS[canvas.getAttribute('data-wave')] || PRESETS.hero;
        this.visible = false;
        this.raf = 0;
        this.resize();
        var self = this;
        window.addEventListener('resize', function () { self.resize(); self.draw(performance.now()); });
        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (entries) {
                self.visible = entries[0].isIntersecting;
                if (self.visible && !reduced) self.loop(); else cancelAnimationFrame(self.raf);
            }).observe(canvas);
        }
        this.draw(1200);
    }

    Wave.prototype.resize = function () {
        var dpr = Math.min(window.devicePixelRatio || 1, 2);
        var r = this.canvas.getBoundingClientRect();
        this.w = r.width; this.h = r.height;
        this.canvas.width = Math.round(r.width * dpr);
        this.canvas.height = Math.round(r.height * dpr);
        this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        var g = this.ctx.createLinearGradient(0, this.h, this.w, 0);
        var c = this.cfg.colors;
        g.addColorStop(0, c[0]); g.addColorStop(0.5, c[1]); g.addColorStop(1, c[2]);
        this.gradient = g;
    };

    Wave.prototype.loop = function () {
        var self = this;
        cancelAnimationFrame(this.raf);
        (function tick(t) {
            self.draw(t);
            if (self.visible) self.raf = requestAnimationFrame(tick);
        }(performance.now()));
    };

    Wave.prototype.draw = function (t) {
        var ctx = this.ctx, cfg = this.cfg, w = this.w, h = this.h;
        if (!w || !h) return;
        ctx.clearRect(0, 0, w, h);
        ctx.globalCompositeOperation = 'lighter';
        ctx.lineWidth = cfg.width;
        ctx.strokeStyle = this.gradient;
        var step = Math.max(6, w / 180);
        for (var i = 0; i < cfg.lines; i++) {
            var p = i / cfg.lines;
            ctx.globalAlpha = cfg.alpha * (0.25 + 0.75 * Math.sin(p * Math.PI));
            ctx.beginPath();
            for (var x = -step; x <= w + step; x += step) {
                var xr = x / w;
                var y = h * cfg.base(xr)
                    + Math.sin(x * cfg.freq + t * cfg.speed + p * 2.4) * cfg.amp * (0.6 + p)
                    + Math.sin(x * cfg.freq * 0.5 - t * cfg.speed * 0.7 + p * 5) * cfg.amp * 0.5
                    + (p - 0.5) * cfg.spread * 60;
                if (x === -step) ctx.moveTo(x, y); else ctx.lineTo(x, y);
            }
            ctx.stroke();
        }
        ctx.globalAlpha = 1;
        ctx.globalCompositeOperation = 'source-over';
    };

    function init() {
        document.querySelectorAll('canvas[data-wave]').forEach(function (c) {
            if (!c.__wave) c.__wave = new Wave(c);
        });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
