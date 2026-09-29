const CACHE_NAME = 'life-app-v4';
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

// ===== NATIVE PUSH NOTIFICATION EVENT LISTENERS =====
self.addEventListener('push', (event) => {
    let data = { title: 'Portal Life Church', message: 'Você tem uma nova notificação.' };
    try {
        if (event.data) {
            data = event.data.json();
        }
    } catch (e) {
        data.message = event.data ? event.data.text() : data.message;
    }

    const title = data.title || 'Portal Life Church';
    const options = {
        body: data.message || data.body || 'Nova atualização no sistema.',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        vibrate: [200, 100, 200],
        tag: 'life-church-notification',
        renotify: true,
        data: {
            url: data.link || data.url || '/notifications'
        }
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url || '/notifications';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

// Fetch Event - Network-First for Navigation, Cache-First for Assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET' || !request.url.startsWith('http')) {
        return;
    }

    const url = new URL(request.url);
    if (url.pathname.includes('/api/') || 
        url.pathname.includes('/pdf') || 
        url.pathname.includes('/export') || 
        url.pathname.includes('/login') || 
        url.pathname.includes('/logout')) {
        return;
    }

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
