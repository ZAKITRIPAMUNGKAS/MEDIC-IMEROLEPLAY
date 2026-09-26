const CACHE_NAME = 'ime-medis-cache-v2';
const ASSETS_TO_CACHE = [
  './manifest.json',
  './icon-192.png',
  './icon-512.png',
  './images/IME-RP-LOGO.jpg',
  './images/motionlife-logo.png',
  './images/logo rhv2.png'
];

self.addEventListener('install', event => {
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.map(key => {
          if (key !== CACHE_NAME) {
            return caches.delete(key);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  // Always fetch CSS and JS fresh from network to prevent layout breakages
  if (event.request.url.includes('.css') || event.request.url.includes('.js')) {
    return;
  }
  event.respondWith(
    fetch(event.request)
      .then(response => {
        if (response && response.status === 200 && response.type === 'basic') {
          const resClone = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(event.request, resClone));
        }
        return response;
      })
      .catch(() => caches.match(event.request))
  );
});
