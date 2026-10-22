const CACHE = 'cg-offline-v3';
const NAVIGATION_TIMEOUT_MS = 10_000;

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

// Only page loads (GET navigations) get the offline fallback. Nothing personal is cached.
self.addEventListener('fetch', (event) => {
    const req = event.request;
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
