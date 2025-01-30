<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents('php://input'), $_DELETE);
    $id = $_GET['id'] ?? null;

    if (!$id || !isset($_SESSION['user_id'])) {
        http_response_code(400);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM notifications 
                              WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        http_response_code(204);
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}