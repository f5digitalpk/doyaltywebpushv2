<?php
require 'db.php';

header('Content-Type: application/json');

try {
    $mobile = $_POST['mobile'];
    $fcm_token = $_POST['fcm_token'] ?? null;

    // Validate mobile number
    if (!preg_match('/^\+?\d{8,15}$/', $mobile)) {
        throw new Exception("Invalid mobile number");
    }

    // Check existing user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile = ?");
    $stmt->execute([$mobile]);
    $user = $stmt->fetch();

    if ($user) {
        // Update FCM token
        $stmt = $pdo->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
        $stmt->execute([$fcm_token, $user['id']]);
        $user_id = $user['id'];
    } else {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (mobile, fcm_token) VALUES (?, ?)");
        $stmt->execute([$mobile, $fcm_token]);
        $user_id = $pdo->lastInsertId();
    }

    // Get user notifications
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'user_id' => $user_id,
        'notifications' => $notifications
    ]);

} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>