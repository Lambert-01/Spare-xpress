<?php
include '../../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    echo json_encode(['success' => false, 'message' => 'Notification ID required']);
    exit;
}

$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
$stmt->bind_param("i", $notification_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to mark notification as read']);
}

$stmt->close();
$conn->close();
?>