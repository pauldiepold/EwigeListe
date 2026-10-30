// Minimaler Service Worker – nur für PWA-Installierbarkeit auf Mobilgeräten.
// Kein Caching, keine Offline-Funktionalität.

self.addEventListener('install', () => self.skipWaiting());

// Räumt Caches früherer Service-Worker-Versionen weg (bis v7 wurden Offline-Assets vorgecacht).
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((names) => Promise.all(names.map((name) => caches.delete(name))))
            .then(() => self.clients.claim()),
    );
});
