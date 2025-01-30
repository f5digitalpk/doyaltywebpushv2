importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

const firebaseConfig = {
    // apiKey: "YOUR_API_KEY",
    // authDomain: "YOUR_AUTH_DOMAIN",
    // projectId: "YOUR_PROJECT_ID",
    // storageBucket: "YOUR_STORAGE_BUCKET",
    // messagingSenderId: "YOUR_SENDER_ID",
    // appId: "YOUR_APP_ID"
      apiKey: "AIzaSyAVQ0gPGszoK4tcKOlldKIWKn8qWVkJKHk",
      authDomain: "doyalty-fcm.firebaseapp.com",
      projectId: "doyalty-fcm",
      storageBucket: "doyalty-fcm.firebasestorage.app",
      messagingSenderId: "884908353385",
      appId: "1:884908353385:web:fef16e32aefa8d91543ff8",
      measurementId: "G-0GCLQHDWFS"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Background message handler
messaging.onBackgroundMessage((payload) => {
  console.log('[sw.js] Received background message:', payload);
  
  // Customize notification
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/icon-192x192.png',
    data: { 
      url: payload.data.link || '/notifications',
      id: payload.data.notificationId
    }
  };

  // Show notification
  return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  
  // Open the app with notification URL
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
  );
  
  // Mark as clicked
  if(event.notification.data.id) {
    fetch(`/mark-clicked.php?id=${event.notification.data.id}`);
  }
});