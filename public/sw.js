const CACHE = 'cg-offline-v4';
const NAVIGATION_TIMEOUT_MS = 10_000;
const IMMUTABLE_ASSET = /^\/build\/assets\/[A-Za-z0-9_-]+-[A-Za-z0-9_-]+\.(css|js|woff2)$/;

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE).then((c) => c.add('/offline.html')));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

// Fingerprinted build files are immutable, so keep them locally for repeat visits.
// HTML, API responses, and user data are never cached by this rule.
self.addEventListener('fetch', (event) => {
    const req = event.request;
    const url = new URL(req.url);

    if (req.method === 'GET' && url.origin === self.location.origin && IMMUTABLE_ASSET.test(url.pathname)) {
        event.respondWith((async () => {
            const cache = await caches.open(CACHE);
            const cached = await cache.match(req);
            if (cached) return cached;

            const response = await fetch(req);
            if (response.ok && response.type === 'basic') await cache.put(req, response.clone());
            return response;
        })());
        return;
    }

    // Locale changes set a session value and redirect back to the current page.
    // Let the browser handle this navigation directly so the offline fallback
    // and navigation timeout cannot interrupt the language switch.
    if (url.origin === self.location.origin && url.pathname.startsWith('/locale/')) return;

    if (req.mode !== 'navigate' || req.method !== 'GET') return;
    event.respondWith((async () => {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), NAVIGATION_TIMEOUT_MS);

        try {
            return await fetch(req, { signal: controller.signal });
        } catch {
            return await caches.match('/offline.html');
        } finally {
            clearTimeout(timeout);
        }
    })());
});
