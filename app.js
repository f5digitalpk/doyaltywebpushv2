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
          const permission = await Notification.requestPermission();
          if (permission === 'granted') {
            const token = await messaging.getToken();
            console.log('FCM Token:', token);
            await storeFCMToken(token);
            setupTokenRefresh();
          }
        } catch (error) {
          console.error('FCM Initialization Error:', error);
        }
      }
      
      async function storeFCMToken(token) {
        try {
          const response = await fetch('/store-token.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token })
          });
          
          if (!response.ok) throw new Error('Token storage failed');
          console.log('Token stored successfully');
        } catch (error) {
          console.error('Token Storage Error:', error);
        }
      }
      
      function setupTokenRefresh() {
        messaging.onTokenRefresh(async () => {
          try {
            const newToken = await messaging.getToken();
            console.log('Token refreshed:', newToken);
            await storeFCMToken(newToken);
          } catch (error) {
            console.error('Token Refresh Error:', error);
          }
        });
      }
      
      // Initialize when DOM loads
      document.addEventListener('DOMContentLoaded', () => {
        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(() => initializeFCM())
            .catch(err => console.error('SW Registration Failed:', err));
        }
      });