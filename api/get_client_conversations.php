<?php
// Get conversations for logged-in client
include '../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

$query = "SELECT
    c.id,
    c.last_message,
    c.updated_at,
    COUNT(CASE WHEN m.sender_type = 'admin' AND m.created_at > COALESCE(
        (SELECT MAX(created_at) FROM messages m2 WHERE m2.conversation_id = c.id AND m2.sender_type = 'client'), '1970-01-01'
    ) THEN 1 END) as unread_count
FROM conversations c
LEFT JOIN messages m ON c.id = m.conversation_id
WHERE c.client_id = ?
GROUP BY c.id
ORDER BY c.updated_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];
while ($row = $result->fetch_assoc()) {
    $conversations[] = [
        'id' => $row['id'],
        'last_message' => $row['last_message'],
        'updated_at' => $row['updated_at'],
        'unread_count' => (int)$row['unread_count']
    ];
}

echo json_encode([
    'success' => true,
    'conversations' => $conversations
]);

$stmt->close();
$conn->close();
?>