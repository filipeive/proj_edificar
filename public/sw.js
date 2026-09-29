const CACHE_NAME = 'life-app-v3';
const PRECACHE_URLS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/favicon.png',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/images/logo.png',
];

// Install Event - Pre-cache core shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

// Activate Event - Clean old caches
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

// Fetch Event - Network-First for Navigation, Cache-First for Assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only intercept GET requests
    if (request.method !== 'GET' || !request.url.startsWith('http')) {
        return;
    }

    // Skip API, search, export, pdf, and auth routes from offline caching
    const url = new URL(request.url);
    if (url.pathname.includes('/api/') || 
        url.pathname.includes('/pdf') || 
        url.pathname.includes('/export') || 
        url.pathname.includes('/login') || 
        url.pathname.includes('/logout')) {
        return;
    }

    // Navigation (HTML Pages): Network first, fallback to Cache, fallback to /offline.html
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
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

    // Static Assets (CSS, JS, Fonts, Images): Cache first, fallback to network
    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) return cached;
            return fetch(request).then((response) => {
                if (response.status === 200) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                }
                return response;
            });
        })
    );
});
