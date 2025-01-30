<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

// Get all users with FCM tokens
$users = $pdo->query("SELECT id, mobile, fcm_token FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .user-list { max-height: 300px; overflow-y: auto; }
        .notification-status { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Send Notifications</h1>
        <form id="notificationForm">
            <div class="mb-3">
                <label class="form-label">Select Users:</label>
                <div class="user-list">
                    <?php foreach ($users as $user): ?>
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="user_ids[]" 
                                   value="<?= $user['id'] ?>"
                                   data-token="<?= $user['fcm_token'] ?>">
                            <label class="form-check-label">
                                <?= htmlspecialchars($user['mobile']) ?>
                                <?php if ($user['fcm_token']): ?>
                                    <span class="notification-status">(Notifications Enabled)</span>
                                <?php else: ?>
                                    <span class="text-muted">(Notifications Disabled)</span>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Message:</label>
                <textarea name="message" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Notification</button>
        </form>
    </div>

    <script>
        document.getElementById('notificationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const userTokens = Array.from(e.target.querySelectorAll('input[type="checkbox"]:checked'))
                .map(input => input.dataset.token)
                .filter(token => token); // Filter out users without FCM tokens

            if (userTokens.length === 0) {
                alert('No users with notification permissions selected.');
                return;
            }

            try {
                const response = await fetch('send-notification.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tokens: userTokens,
                        message: formData.get('message')
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    alert('Notification sent successfully!');
                } else {
                    alert('Error: ' + (result.message || 'Failed to send notification'));
                }
            } catch (error) {
                console.error('Network Error:', error);
                alert('Network error - please check your connection.');
            }
        });
    </script>
</body>
</html>