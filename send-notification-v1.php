<?php
require 'vendor/autoload.php'; // Require Google API Client

use Google\Client;
use Google\Service\FirebaseCloudMessaging;

function sendFcmV1Notification($token, $message) {
    $client = new Client();
    $client->setAuthConfig('path/to/service-account.json');
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    
    $fcm = new FirebaseCloudMessaging($client);
    
    $projectId = 'your-project-id'; // From service account JSON
    
    $body = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => 'New Notification',
                'body' => $message
            ],
            'webpush' => [
                'fcm_options' => [
                    'link' => 'https://your-pwa-url.com/notifications'
                ]
            ]
        ]
    ];

    try {
        $response = $fcm->projects_messages->send(
            "projects/$projectId",
            new FirebaseCloudMessaging\SendMessageRequest($body)
        );
        return $response;
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

// Usage example
$userToken = 'user-fcm-token-from-database';
$result = sendFcmV1Notification($userToken, 'Hello from FCM V1!');
echo json_encode($result);
?>