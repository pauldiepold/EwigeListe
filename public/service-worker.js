/*let CACHE_NAME = 'my-site-cache-v1';
const FILES_TO_CACHE = [
    '/offline.html',
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Opened cache');
                return cache.addAll(FILES_TO_CACHE);
            })
    );
});
 */

self.addEventListener('install', (e) => {
    console.log('[Service Worker] Install');
});
