<?php
// Client Reply API - Send message from client to admin
include '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$conversation_id = $_POST['conversation_id'] ?? null;
$message_text = trim($_POST['message'] ?? '');

// Handle file upload
$attachment_path = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/messages/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_name = uniqid() . '_' . basename($_FILES['attachment']['name']);
    $file_path = $upload_dir . $file_name;
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
        $attachment_path = 'uploads/messages/' . $file_name;
    }
}

if (!$conversation_id || (empty($message_text) && !$attachment_path)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Conversation ID and message or attachment are required']);
    exit;
}

// Verify conversation belongs to customer
$customer_id = $_SESSION['customer_id'];
$conv_check = "SELECT id FROM conversations WHERE id = ? AND client_id = ?";
$check_stmt = $conn->prepare($conv_check);
$check_stmt->bind_param("ii", $conversation_id, $customer_id);
$check_stmt->execute();

if ($check_stmt->get_result()->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Insert the message
$message_query = "INSERT INTO messages (conversation_id, sender_type, message, attachment) VALUES (?, 'client', ?, ?)";
$message_stmt = $conn->prepare($message_query);
$message_stmt->bind_param("iss", $conversation_id, $message_text, $attachment_path);

if (!$message_stmt->execute()) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save message']);
    exit;
}

// Update conversation last_message and timestamp
$update_conv = "UPDATE conversations SET last_message = ?, updated_at = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_conv);
$preview = substr($message_text, 0, 100) . (strlen($message_text) > 100 ? '...' : '');
$update_stmt->bind_param("si", $preview, $conversation_id);
$update_stmt->execute();

// Create notification for admin
$notification_query = "INSERT INTO notifications (user_id, type, reference_id, is_read) VALUES (?, 'message', ?, 0)";
$notif_stmt = $conn->prepare($notification_query);
$notif_stmt->bind_param("ii", $customer_id, $conversation_id);
$notif_stmt->execute();

echo json_encode([
    'success' => true,
    'message' => 'Reply sent successfully'
]);
?>