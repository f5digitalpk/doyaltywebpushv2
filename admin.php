<?php
require 'db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin-login.php");
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin-login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .logout-btn { float: right; }
        .notification-form { margin-top: 30px; }
        .user-list { border: 1px solid #ddd; padding: 10px; margin: 10px 0; }
        .user-item { margin: 5px 0; }
        textarea { width: 100%; height: 100px; margin: 10px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <a href="?logout" class="logout-btn">Logout</a>
    <h1>Send Notifications</h1>
    
    <div class="notification-form">
        <form id="notificationForm">
            <h3>Select Users:</h3>
            <div class="user-list">
                <?php
                try {
                    $stmt = $pdo->query("SELECT id, mobile FROM users");
                    $users = $stmt->fetchAll();
                    
                    if (count($users) === 0) {
                        echo "<p>No users found!</p>";
                    } else {
                        foreach ($users as $user) {
                            echo '<div class="user-item">';
                            echo '<label>';
                            echo '<input type="checkbox" name="user_ids[]" value="'.$user['id'].'"> ';
                            echo htmlspecialchars($user['mobile']);
                            echo '</label>';
                            echo '</div>';
                        }
                    }
                } catch(PDOException $e) {
                    echo "<p>Error loading users: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>
            
            <h3>Notification Message:</h3>
            <textarea name="message" required placeholder="Enter notification message"></textarea>
            
            <button type="submit">Send Notification</button>
        </form>
    </div>

    <script>
        document.getElementById('notificationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const userIDs = Array.from(formData.getAll('user_ids[]'));
            const message = formData.get('message');

            if (userIDs.length === 0 || !message) {
                alert('Please select at least one user and enter a message');
                return;
            }

            try {
                const response = await fetch('send-notification.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });
                
                const result = await response.json();
                if (result.status === 'success') {
                    alert('Notification sent successfully!');
                    e.target.reset();
                } else {
                    alert('Error: ' + (result.message || 'Failed to send notification'));
                }
            } catch (error) {
                alert('Network error - please try again');
            }
        });
    </script>
</body>
</html>