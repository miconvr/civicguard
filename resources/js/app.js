import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Sample page timings without collecting URLs, account identifiers, or form data.
(function initializeSampledWebVitals() {
    if (Math.random() >= 0.1 || !('PerformanceObserver' in window)) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const route = document.body.dataset.cgRoute;
    if (!csrf || !route) return;

    const metrics = {};
    let sent = false;
    let cls = 0;
    let inp = 0;
    const navigation = performance.getEntriesByType('navigation')[0];
    if (navigation) metrics.ttfb_ms = Math.max(0, navigation.responseStart - navigation.requestStart);

    const observe = (type, callback) => {
        try {
            new PerformanceObserver(list => callback(list.getEntries())).observe({ type, buffered: true });
        } catch {}
    };

    observe('largest-contentful-paint', entries => {
        const entry = entries.at(-1);
        if (entry) metrics.lcp_ms = entry.startTime;
    });
    observe('layout-shift', entries => {
        entries.forEach(entry => {
            if (!entry.hadRecentInput) cls += entry.value;
        });
        metrics.cls = cls;
    });
    observe('event', entries => {
        entries.forEach(entry => {
            if (entry.interactionId) inp = Math.max(inp, entry.duration);
        });
        if (inp) metrics.inp_ms = inp;
    });

    const send = () => {
        if (sent || Object.keys(metrics).length === 0) return;
        sent = true;
        fetch('/performance-metrics', {
            method: 'POST',
            keepalive: true,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ route, metrics }),
        }).catch(() => {});
    };

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') send();
    }, { once: true });
    window.addEventListener('pagehide', send, { once: true });
})();

// Keep the browser's install prompt so the landing page can offer it later.
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    window.__cgInstallPrompt = e;
    window.dispatchEvent(new Event('cg-install-ready'));
});

function initializeScrollReveals() {
    const elements = document.querySelectorAll('[data-reveal]');

    if (!elements.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    document.documentElement.classList.add('cg-motion-ready');

    const observer = new IntersectionObserver((entries, currentObserver) => {
        let order = 0;
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Cards that appear together fade in one after another.
                if (!getComputedStyle(entry.target).getPropertyValue('--cg-reveal-delay').trim()) {
                    entry.target.style.setProperty('--cg-reveal-delay', Math.min(order * 70, 350) + 'ms');
                }
                order += 1;
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -32px 0px', threshold: 0.08 });

    elements.forEach(element => observer.observe(element));

    document.addEventListener('focusin', event => {
        const element = event.target.closest('[data-reveal]');
        if (element) {
            element.classList.add('cg-motion-immediate');
            element.classList.add('is-visible');
            observer.unobserve(element);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeScrollReveals, { once: true });
} else {
    initializeScrollReveals();
}

// cg-dashboard-motion: report totals count up when they scroll into view
function initializeCountUps() {
    const nodes = document.querySelectorAll('.cg-kpi');

    if (!nodes.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const run = (el) => {
        const raw = el.textContent.trim();
        if (!/^\d[\d,]*$/.test(raw)) return;

        const target = parseInt(raw.replace(/,/g, ''), 10);
        if (!Number.isFinite(target) || target < 2 || target > 100000) return;

        const withCommas = raw.includes(',');
        const duration = 900;
        const start = performance.now();
        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const value = Math.round(target * (1 - Math.pow(1 - progress, 3)));
            el.textContent = withCommas ? value.toLocaleString('en-US') : String(value);
            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = raw;
            }
        };

        el.textContent = '0';
        requestAnimationFrame(tick);
    };

    const io = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                observer.unobserve(entry.target);
                run(entry.target);
            }
        });
    }, { threshold: 0.4 });

    nodes.forEach(node => io.observe(node));
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCountUps, { once: true });
} else {
    initializeCountUps();
}

Alpine.data('appShell', () => ({
    collapsed: (() => {
        try {
            return localStorage.getItem('sidebarCollapsed') === 'true';
        } catch (error) {
            return false;
        }
    })(),
    toggleSidebar() {
        this.collapsed = !this.collapsed;
        try {
            localStorage.setItem('sidebarCollapsed', this.collapsed);
        } catch (error) {
            return;
        }
    },
}));







const pageModules = [];
if (document.querySelector('[x-data="landingPage"]')) {
    pageModules.push(import('./pages/landing.js').then(module => module.registerPage(Alpine)));
}
if (document.querySelector('[x-data^="verificationTabs("]')) {
    pageModules.push(import('./pages/verification-tabs.js').then(module => module.registerPage(Alpine)));
}
if (document.querySelector('[x-data="profilePhotoEditor"]')) {
    pageModules.push(import('./pages/profile-photo.js').then(module => module.registerPage(Alpine)));
}
Promise.all(pageModules)
    .catch(error => console.error('Unable to load a page component:', error))
    .finally(() => Alpine.start());

