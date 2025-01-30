<?php
require 'db.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['userId']) || !isset($data['message'])) {
        throw new Exception('Invalid data');
    }

    $stmt = $pdo->prepare("INSERT INTO notifications 
                          (user_id, message, is_delivered) 
                          VALUES (?, ?, 1)");
    $stmt->execute([$data['userId'], $data['message']]);
    
    echo json_encode(['status' => 'success']);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}