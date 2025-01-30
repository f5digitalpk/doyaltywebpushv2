<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

try {
  if (!isset($_SESSION['user_id'])) {
    throw new Exception('User not authenticated');
  }

  $data = json_decode(file_get_contents('php://input'), true);
  
  if (empty($data['token'])) {
    throw new Exception('Missing FCM token');
  }

  $stmt = $pdo->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
  $stmt->execute([trim($data['token']), $_SESSION['user_id']]);

  echo json_encode(['status' => 'success']);

} catch(Exception $e) {
  http_response_code(400);
  echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}