<?php
require 'db.php';
session_start();

// For demo purposes - you should implement proper user authentication
$mobile = $_SESSION['mobile'] ?? null;

if (!$mobile) {
    http_response_code(401);
    die(json_encode(['error' => 'Not authenticated']));
}

try {
    $stmt = $pdo->prepare("SELECT n.* FROM notifications n
                          JOIN users u ON n.user_id = u.id
                          WHERE u.mobile = ?
                          ORDER BY n.created_at DESC");
    $stmt->execute([$mobile]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($notifications);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>