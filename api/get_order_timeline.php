<?php
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start();

header('Content-Type: application/json');

include '../includes/config.php';

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

$order_stmt = $conn->prepare("SELECT id FROM orders_enhanced WHERE id = ? AND customer_id = ?");
$order_stmt->bind_param("ii", $order_id, $_SESSION['customer_id']);
$order_stmt->execute();
if (!$order_stmt->get_result()->fetch_assoc()) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

$timeline_stmt = $conn->prepare("SELECT action, details, created_at FROM order_timeline WHERE order_id = ? ORDER BY created_at ASC");
$timeline_stmt->bind_param("i", $order_id);
$timeline_stmt->execute();
$timeline_result = $timeline_stmt->get_result();

$timeline = [];
while ($row = $timeline_result->fetch_assoc()) {
    $details = [];
    if (!empty($row['details'])) {
        $decoded = json_decode($row['details'], true);
        $details = is_array($decoded) ? $decoded : ['description' => $row['details']];
    }

    $timeline[] = [
        'status' => $row['action'] ?? '',
        'description' => $details['description'] ?? null,
        'tracking_number' => $details['tracking_number'] ?? null,
        'carrier_name' => $details['carrier_name'] ?? null,
        'created_at' => $row['created_at'] ?? null,
    ];
}

$notes = [];
$notes_stmt = $conn->prepare("SELECT note_type, note_content, created_at FROM order_notes WHERE order_id = ? AND is_visible_to_customer = 1 ORDER BY created_at ASC");
if ($notes_stmt) {
    $notes_stmt->bind_param("i", $order_id);
    $notes_stmt->execute();
    $notes_result = $notes_stmt->get_result();
    while ($row = $notes_result->fetch_assoc()) {
        $notes[] = [
            'type' => $row['note_type'] ?? 'customer',
            'content' => $row['note_content'] ?? '',
            'created_at' => $row['created_at'] ?? null,
        ];
    }
}

echo json_encode([
    'success' => true,
    'timeline' => $timeline,
    'notes' => $notes,
]);
?>
