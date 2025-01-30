importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

const firebaseConfig = {
    apiKey: "AIzaSyAVQ0gPGszoK4tcKOlldKIWKn8qWVkJKHk",
    authDomain: "doyalty-fcm.firebaseapp.com",
    projectId: "doyalty-fcm",
    storageBucket: "doyalty-fcm.firebasestorage.app",
    messagingSenderId: "884908353385",
    appId: "1:884908353385:web:fef16e32aefa8d91543ff8",
    measurementId: "G-0GCLQHDWFS"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
  console.log('[SW] Received background message:', payload);
  
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/icon-192x192.png',
    data: { url: '/notifications' }
  };

  return self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
});