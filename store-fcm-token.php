<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($_SESSION['user_id']) || !isset($data['token'])) {
        throw new Exception('Invalid request');
    }

    $stmt = $pdo->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
    $stmt->execute([$data['token'], $_SESSION['user_id']]);
    
    echo json_encode(['status' => 'success']);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}