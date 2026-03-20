// Minimaler Service Worker – nur für PWA-Installierbarkeit auf Mobilgeräten.
// Kein Caching, keine Offline-Funktionalität.

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', () => self.clients.claim());
