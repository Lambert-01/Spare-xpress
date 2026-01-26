<?php
include '../includes/auth.php';
include '../includes/functions.php';
header('Content-Type: application/json');
if (!isset($_GET['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Customer ID required']);
    exit;
}
$customer_id = $_GET['customer_id'];
$detail_query = "
    SELECT
        c.*,
        COUNT(DISTINCT conv.id) as total_conversations,
        COUNT(m.id) as total_messages,
        COUNT(CASE WHEN n.is_read = 0 AND n.type = 'message' THEN 1 END) as unread_messages,
        MAX(m.created_at) as last_activity,
        MIN(c.created_at) as registration_date
    FROM customers_enhanced c
    LEFT JOIN conversations conv ON c.id = conv.client_id
    LEFT JOIN messages m ON conv.id = m.conversation_id
    LEFT JOIN notifications n ON c.id = n.user_id AND n.type = 'message'
    WHERE c.id = ?
    GROUP BY c.id
";
$detail_stmt = $conn->prepare($detail_query);
$detail_stmt->bind_param("i", $customer_id);
$detail_stmt->execute();
$customer = $detail_stmt->get_result()->fetch_assoc();
if ($customer) {
    echo json_encode(['success' => true, 'customer' => $customer]);
} else {
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
}
?>