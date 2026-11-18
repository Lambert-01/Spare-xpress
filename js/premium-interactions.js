/* =====================================================================
   SPARE XPRESS — Premium interactions (Apple-store inspired)
   Scroll-reveal (fade + rise), staggered, plus smooth anchor scrolling.
   Hover lift / image zoom are handled in CSS (theme.css).
   Respects prefers-reduced-motion.
   ===================================================================== */
(function () {
    'use strict';

    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Selectors that should animate into view.
    var TARGETS = [
        '.product-card',
        '.spx-brand-card',
        '.spx-cat-card',
        '.spx-how-card',
        '.spx-section-head',
        '.spx-trust-item',
        '[data-reveal]'
    ].join(',');

    var io = null;
    if (!reduce && 'IntersectionObserver' in window) {
        io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    }

    function prepare(el, index) {
        if (el.classList.contains('reveal-up')) return;
        el.classList.add('reveal-up');
        // Stagger items that sit in the same row/grid for a cascading feel.
        var delay = Math.min((index % 8) * 60, 420);
        el.style.setProperty('--reveal-delay', delay + 'ms');
        if (io) {
            io.observe(el);
        } else {
            el.classList.add('is-visible'); // no IO / reduced motion -> show immediately
        }
    }

    function scan(root) {
        var nodes = (root || document).querySelectorAll(TARGETS);
        nodes.forEach(prepare);
    }

    document.addEventListener('DOMContentLoaded', function () {
        scan(document);

        // Products on the shop page are injected after an async fetch; catch them.
        var mo = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.matches && node.matches(TARGETS)) prepare(node, 0);
                    if (node.querySelectorAll) scan(node);
                });
            });
        });
        mo.observe(document.body, { childList: true, subtree: true });

        // Smooth scroll for in-page anchors.
        document.querySelectorAll('a[href^="#"]').forEach(function (a) {
            a.addEventListener('click', function (e) {
                var id = a.getAttribute('href');
                if (id.length < 2) return;
                var target = document.querySelector(id);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
                }
            });
        });
    });
})();
