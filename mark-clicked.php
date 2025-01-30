<?php
require 'db.php';
session_start();

if (!isset($_SESSION['mobile'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Unauthorized']));
}

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications 
                              SET is_clicked = 1 
                              WHERE id = ? AND user_id = 
                                (SELECT id FROM users WHERE mobile = ?)");
        $stmt->execute([$_GET['id'], $_SESSION['mobile']]);
        echo json_encode(['status' => 'success']);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>