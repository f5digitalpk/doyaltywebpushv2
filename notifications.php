<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Mark notifications as read
$pdo->beginTransaction();
try {
    $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")
        ->execute([$_SESSION['user_id']]);
    $pdo->commit();
} catch(Exception $e) {
    $pdo->rollBack();
}

// Get notifications
$stmt = $pdo->prepare("SELECT * FROM notifications 
                      WHERE user_id = ? 
                      ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notification-list {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 15px;
        }
        .notification-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .notification-card:hover {
            transform: translateX(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .notification-card.unread {
            border-left-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .notification-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .notification-actions {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .notification-card:hover .notification-actions {
            opacity: 1;
        }
        .empty-state {
            text-align: center;
            padding: 4rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        .badge-circle {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; // Include navigation bar ?>
    
    <div class="notification-list">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Notifications</h2>
            <button class="btn btn-outline-secondary" onclick="refreshNotifications()">
                <i class="fas fa-sync"></i> Refresh
            </button>
        </div>

        <?php if(count($notifications) > 0): ?>
            <div class="list-group">
                <?php foreach ($notifications as $notification): ?>
                    <div class="list-group-item notification-card <?= $notification['is_read'] ? '' : 'unread' ?>"
                        data-id="<?= $notification['id'] ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="me-3">
                                <?php if(!$notification['is_read']): ?>
                                    <span class="badge bg-primary badge-circle"></span>
                                <?php endif; ?>
                                <div class="mb-1"><?= htmlspecialchars($notification['message']) ?></div>
                                <small class="notification-time">
                                    <i class="fas fa-clock"></i>
                                    <?= date('M j, Y \a\t g:i A', strtotime($notification['created_at'])) ?>
                                </small>
                            </div>
                            <div class="notification-actions">
                                <button class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteNotification(<?= $notification['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h4>No notifications yet</h4>
                <p class="text-muted">We'll notify you when there's something new.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time updates
        const eventSource = new EventSource('notification-stream.php');
        
        eventSource.onmessage = function(e) {
            const notification = JSON.parse(e.data);
            const list = document.querySelector('.list-group');
            
            if(list) {
                const newNotification = createNotificationElement(notification);
                list.prepend(newNotification);
            }
        };

        function createNotificationElement(notification) {
            const element = document.createElement('div');
            element.className = `list-group-item notification-card ${notification.is_read ? '' : 'unread'}`;
            element.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <div class="me-3">
                        ${!notification.is_read ? '<span class="badge bg-primary badge-circle"></span>' : ''}
                        <div class="mb-1">${notification.message}</div>
                        <small class="notification-time">
                            <i class="fas fa-clock"></i>
                            ${new Date(notification.created_at).toLocaleString()}
                        </small>
                    </div>
                    <div class="notification-actions">
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="deleteNotification(${notification.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            return element;
        }

        async function deleteNotification(id) {
            if(confirm('Are you sure you want to delete this notification?')) {
                try {
                    const response = await fetch(`delete-notification.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    if(response.ok) {
                        document.querySelector(`[data-id="${id}"]`).remove();
                    }
                } catch(error) {
                    console.error('Error deleting notification:', error);
                }
            }
        }

        function refreshNotifications() {
            window.location.reload();
        }
    </script>
</body>
</html>