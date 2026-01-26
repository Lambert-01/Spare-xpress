<?php
include '../../includes/config.php';
include '../includes/auth.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid message ID']);
    exit;
}

$message_id = (int)$_GET['id'];

$message = $conn->query("SELECT * FROM client_messages WHERE id = $message_id")->fetch_assoc();

if ($message) {
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Message not found'
    ]);
}

$conn->close();
?>