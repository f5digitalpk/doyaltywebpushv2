<?php
require 'db.php';
require 'vendor/autoload.php'; // Composer autoload

use Google\Client;
use Google\Service\FirebaseCloudMessaging;

header('Content-Type: application/json');

// 1. Service Account Configuration
$serviceAccount = [
  "type" => "service_account",
  "project_id" => "doyalty-fcm",
  "private_key_id" => "YOUR_PRIVATE_KEY_ID",
  "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEugIBADANBgkqhkiG9w0BAQEFAASCBKQwggSgAgEAAoIBAQCx5eqLoRUxWeZd\npxEIPiuCcNAFR2UHuaxvTQdEQ1BfHrsBvoRVdsFHwDYRQljKotoN2Fje7F9L9S0V\nDQXtUT8s3HHqWXXfru45KrO0T6XLDneFg0zRY62aXpsZoQV3x9tdr0em/Js2U1me\noY5Mq3H9n+8SzTwpLbRC4SQzp4XsLCv7a78SRnmwL7dZq2MoHzI4t7Qr+LxTD8XC\nzo2fw6n8tWh4VjKKvCzRPgrGXIXZApBbQSgGAPmDbKIK2imRDccKsjSPTsuYeMEZ\nYRGt0ZWEs3AwjuQz3DFXiy/MWwj7k/R2z+C4Qdco/uYjhkuyl6E3v6GHXOGxZL1w\nxSB0SqP5AgMBAAECggEAD5GfCrRAvg5vlj+iQnXqaeKEmgmrAleSCzWTDrrTZFe0\npwzs2OvpM2XTdYQ4cBd7fN2zvWKzXlrpJJ/8pk3HeR4bzBYjNj3BdHhTvNKBoHk2\nJZWdme+eyFVCPqZ0a4yumo7V9vvBe6okvDjIUtZoRfmCu0MB4lqxHW9Fs02Ub1wy\nqPDNeKmQRyiTOfLITYVbfmeNlxsOHRUaggOvAnXEDeCWcoh6l9p3oDpDidnaPzDu\nYAUj1pkQTAYERPfbvclY+bBuEAS4SE0sIJnWhpfDU7Uimyx9zct94Qs1rOG885D4\nyHrTUgh/ttTCZIioV9qtmB/6ZdroKbvgbE1v0ntFbwKBgQDwYvmVv6cP/nYgSmi2\nsu7VHfQTMvHEk4P70wNQ7vHC05ECfOt5kGlwjwetbQoQxr2r5Lv5U0vdSBOIRmUh\n7AGLwg2MrX2XSY7OG2OTyY02QPKucwtfhUps7CQ+OrycIO/BDRPzlMDYI67xTjIF\nkK03Fs1T8R+Ef5hNTVPcx12M2wKBgQC9c+oZAq5eNpuZ2SQWhIN+kY5nEzLgzNCn\nzvqo8dy0LpTLm+cc2sOYGJ49NQRXyhCwjjK0Xf72YMg/0Z5aE+pXq2aaVoA/IZFD\nluiqCb87Sm1u0Xu1Ug03zDAInkyBo9sybuglQBPxjH0MofPvspjH3pgpwMM4sx/r\nY1e6XJpAuwKBgAu5B+rhmcgR5LjWaBzgxPznpQt6pIetmfYh6Dt+K5QQtTWl2eXn\nwPyYPQucEa2Xw5Aqa3BRO5Xi+fDfXfc1hy3FnNuLamCCWdB7TXPblGNc73jKa3eR\nDHwbV/kg7CnBAXAsrxhl3LGtq85gvde/onLZTdIWJC9V+ZtrdfeCUXGZAn9wfGKC\ndxUQ21uH/WVv+T4Z6FKk4MFUuEcrW7l4liJ02TN/sRGIEFwR8owBy3jIpzFBoyx3\n61d28f/z5IIg+bIqW5qQCMwcr9GQUAnU6/SfP5G6Oqc5AX7XomTiuRqz3pZHW5J6\ni5FL2h5lYcN0jpYoFgJCyDEheTl6iYGNJW5xAoGAVw5udkdIEpzIqukGRZBQm0bp\ng1IpCj/LrVnFdDjKAaXh1afqyZMdff6fHpmlnX2g0eDJHaY5ZSbD5lYidSBNX1VQ\ne6pDb7ornxek97bvJN7ImCKtnrSkdTyPOF5gX0vtLbSqXR3JMXWENxQG6enGNs8I\nmZjAXKCDqw3iWCV8Io8=\n-----END PRIVATE KEY-----\n",
  "client_email" => "firebase-adminsdk-fbsvc@doyalty-fcm.iam.gserviceaccount.com",
  "client_id" => "115552680161729418413",
  "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
  "token_uri" => "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-fbsvc%40doyalty-fcm.iam.gserviceaccount.com"
];

try {
  // 2. Generate Access Token
  $client = new Client();
  $client->setAuthConfig($serviceAccount);
  $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
  $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

  // 3. Process Notification Request
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

  // 4. Send via FCM v1 API
  $fcm = new FirebaseCloudMessaging($client);
  $response = $fcm->projects_messages->send(
    "projects/{$serviceAccount['project_id']}",
    new FirebaseCloudMessaging\SendMessageRequest($message)
  );

  // 5. Save to Database
  $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
  $stmt->execute([$data['userId'], $data['body']]);

  echo json_encode(['status' => 'success', 'data' => $response]);

} catch(Exception $e) {
  http_response_code(500);
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}