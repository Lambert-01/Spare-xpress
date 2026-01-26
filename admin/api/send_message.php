<?php
// Send Message API - Admin replies to client conversations
include '../../includes/config.php';
include '../includes/auth.php'; // Ensure admin is logged in

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$conversation_id = $_POST['conversation_id'] ?? null;
$message_text = trim($_POST['message'] ?? '');
$send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'on';

// Handle file upload
$attachment_path = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../uploads/messages/';
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

// Get conversation details
$conv_query = "SELECT conv.*, c.first_name, c.last_name, c.email FROM conversations conv JOIN customers_enhanced c ON conv.client_id = c.id WHERE conv.id = ?";
$conv_stmt = $conn->prepare($conv_query);
$conv_stmt->bind_param("i", $conversation_id);
$conv_stmt->execute();
$conversation = $conv_stmt->get_result()->fetch_assoc();

if (!$conversation) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Conversation not found']);
    exit;
}

// Insert the message
$message_query = "INSERT INTO messages (conversation_id, sender_type, message, attachment) VALUES (?, 'admin', ?, ?)";
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

// Mark related notifications as read
$conn->query("UPDATE notifications SET is_read = 1 WHERE reference_id = $conversation_id AND type = 'message'");

// Send email if requested
$email_sent = false;
if ($send_email && file_exists('../../includes/email.php')) {
    include '../../includes/email.php';
    $emailService = new EmailService();
    $portal_link = SITE_URL . "/pages/messages.php";
    $email_sent = $emailService->sendMessageNotification(
        $conversation['email'],
        $conversation['first_name'] . ' ' . $conversation['last_name'],
        substr($message_text, 0, 100) . (strlen($message_text) > 100 ? '...' : ''),
        $portal_link
    );
}

echo json_encode([
    'success' => true,
    'message' => 'Message sent successfully',
    'email_sent' => $email_sent
]);
?>