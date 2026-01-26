<?php
include '../includes/auth.php';
include '../includes/config.php';

if (!isset($_GET['conversation_id'])) {
    die('Conversation ID required');
}

$conversation_id = (int)$_GET['conversation_id'];

// Get conversation details
$conv_query = "
    SELECT conv.*, c.first_name, c.last_name, c.email
    FROM conversations conv
    JOIN customers_enhanced c ON conv.client_id = c.id
    WHERE conv.id = ?
";
$conv_stmt = $conn->prepare($conv_query);
$conv_stmt->bind_param("i", $conversation_id);
$conv_stmt->execute();
$conversation = $conv_stmt->get_result()->fetch_assoc();

if (!$conversation) {
    die('Conversation not found');
}

// Get messages
$msg_query = "
    SELECT m.*, c.first_name, c.last_name, a.username as admin_name
    FROM messages m
    LEFT JOIN customers_enhanced c ON m.sender_type = 'client' AND c.id = m.sender_id
    LEFT JOIN admin_users a ON m.sender_type = 'admin' AND a.id = m.sender_id
    WHERE m.conversation_id = ?
    ORDER BY m.created_at ASC
";
$msg_stmt = $conn->prepare($msg_query);
$msg_stmt->bind_param("i", $conversation_id);
$msg_stmt->execute();
$messages = $msg_stmt->get_result();

// Generate export content
$filename = "conversation_{$conversation_id}_" . date('Y-m-d_H-i-s') . '.txt';

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo "SPARE XPRESS LTD - Conversation Export\n";
echo "=====================================\n\n";
echo "Customer: {$conversation['first_name']} {$conversation['last_name']}\n";
echo "Email: {$conversation['email']}\n";
echo "Conversation ID: {$conversation_id}\n";
echo "Started: " . date('F d, Y H:i:s', strtotime($conversation['created_at'])) . "\n";
echo "Last Updated: " . date('F d, Y H:i:s', strtotime($conversation['updated_at'])) . "\n\n";
echo "Message History:\n";
echo "----------------\n\n";

while ($message = $messages->fetch_assoc()) {
    $sender = $message['sender_type'] === 'admin' ?
        'Admin (' . ($message['admin_name'] ?: 'System') . ')' :
        'Customer (' . $message['first_name'] . ' ' . $message['last_name'] . ')';

    echo "[" . date('M d, Y H:i:s', strtotime($message['created_at'])) . "] {$sender}:\n";
    echo wordwrap($message['message'], 80, "\n", true) . "\n\n";
}

echo "End of Conversation\n";
echo "Generated on: " . date('F d, Y H:i:s') . "\n";

$conn->close();
?>