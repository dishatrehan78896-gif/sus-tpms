const CACHE_NAME = 'tpms-v1.2';
const urlsToCache = [
    '/',
    '/index.php',
    '/dashboard.php',
    '/login.php',
    '/signup.php',
    '/tire-health.php',
    '/config.php',
    '/style.css',
    '/offline.html',
	'/logout.php',
	'/image.html',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://cdn.jsdelivr.net/npm/chart.js'
];

// Install event - cache files
self.addEventListener('install', event => {
    console.log('Service Worker: Installed');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Service Worker: Caching Files');
                return cache.addAll(urlsToCache);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activated');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cache => {
                    if (cache !== CACHE_NAME) {
                        console.log('Service Worker: Clearing Old Cache');
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', event => {
    console.log('Service Worker: Fetching');
    event.respondWith(
        fetch(event.request)
            .then(response => {
                // Make copy/clone of response
                const resClone = response.clone();
                // Open cache
                caches.open(CACHE_NAME)
                    .then(cache => {
                        // Add response to cache
                        cache.put(event.request, resClone);
                    });
                return response;
            })
            .catch(err => {
                // If network fails, serve from cache
                return caches.match(event.request)
                    .then(response => {
                        if (response) {
                            return response;
                        }
                        // If not in cache, show offline page
                        return caches.match('/offline.html');
                    });
            })
    );
});

// Background sync for offline data
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        console.log('Service Worker: Background Sync');
        event.waitUntil(doBackgroundSync());
    }
});

async function doBackgroundSync() {
    // Sync data when connection is restored
    try {
        // Your sync logic here
        console.log('Background sync completed');
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}