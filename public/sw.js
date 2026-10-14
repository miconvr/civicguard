const CACHE = 'cg-offline-v1';

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
    event.respondWith(fetch(req).catch(() => caches.match('/offline.html')));
});
