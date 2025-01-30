<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = preg_replace('/[^0-9+]/', '', $_POST['mobile']);
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE mobile = ?");
        $stmt->execute([$mobile]);
        $user = $stmt->fetch();

        if (!$user) {
            // Insert new user with NULL fcm_token
            $stmt = $pdo->prepare("INSERT INTO users (mobile, fcm_token) VALUES (?, NULL)");
            $stmt->execute([$mobile]);
            $userId = $pdo->lastInsertId();
        } else {
            $userId = $user['id'];
        }

        $_SESSION['user_id'] = $userId;
        $_SESSION['mobile'] = $mobile;
        
        header("Location: notifications.php");
        exit;

    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5" style="max-width: 400px;">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Mobile Login</h2>
                <form method="POST">
                    <div class="mb-3">
                        <input type="tel" 
                               class="form-control" 
                               name="mobile" 
                               placeholder="Enter Mobile Number"
                               required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        Login/Register
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>