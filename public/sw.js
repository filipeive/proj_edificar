const CACHE_NAME = 'life-church-v2';
const PRECACHE_URLS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/favicon.png',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/images/logo.png',
    '/images/logo-white-orange.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only intercept GET requests and http/https protocols (ignore POST, PUT, DELETE, Chrome extensions, etc.)
    if (request.method !== 'GET' || !request.url.startsWith('http')) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    // Only cache successful standard responses
                    if (response.status === 200) {
                        const copy = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
                .catch(() => {
                    return caches.match(request).then((cached) => cached || caches.match('/offline.html'));
                })
        );
        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            return cached || fetch(request).then((response) => {
                // Only cache successful standard responses
                if (response.status === 200) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                }
                return response;
            });
        })
    );
});

