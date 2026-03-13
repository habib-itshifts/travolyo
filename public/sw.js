/**
 * Travolyo Service Worker
 * Enables PWA installability and basic offline caching.
 */

const CACHE_NAME = 'travolyo-v1';

const STATIC_ASSETS = [
    '/',
    '/assets/images/logo/travolyo-logo.svg',
    '/images/logo.png',
    '/offline.html'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.allSettled(
                STATIC_ASSETS.map(url => cache.add(url).catch(() => {}))
            );
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys
                    .filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/api')) return;

    event.respondWith(
        fetch(event.request)
            .then(response => {
                if (response.ok && url.pathname.match(/\.(css|js|svg|png|jpg|webp|woff2?)$/)) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() => {
                return caches.match(event.request).then(cached => {
                    return cached || caches.match('/offline.html');
                });
            })
    );
});
