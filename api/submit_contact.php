<?php
// Contact Form Submission API
include '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$project = trim($_POST['project'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Parse name into first and last name
$name_parts = explode(' ', $name, 2);
$first_name = $name_parts[0];
$last_name = $name_parts[1] ?? '';

// Check if customer exists by email
$customer_query = "SELECT id FROM customers_enhanced WHERE email = ?";
$customer_stmt = $conn->prepare($customer_query);
$customer_stmt->bind_param("s", $email);
$customer_stmt->execute();
$customer_result = $customer_stmt->get_result();

$customer_id = null;

if ($customer_result->num_rows > 0) {
    // Existing customer
    $customer = $customer_result->fetch_assoc();
    $customer_id = $customer['id'];
} else {
    // Create new customer
    $insert_customer = "INSERT INTO customers_enhanced (
        customer_number, first_name, last_name, email, phone, phone_secondary,
        customer_status, email_verified, created_at, updated_at
    ) VALUES (
        CONCAT('CUST', LPAD((SELECT COALESCE(MAX(id), 0) + 1 FROM customers_enhanced), 6, '0')),
        ?, ?, ?, ?, ?,
        'active', 0, NOW(), NOW()
    )";

    $cust_stmt = $conn->prepare($insert_customer);
    $cust_stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $phone);
    $cust_stmt->execute();
    $customer_id = $conn->insert_id;
}

// Check if conversation already exists for this customer
$conv_query = "SELECT id FROM conversations WHERE client_id = ?";
$conv_stmt = $conn->prepare($conv_query);
$conv_stmt->bind_param("i", $customer_id);
$conv_stmt->execute();
$conv_result = $conv_stmt->get_result();

$conversation_id = null;

if ($conv_result->num_rows > 0) {
    // Use existing conversation
    $conversation = $conv_result->fetch_assoc();
    $conversation_id = $conversation['id'];
} else {
    // Create new conversation
    $insert_conv = "INSERT INTO conversations (client_id, last_message, updated_at) VALUES (?, ?, NOW())";
    $conv_insert_stmt = $conn->prepare($insert_conv);
    $preview = substr($message, 0, 100) . (strlen($message) > 100 ? '...' : '');
    $conv_insert_stmt->bind_param("is", $customer_id, $preview);
    $conv_insert_stmt->execute();
    $conversation_id = $conn->insert_id;
}

// Insert the message
$message_query = "INSERT INTO messages (conversation_id, sender_type, message) VALUES (?, 'client', ?)";
$message_stmt = $conn->prepare($message_query);
$message_stmt->bind_param("is", $conversation_id, $message);
$message_stmt->execute();

// Create notification for admin
$notification_query = "INSERT INTO notifications (user_id, type, reference_id, is_read) VALUES (?, 'message', ?, 0)";
$notif_stmt = $conn->prepare($notification_query);
$notif_stmt->bind_param("ii", $customer_id, $conversation_id);
$notif_stmt->execute();

// Update conversation last_message
$update_conv = "UPDATE conversations SET last_message = ?, updated_at = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_conv);
$preview = substr($message, 0, 100) . (strlen($message) > 100 ? '...' : '');
$update_stmt->bind_param("si", $preview, $conversation_id);
$update_stmt->execute();

echo json_encode([
    'success' => true,
    'message' => 'Message sent successfully. We will get back to you within 24 hours.',
    'conversation_id' => $conversation_id
]);
?>