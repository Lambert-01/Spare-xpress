<?php
// Get order items API endpoint
header('Content-Type: application/json');
include '../includes/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if customer is logged in
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['customer_name']) || !isset($_SESSION['customer_email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to view order items'
    ]);
    exit;
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid order ID'
    ]);
    exit;
}

// Verify that the order belongs to the current customer
$order_check_stmt = $conn->prepare("SELECT id FROM orders_enhanced WHERE id = ? AND customer_id = ?");
$order_check_stmt->bind_param("ii", $order_id, $_SESSION['customer_id']);
$order_check_stmt->execute();

if ($order_check_stmt->get_result()->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Order not found or access denied'
    ]);
    $order_check_stmt->close();
    exit;
}
$order_check_stmt->close();

// Get order items
$stmt = $conn->prepare("
    SELECT oi.*,
           p.product_name,
           p.image,
           b.brand_name,
           m.model_name
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    LEFT JOIN vehicle_brands b ON p.brand_id = b.id
    LEFT JOIN vehicle_models m ON p.model_id = m.id
    WHERE oi.order_id = ?
    ORDER BY oi.id
");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    // Format image path
    $image_path = $row['image'];
    if (!empty($image_path) && !str_starts_with($image_path, '/')) {
        $image_path = '/' . $image_path;
    }

    $items[] = [
        'id' => $row['id'],
        'product_id' => $row['product_id'],
        'product_name' => $row['product_name'] ?? 'Unknown Product',
        'quantity' => $row['quantity'],
        'price' => (float)$row['unit_price'],
        'subtotal' => (float)$row['subtotal'],
        'image' => $image_path ?: '/img/no-image.png',
        'brand' => $row['product_brand'],
        'model' => $row['product_model']
    ];
}

$stmt->close();

echo json_encode([
    'success' => true,
    'items' => $items,
    'total_items' => count($items)
]);
?>