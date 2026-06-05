// SecRadar PWA Service Worker
const CACHE = 'secradar-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE = ['/manifest.json'];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE).then(c => c.addAll(PRECACHE)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;
  if (event.request.url.includes('/api/')) return; // Never cache API

  event.respondWith(
    fetch(event.request)
      .then(resp => {
        // Cache successful responses for static assets
        if (resp.ok && event.request.destination !== 'document') {
          const clone = resp.clone();
          caches.open(CACHE).then(c => c.put(event.request, clone));
        }
        return resp;
      })
      .catch(() => caches.match(event.request).then(r => r || caches.match(OFFLINE_URL)))
  );
});

// Push notifications
self.addEventListener('push', event => {
  const data = event.data?.json() || {};
  event.waitUntil(
    self.registration.showNotification(data.title || '⚠ SecRadar Alerta', {
      body: data.body || 'Seus dados foram detectados em um vazamento. Acesse o painel.',
      icon: '/icons/icon-192.png',
      badge: '/icons/badge-96.png',
      tag: 'secradar-alert',
      requireInteraction: true,
      data: { url: data.url || '/painel/alertas' },
      actions: [
        { action: 'view',    title: 'Ver detalhes' },
        { action: 'dismiss', title: 'Fechar' },
      ]
    })
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  if (event.action === 'dismiss') return;
  const url = event.notification.data?.url || '/painel/alertas';
  event.waitUntil(
    clients.matchAll({ type: 'window' }).then(cs => {
      const existing = cs.find(c => c.url.includes('/painel'));
      if (existing) { existing.focus(); existing.navigate(url); }
      else clients.openWindow(url);
    })
  );
});