// Scroll reveal for the landing page
(function () {
    const run = () => {
        const items = document.querySelectorAll('[data-cg-scroll]');
        if (!items.length) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        if (!('IntersectionObserver' in window)) return;
        items.forEach((el) => {
            const group = el.parentElement
                ? Array.from(el.parentElement.querySelectorAll(':scope > [data-cg-scroll]'))
                : [];
            el.style.setProperty('--cg-delay', Math.max(group.indexOf(el), 0) * 120 + 'ms');
            el.classList.add('cg-scroll');
        });
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' });
        items.forEach((el) => io.observe(el));
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();

// ---- Loading state for forms: buttons marked data-loading-btn ----
const CG_SPINNER = '<svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>';

// Keep form confirmations and filter submits compatible with the production CSP.
document.addEventListener('submit', (event) => {
    const form = event.target;
    const confirmation = form instanceof HTMLFormElement ? form.dataset.confirm : null;

    if (confirmation && !window.confirm(confirmation)) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);

document.addEventListener('change', (event) => {
    const control = event.target.closest('[data-submit-on-change]');
    const form = control?.form;

    if (form) form.requestSubmit();
});

function cgSetBusy(btn) {
    btn.dataset.originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.setAttribute('aria-busy', 'true');
    btn.innerHTML = '<span class="inline-flex items-center gap-2" role="status">' + CG_SPINNER + '<span></span></span>';
    btn.querySelector('span span').textContent = btn.dataset.loadingText || '...';
}
function cgResetBusy() {
    document.querySelectorAll('[data-loading-btn][aria-busy="true"]').forEach((btn) => {
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
    });
}
document.addEventListener('submit', (e) => {
    const btn = e.target.querySelector && e.target.querySelector('[data-loading-btn]');
    if (!btn || e.defaultPrevented) return;
    if (btn.disabled) { e.preventDefault(); return; }
    cgSetBusy(btn);
});
window.addEventListener('pageshow', cgResetBusy);

// ---- Keyboard: keep the active field in view ----
(function () {
    const skip = ['checkbox', 'radio', 'file', 'submit', 'button'];
    const isField = (el) => el && el.matches && el.matches('input,textarea,select') && !skip.includes(el.type);
    const reveal = (el) => el.scrollIntoView({ block: 'center', behavior: 'auto' });
    document.addEventListener('focusin', (e) => {
        if (isField(e.target)) setTimeout(() => reveal(e.target), 300);
    });
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', () => {
            if (isField(document.activeElement)) reveal(document.activeElement);
        });
    }
})();

// ---- Offline banner and submit guard ----
(function () {
    const tl = (document.documentElement.lang || '').toLowerCase().startsWith('tl');
    const text = tl
        ? { msg: 'Walang koneksyon sa internet. Hindi maipapadala ang inyong mga pagbabago hanggang makakonekta kayo muli.', retry: 'Subukan muli', still: 'Offline pa rin' }
        : { msg: "You're offline. Your changes can't be sent until you reconnect.", retry: 'Retry', still: 'Still offline' };
    let banner = null;
    function build() {
        banner = document.createElement('div');
        banner.setAttribute('role', 'status');
        banner.style.cssText = 'position:fixed;left:0;right:0;top:0;z-index:9999;display:none;align-items:center;justify-content:space-between;gap:12px;padding:12px 16px;padding-top:calc(12px + env(safe-area-inset-top,0px));background:#7a1f1f;color:#fff;font-size:14px;';
        const msg = document.createElement('span');
        msg.textContent = text.msg;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = text.retry;
        btn.style.cssText = 'min-height:44px;padding:0 14px;border:1px solid #fff;border-radius:8px;background:transparent;color:#fff;font-weight:600;';
        btn.addEventListener('click', async () => {
            try {
                await fetch('/favicon.svg?cb=' + Date.now(), { cache: 'no-store' });
                hide();
            } catch (err) {
                btn.textContent = text.still;
                setTimeout(() => { btn.textContent = text.retry; }, 2000);
            }
        });
        banner.append(msg, btn);
        document.body.appendChild(banner);
    }
    function show() { if (!banner) build(); banner.style.display = 'flex'; }
    function hide() { if (banner) banner.style.display = 'none'; }
    window.addEventListener('offline', show);
    window.addEventListener('online', hide);
    document.addEventListener('DOMContentLoaded', () => { if (!navigator.onLine) show(); });
    // Capture phase: block submits while offline before other handlers (including the loading spinner) run.
    document.addEventListener('submit', (e) => {
        if (!navigator.onLine) {
            e.preventDefault();
            e.stopImmediatePropagation();
            show();
        }
    }, true);
})();

// ---- Service worker: friendly offline page for page loads only ----
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

// cgInstallObserver: play the install-guide animation only while it is on screen
(function () {
    const run = () => {
        const el = document.getElementById('install-guide');
        if (!el) return;
        if (!('IntersectionObserver' in window)) { el.classList.add('is-playing'); return; }
        new IntersectionObserver((entries) => {
            entries.forEach((e) => el.classList.toggle('is-playing', e.isIntersecting));
        }, { threshold: 0.2 }).observe(el);
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run, { once: true });
    } else {
        run();
    }
})();

// cgNavReveal: show the public header as the emergency guidance section enters view.
(function () {
    const emergency = document.querySelector('.cg-emergency-strip');
    const header = document.querySelector('.cg-public-header');
    if (!emergency || !header) return;
    const root = document.documentElement;
    root.classList.add('cg-nav-js');
    let ticking = false;
    const update = () => {
        ticking = false;
        // On phones, reveal a little later so the header does not cover the
        // emergency message as soon as it enters a short viewport.
        // Keep it visible after that point, and hide it again when scrolling above it.
        const viewportHeight = window.innerHeight;
        const isMobile = window.matchMedia('(max-width: 639px)').matches;
        const revealLine = isMobile
            ? Math.min(viewportHeight * 0.62, viewportHeight - 140)
            : viewportHeight * 0.7;

        root.classList.toggle('cg-nav-on', emergency.getBoundingClientRect().top <= revealLine);
    };
    const request = () => {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
    };
    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request);
    // iOS can restore this page from the back-forward cache without rerunning
    // initialization or firing a scroll event. Recompute the navbar state
    // after the restored scroll position is applied.
    window.addEventListener('pageshow', update);
    update();
})();
