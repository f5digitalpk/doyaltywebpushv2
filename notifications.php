<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total notifications
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ?");
$totalStmt->execute([$_SESSION['user_id']]);
$totalNotifications = $totalStmt->fetchColumn();

// Get paginated notifications
$stmt = $pdo->prepare("SELECT * FROM notifications 
                      WHERE user_id = :user_id 
                      ORDER BY created_at DESC 
                      LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .notification-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease-in-out;
        }
        .notification-card.unread {
            border-left-color: #dc3545;
        }
        .notification-card:hover {
            transform: scale(1.05);
        }
        .pagination {
            margin-top: 2rem;
        }
        .notification-card p {
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .modal-content {
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1>Your Notifications</h1>

        <!-- Notification Cards -->
        <div class="row">
            <?php if (count($notifications) > 0): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                        <div class="notification-card <?= $notification['is_read'] ? '' : 'unread' ?>" 
                             data-bs-toggle="modal" 
                             data-bs-target="#notificationModal" 
                             data-bs-message="<?= htmlspecialchars($notification['message']) ?>"
                             data-bs-created="<?= date('M j, Y g:i a', strtotime($notification['created_at'])) ?>"
                             data-bs-details="<?= isset($notification['details']) ? htmlspecialchars($notification['details']) : 'No details available' ?>">
                            <p><?= htmlspecialchars($notification['message']) ?></p>
                            <small class="text-muted">
                                <?= date('M j, Y g:i a', strtotime($notification['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No notifications found.</div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= ceil($totalNotifications / $perPage); $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Modal for Notification Details -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 id="notificationMessage"></h5>
                    <p><strong>Details:</strong> <span id="notificationDetails"></span></p>
                    <p><strong>Created on:</strong> <span id="notificationCreated"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var notificationModal = document.getElementById('notificationModal');
        notificationModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var message = button.getAttribute('data-bs-message');
            var created = button.getAttribute('data-bs-created');
            var details = button.getAttribute('data-bs-details');

            var modalMessage = notificationModal.querySelector('#notificationMessage');
            var modalDetails = notificationModal.querySelector('#notificationDetails');
            var modalCreated = notificationModal.querySelector('#notificationCreated');

            modalMessage.textContent = message;
            modalDetails.textContent = details; // This will display 'No details available' if no details exist
            modalCreated.textContent = "Created on: " + created;
        });
    </script>
</body>
</html>
