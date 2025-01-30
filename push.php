<?php
// Database Connection
$db_host = 'localhost';
$db_user = 'f5digit2_saqib';
$db_password = 'kingkool111';
$db_name = 'f5digit2_webpush';

$conn = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Create required tables
function setupDatabase($conn)
{
    $createUsersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mobile VARCHAR(20) NOT NULL UNIQUE,
        fcm_token TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $createNotificationsTable = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";

    $conn->query($createUsersTable);
    $conn->query($createNotificationsTable);
}
setupDatabase($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Push Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Send Push Notifications</h2>
    <form id="notificationForm" method="POST">
        <div class="mb-3">
            <label for="user_ids" class="form-label">User IDs (comma-separated)</label>
            <input type="text" class="form-control" id="user_ids" name="user_ids" placeholder="e.g., 1,2,3" required>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Notification Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Notification</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('notificationForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'sendNotification');

        try {
            const response = await fetch('admin.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            alert(result.message);
        } catch (error) {
            alert('Failed to send notification');
        }
    });
</script>
</body>
</html>
