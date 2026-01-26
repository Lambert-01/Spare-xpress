<?php
// Create new conversation and send initial message
include '../includes/config.php';
include '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$customer_id = $_POST['customer_id'] ?? null;
$message_text = trim($_POST['message'] ?? '');
$send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'on';

if ($customer_id === null || $customer_id === '' || !is_numeric($customer_id) || $customer_id < 0 || empty($message_text)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid customer ID and message are required']);
    exit;
}

// Verify customer exists
$customer_check = $conn->prepare("SELECT id, first_name, last_name, email FROM customers_enhanced WHERE id = ?");
$customer_check->bind_param("i", $customer_id);
$customer_check->execute();
$customer_result = $customer_check->get_result();

if ($customer_result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    exit;
}

$customer = $customer_result->fetch_assoc();

// Start transaction
$conn->begin_transaction();

try {
    // Create conversation
    $conv_query = "INSERT INTO conversations (client_id, last_message, updated_at) VALUES (?, ?, NOW())";
    $conv_stmt = $conn->prepare($conv_query);
    $preview = substr($message_text, 0, 100) . (strlen($message_text) > 100 ? '...' : '');
    $conv_stmt->bind_param("is", $customer_id, $preview);
    $conv_stmt->execute();
    $conversation_id = $conn->insert_id;

    // Insert initial message
    $msg_query = "INSERT INTO messages (conversation_id, sender_type, message) VALUES (?, 'admin', ?)";
    $msg_stmt = $conn->prepare($msg_query);
    $msg_stmt->bind_param("is", $conversation_id, $message_text);
    $msg_stmt->execute();

    // Create notification for customer
    $notif_query = "INSERT INTO notifications (user_id, type, reference_id, is_read) VALUES (?, 'message', ?, 0)";
    $notif_stmt = $conn->prepare($notif_query);
    $notif_stmt->bind_param("ii", $customer_id, $conversation_id);
    $notif_stmt->execute();

    // Send email if requested
    $email_sent = false;
    if ($send_email && file_exists('../includes/email.php')) {
        include '../includes/email.php';
        $emailService = new EmailService();
        $portal_link = SITE_URL . "/pages/my_account.php";
        $email_sent = $emailService->sendMessageNotification(
            $customer['email'],
            $customer['first_name'] . ' ' . $customer['last_name'],
            substr($message_text, 0, 100) . (strlen($message_text) > 100 ? '...' : ''),
            $portal_link
        );
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Conversation created and message sent successfully',
        'conversation_id' => $conversation_id,
        'email_sent' => $email_sent
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create conversation: ' . $e->getMessage()]);
}

$conn->close();
?>