<?php
// Get messages for a specific conversation
include '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$conversation_id = $_GET['conversation_id'] ?? null;
if (!$conversation_id || !is_numeric($conversation_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid conversation ID required']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Verify conversation belongs to customer
$check_query = "SELECT id FROM conversations WHERE id = ? AND client_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $conversation_id, $customer_id);
$check_stmt->execute();

if ($check_stmt->get_result()->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$query = "SELECT
    id,
    sender_type,
    message,
    attachment,
    created_at
FROM messages
WHERE conversation_id = ?
ORDER BY created_at ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $conversation_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['id'],
        'sender_type' => $row['sender_type'],
        'message' => $row['message'],
        'attachment' => $row['attachment'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

$stmt->close();
$conn->close();
?>