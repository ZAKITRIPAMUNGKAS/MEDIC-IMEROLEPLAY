const CACHE_NAME = 'ime-medis-cache-v3';
const STATIC_ASSET_EXTENSIONS = [
  '.png', '.jpg', '.jpeg', '.webp', '.svg', '.ico', '.woff2', '.woff', '.ttf'
];
const EXCLUDED_PATH_PREFIXES = [
  '/staff', '/admin', '/portal', '/login', '/logout', '/csrf-token', '/api', '/livewire', '/wrapped'
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
  // Never intercept HTML page navigations so auth redirects, cookies, and CSRF tokens stay fresh
  if (event.request.mode === 'navigate') return;

  const url = new URL(event.request.url);
  if (url.origin !== self.location.origin) return;
  if (EXCLUDED_PATH_PREFIXES.some(prefix => url.pathname === prefix || url.pathname.startsWith(prefix + '/'))) {
    return;
  }

  const isStaticAsset = STATIC_ASSET_EXTENSIONS.some(ext => url.pathname.toLowerCase().endsWith(ext));
  if (!isStaticAsset) return;

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
