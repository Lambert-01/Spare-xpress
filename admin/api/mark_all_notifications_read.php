<?php
include '../includes/auth.php';
include '../includes/config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark all notifications as read
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $success = $stmt->execute();

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to mark notifications as read']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>