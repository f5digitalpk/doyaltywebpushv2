      const firebaseConfig = {
        apiKey: "AIzaSyAVQ0gPGszoK4tcKOlldKIWKn8qWVkJKHk",
        authDomain: "doyalty-fcm.firebaseapp.com",
        projectId: "doyalty-fcm",
        storageBucket: "doyalty-fcm.firebasestorage.app",
        messagingSenderId: "884908353385",
        appId: "1:884908353385:web:fef16e32aefa8d91543ff8",
        measurementId: "G-0GCLQHDWFS"
      };

    const app = firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    
    async function initializeFCM() {
        try {
            const token = await messaging.getToken();
            console.log('FCM Token:', token);
            await storeFCMToken(token);
            setupMessageHandling();
        } catch (error) {
            console.error('FCM Initialization Error:', error);
        }
    }
    
    async function storeFCMToken(token) {
        try {
            const response = await fetch('/store-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ token })
            });
            
            if (!response.ok) throw new Error('Token storage failed');
            console.log('Token stored successfully');
        } catch (error) {
            console.error('Token Storage Error:', error);
        }
    }
    
    function setupMessageHandling() {
        messaging.onMessage((payload) => {
            console.log('Foreground message:', payload);
            showNotificationPopup(payload.notification);
        });
    }
    
    function showNotificationPopup(notification) {
        if (!('Notification' in window)) return;
    
        if (Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.body,
                icon: '/icon-192x192.png'
            });
        }
    }
    
    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', () => {
        if ('serviceWorker' in navigator && firebase.messaging.isSupported()) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then(() => console.log('Service Worker registered'))
                .catch(err => console.error('SW registration failed:', err));
        }
    });