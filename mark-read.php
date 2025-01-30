<?php
require 'db.php';
session_start();

$mobile = $_SESSION['mobile'] ?? null;
if (!$mobile) die;

$stmt = $pdo->prepare("UPDATE notifications n
                      JOIN users u ON n.user_id = u.id
                      SET n.is_read = 1
                      WHERE u.mobile = ?");
$stmt->execute([$mobile]);
echo json_encode(['status' => 'success']);
?>