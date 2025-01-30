<?php
require 'db.php';
require 'vendor/autoload.php';

use Google\Client;
use Google\Service\FirebaseCloudMessaging;

header('Content-Type: application/json');

try {
    $serviceAccount = json_decode(file_get_contents('service-account.json'), true);
    
    $client = new Client();
    $client->setAuthConfig($serviceAccount);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

    $data = json_decode(file_get_contents('php://input'), true);
    
    $message = [
        'message' => [
            'token' => $data['token'],
            'notification' => [
                'title' => $data['title'],
                'body' => $data['body']
            ],
            'webpush' => [
                'fcm_options' => [
                    'link' => 'https://yourdomain.com/notifications'
                ]
            ]
        ]
    ];

    $fcm = new FirebaseCloudMessaging($client);
    $response = $fcm->projects_messages->send(
        "projects/{$serviceAccount['project_id']}",
        new FirebaseCloudMessaging\SendMessageRequest($message)
    );

    // Store notification in database
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$data['userId'], $data['body']]);

    echo json_encode(['status' => 'success', 'response' => $response]);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}