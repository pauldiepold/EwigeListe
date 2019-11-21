let CACHE_NAME = 'ewige-liste-cache-v1';
const FILES_TO_CACHE = [
    '/offline.html',
    '/webfonts/OpenSans-Regular.ttf',
    '/webfonts/Raleway-Regular.ttf',
    '/img/offline.png',
    '/img/favicon_32x32.png',
    '/img/favicon_192x192.png'
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function (cache) {
                return cache.addAll(FILES_TO_CACHE);
            })
    );
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', function (event) {
    event.respondWith(
        fetch(event.request)
            .catch(() => {
                return caches.open(CACHE_NAME)
                    .then((cache) => {
                        return cache.match('offline.html');
                    });
            })
    );
});
