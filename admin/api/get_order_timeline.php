<?php
// Get Order Timeline API
require_once __DIR__ . '/../../includes/session_init.php';
spx_session_start();

// Set JSON content type
header('Content-Type: application/json');

include '../includes/config.php';

// Check if database connection exists
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

// Get timeline data
$query = "SELECT * FROM order_timeline WHERE order_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$timeline = [];
while ($row = $result->fetch_assoc()) {
    $details = [];
    if (!empty($row['details'])) {
        $decoded = json_decode($row['details'], true);
        if (is_array($decoded)) {
            $details = $decoded;
        } else {
            $details = ['description' => $row['details']];
        }
    }

    $timeline[] = [
        'status' => $row['action'] ?? '',
        'status_description' => $details['description'] ?? null,
        'tracking_number' => $details['tracking_number'] ?? null,
        'carrier_name' => $details['carrier_name'] ?? null,
        'created_at' => $row['created_at'] ?? null,
    ];
}

echo json_encode([
    'success' => true,
    'timeline' => $timeline
]);

$stmt->close();
