const CACHE_VERSION = 'saas-__SW_VERSION__';
const OFFLINE_URL = '/offline';
const PRECACHE_URLS = [
    OFFLINE_URL,
    '/favicon.ico',
    '/manifest.webmanifest',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_VERSION).then((cache) => cache.addAll(PRECACHE_URLS)),
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((keys) =>
                Promise.all(
                    keys
                        .filter((key) => key !== CACHE_VERSION)
                        .map((key) => caches.delete(key)),
                ),
            )
            .then(() => self.clients.claim()),
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() =>
                caches.match(OFFLINE_URL).then((response) => response ?? new Response('Offline', { status: 503 })),
            ),
        );
        return;
    }

    if (/\.(?:css|js|png|jpg|jpeg|svg|webp|ico|woff2?)$/i.test(url.pathname)) {
        event.respondWith(
            caches.match(request).then((cached) => {
                const fetchPromise = fetch(request)
                    .then((response) => {
                        if (response && response.ok) {
                            const clone = response.clone();
                            caches.open(CACHE_VERSION).then((cache) => cache.put(request, clone));
                        }
                        return response;
                    })
                    .catch(() => cached);
                return cached ?? fetchPromise;
            }),
        );
    }
});

self.addEventListener('push', (event) => {
    const data = (() => {
        try {
            return event.data ? event.data.json() : {};
        } catch (e) {
            return { title: 'Notification', body: event.data ? event.data.text() : '' };
        }
    })();

    const title = data.title ?? 'Notification';
    const options = {
        body: data.body ?? '',
        icon: data.icon,
        badge: data.badge,
        tag: data.tag,
        data: data.data ?? {},
        actions: data.actions ?? [],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url ?? '/app';
    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
            const existing = clients.find((client) => client.url.includes(url));
            if (existing) {
                return existing.focus();
            }
            return self.clients.openWindow(url);
        }),
    );
});
