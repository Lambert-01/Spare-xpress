<?php
include '../../includes/config.php';
include '../includes/auth.php';

header('Content-Type: application/json');

$unread_count = $conn->query("SELECT COUNT(*) as count FROM client_messages WHERE status = 'unread'")->fetch_assoc()['count'];

echo json_encode([
    'unread_count' => $unread_count
]);

$conn->close();
?>