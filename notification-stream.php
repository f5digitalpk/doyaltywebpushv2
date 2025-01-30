<?php
require 'db.php';
session_start();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

while(true) {
    // Get last notification for user
    $stmt = $pdo->prepare("SELECT * FROM notifications 
                          WHERE user_id = ? 
                          ORDER BY created_at DESC 
                          LIMIT 1");
    $stmt->execute([$_SESSION['user_id']]);
    $notification = $stmt->fetch();
    
    if($notification && !$notification['is_read']) {
        echo "data: " . json_encode([
            'title' => 'New Notification',
            'body' => $notification['message']
        ]) . "\n\n";
        ob_flush();
        flush();
        
        // Mark as read
        $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?")
            ->execute([$notification['id']]);
    }
    
    sleep(1);
}