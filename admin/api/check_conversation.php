<?php
// Check if conversation exists for a customer
include '../includes/config.php';
include '../includes/auth.php';

header('Content-Type: application/json');

$customer_id = $_GET['customer_id'] ?? null;
if ($customer_id === null || $customer_id === '' || !is_numeric($customer_id) || $customer_id < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Valid customer ID required']);
    exit;
}

$query = "SELECT id FROM conversations WHERE client_id = ? ORDER BY updated_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $conversation = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'exists' => true,
        'conversation_id' => $conversation['id']
    ]);
} else {
    echo json_encode([
        'success' => true,
        'exists' => false
    ]);
}

$stmt->close();
$conn->close();
?>