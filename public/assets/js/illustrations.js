/**
 * Luany — Illustrations (GSAP)
 *
 * Each [data-illo="key"] gets a looping timeline built by SCENES[key].
 * Timelines only run while the illustration is on screen (ScrollTrigger)
 * and are skipped entirely when the user prefers reduced motion.
 */
(function () {
    'use strict';

    if (typeof window.gsap === 'undefined') return;
    if (document.documentElement.classList.contains('reduced-motion')) return;

    gsap.registerPlugin(ScrollTrigger);
    if (window.DrawSVGPlugin) gsap.registerPlugin(DrawSVGPlugin);

    function q(el, sel) { return Array.prototype.slice.call(el.querySelectorAll(sel)); }
    function loop(opts) { return gsap.timeline(Object.assign({ repeat: -1, paused: true, defaults: { ease: 'power3.out' } }, opts || {})); }
    function typing(lines, tl, at) {
        tl.set(lines, { clipPath: 'inset(0 100% 0 0)' }, at || 0)
          .to(lines, { clipPath: 'inset(0 0% 0 0)', duration: .5, stagger: .26, ease: 'steps(16)' });
    }

    var SCENES = {

        // Hero: the editor types the template, the terminal reports the reload
        'hero-code': function (el) {
            var lines = q(el, '.ln');
            var tl = loop({ repeatDelay: 2.4 });
            typing(lines, tl);
            tl.to(lines, { opacity: 0, duration: .4 }, '+=3')
              .set(lines, { opacity: 1 });
            return tl;
        },
        'hero-term': function (el) {
            var lines = q(el, '.ln');
            var tl = loop({ repeatDelay: 1.6, delay: 1.2 });
            tl.set(lines, { opacity: 0, x: -6 })
              .to(lines.slice(0, 3), { opacity: 1, x: 0, duration: .35, stagger: .35 })
              .to(lines[3], { opacity: 1, x: 0, duration: .35 }, '+=1.4')
              .to(lines[4], { opacity: 1, x: 0, duration: .35 }, '+=.5')
              .to(lines.slice(3), { opacity: 0, duration: .3 }, '+=2.2');
            return tl;
        },

        // 01 HTTP: a request walks the middleware stack, a response comes back
        http: function (svg) {
            var packet = q(svg, '.packet')[0], layers = q(svg, '.layer'), res = q(svg, '.res')[0], req = q(svg, '.req')[0];
            var tl = loop({ repeatDelay: .8 });
            tl.fromTo(req, { x: -14, opacity: 0 }, { x: 0, opacity: 1, duration: .5 })
              .set(packet, { attr: { cy: 48 }, opacity: 1 });
            layers.forEach(function (layer) {
                var y = parseFloat(layer.getAttribute('data-y')) + 15;
                var hl = q(layer, '.layer-hl')[0];
                tl.to(packet, { attr: { cy: y }, duration: .55, ease: 'power2.inOut' })
                  .to(hl, { opacity: 1, duration: .2 }, '<.35')
                  .to(hl, { opacity: 0, duration: .5 }, '+=.35');
            });
            tl.to(packet, { attr: { cy: 196 }, duration: .45, ease: 'power2.in' }, '-=.4')
              .to(packet, { opacity: 0, duration: .15 })
              .fromTo(res, { y: 8, opacity: 0 }, { y: 0, opacity: 1, duration: .5, ease: 'back.out(2)' })
              .to([res, req], { opacity: 0, duration: .4 }, '+=1.8');
            return tl;
        },

        // 02 Views: the syntax tree builds, an unclosed block is caught, then fixed
        views: function (svg) {
            var nodes = q(svg, '.node'), edges = q(svg, '.edge');
            var err = q(svg, '.loop-err')[0], errTip = q(svg, '.err-tip')[0], okTip = q(svg, '.ok-tip')[0];
            var tl = loop({ repeatDelay: .6 });
            tl.from(nodes, { y: 10, opacity: 0, duration: .45, stagger: .12 })
              .from(edges, window.DrawSVGPlugin ? { drawSVG: '0%', duration: .6, stagger: .1, ease: 'power2.inOut' } : { opacity: 0, duration: .4 }, '-=.5')
              .to(err, { opacity: 1, duration: .3 }, '+=.4')
              .fromTo(errTip, { y: 6, opacity: 0 }, { y: 0, opacity: 1, duration: .45, ease: 'back.out(2)' }, '<')
              .to(errTip, { opacity: 0, duration: .3 }, '+=1.6')
              .to(err, { opacity: 0, duration: .3 }, '<')
              .fromTo(okTip, { y: 6, opacity: 0 }, { y: 0, opacity: 1, duration: .45, ease: 'back.out(2)' })
              .to([okTip].concat(nodes, edges), { opacity: 0, duration: .4 }, '+=1.6')
              .set(nodes.concat(edges), { opacity: 1 });
            return tl;
        },

        // 03 Data: tables appear, relations draw, the eager-loaded query runs
        data: function (svg) {
            var tbls = q(svg, '.tbl'), rels = q(svg, '.rel'), labels = q(svg, '.rel-label');
            var query = q(svg, '.query')[0], count = q(svg, '.qcount')[0];
            var tl = loop({ repeatDelay: .6 });
            tl.from(tbls, { y: 12, opacity: 0, duration: .5, stagger: .14 })
              .from(rels, window.DrawSVGPlugin ? { drawSVG: '0%', duration: .8, stagger: .2, ease: 'power2.inOut' } : { opacity: 0, duration: .4 })
              .to(labels, { opacity: 1, duration: .3, stagger: .15 }, '-=.3')
              .fromTo(query, { x: -10, opacity: 0 }, { x: 0, opacity: 1, duration: .5 })
              .fromTo(count, { scale: .6, opacity: 0, transformOrigin: '50% 50%' }, { scale: 1, opacity: 1, duration: .5, ease: 'back.out(2.4)' }, '+=.3')
              .to([query, count].concat(labels), { opacity: 0, duration: .4 }, '+=1.8');
            return tl;
        },

        // Tools: CLI writes a view and scaffolds a feature
        'p-cli': function (el) {
            var lines = q(el, 'pre .ln'), term = q(el, '.pv-term .ln');
            var tl = loop({ repeatDelay: 2 });
            tl.set(lines.concat(term), { clipPath: 'inset(0 100% 0 0)' })
              .to(lines, { clipPath: 'inset(0 0% 0 0)', duration: .5, stagger: .28, ease: 'steps(18)' })
              .to(term[0], { clipPath: 'inset(0 0% 0 0)', duration: .8, ease: 'steps(28)' }, '+=.3')
              .to(term.slice(1), { clipPath: 'inset(0 0% 0 0)', duration: .25, stagger: .35, ease: 'power1.out' }, '+=.3')
              .to(lines.concat(term), { opacity: 0, duration: .4 }, '+=2.4')
              .set(lines.concat(term), { opacity: 1, clipPath: 'inset(0 100% 0 0)' });
            return tl;
        },

        // Tools: VS Code flags the error, then it is fixed
        'p-vscode': function (svg) {
            var squiggle = q(svg, '.squiggle'), tip = q(svg, '.hover-tip'), row = q(svg, '.prob-row'), ok = q(svg, '.prob-ok'), badge = q(svg, '.prob-badge');
            var tl = loop({ repeatDelay: 1 });
            tl.set(badge, { opacity: 0 })
              .from(squiggle, window.DrawSVGPlugin ? { drawSVG: '0%', duration: .9, ease: 'power2.inOut' } : { opacity: 0, duration: .4 }, .4)
              .to(badge, { opacity: 1, duration: .2 })
              .fromTo(row, { x: 16, opacity: 0 }, { x: 0, opacity: 1, duration: .45 }, '<')
              .fromTo(tip, { y: -6, opacity: 0 }, { y: 0, opacity: 1, duration: .4, ease: 'back.out(2)' }, '+=.3')
              .to(tip, { opacity: 0, duration: .3 }, '+=1.6')
              .to([squiggle, row, badge].flat(), { opacity: 0, duration: .35 })
              .fromTo(ok, { y: 6, opacity: 0 }, { y: 0, opacity: 1, duration: .45 })
              .to(ok, { opacity: 0, duration: .35 }, '+=1.6')
              .set(squiggle, { opacity: 1 });
            return tl;
        },

        // Tools: CI checks run one after another and pass
        'p-ci': function (svg) {
            var steps = q(svg, '.ci-step'), done = q(svg, '.ci-done')[0];
            var tl = loop({ repeatDelay: 1 });
            steps.forEach(function (step) {
                var bar = q(step, '.ci-bar')[0], spin = q(step, '.ci-spin')[0], ok = q(step, '.ci-ok')[0];
                tl.set(ok, { opacity: 0 }, 0).set(spin, { opacity: 1 }, 0).set(bar, { scaleX: 0, transformOrigin: '0 50%' }, 0);
            });
            steps.forEach(function (step) {
                var bar = q(step, '.ci-bar')[0], spin = q(step, '.ci-spin')[0], ok = q(step, '.ci-ok')[0];
                tl.to(spin, { rotation: 360, svgOrigin: '44 ' + spin.getAttribute('cy'), duration: .9, ease: 'none' })
                  .to(bar, { scaleX: 1, duration: .9, ease: 'power1.inOut' }, '<')
                  .to(spin, { opacity: 0, duration: .15 })
                  .fromTo(ok, { scale: .4, opacity: 0, transformOrigin: '50% 50%' }, { scale: 1, opacity: 1, duration: .35, ease: 'back.out(2.6)' }, '<');
            });
            tl.fromTo(done, { y: 8, opacity: 0 }, { y: 0, opacity: 1, duration: .5, ease: 'back.out(1.8)' }, '+=.2')
              .to(done, { opacity: 0, duration: .4 }, '+=2');
            return tl;
        }
    };

    function init() {
        document.querySelectorAll('[data-illo]').forEach(function (el) {
            var build = SCENES[el.getAttribute('data-illo')];
            if (!build || el.__tl) return;
            var tl = build(el);
            el.__tl = tl;
            ScrollTrigger.create({
                trigger: el,
                start: 'top 92%',
                end: 'bottom 8%',
                onToggle: function (self) { self.isActive ? tl.play() : tl.pause(); }
            });
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
}());
