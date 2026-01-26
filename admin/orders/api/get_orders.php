<?php
// API Endpoint: Get Orders Data for Real-time Updates
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../../includes/auth.php';
include '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    if ($action === 'count') {
        // Get new orders count (orders from last 24 hours)
        $new_orders = $conn->query("
            SELECT COUNT(*) as count
            FROM orders_enhanced
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ")->fetch_assoc()['count'];

        echo json_encode([
            'success' => true,
            'new_orders' => (int)$new_orders
        ]);

    } elseif ($action === 'urgent') {
        // Get urgent orders count
        $urgent_count = $conn->query("
            SELECT COUNT(*) as count
            FROM orders_enhanced
            WHERE priority_level = 'urgent'
            AND order_status NOT IN ('delivered', 'cancelled', 'failed')
        ")->fetch_assoc()['count'];

        echo json_encode([
            'success' => true,
            'urgent_orders' => (int)$urgent_count
        ]);

    } elseif ($action === 'stats') {
        // Get order statistics
        $stats = $conn->query("
            SELECT
                COUNT(*) as total_orders,
                COUNT(CASE WHEN order_status = 'pending' THEN 1 END) as pending_orders,
                COUNT(CASE WHEN order_status = 'processing' THEN 1 END) as processing_orders,
                COUNT(CASE WHEN order_status = 'shipped' THEN 1 END) as shipped_orders,
                COUNT(CASE WHEN order_status = 'delivered' THEN 1 END) as delivered_orders,
                COUNT(CASE WHEN priority_level = 'urgent' THEN 1 END) as urgent_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue
            FROM orders_enhanced
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetch_assoc();

        echo json_encode([
            'success' => true,
            'stats' => [
                'total_orders' => (int)$stats['total_orders'],
                'pending_orders' => (int)$stats['pending_orders'],
                'processing_orders' => (int)$stats['processing_orders'],
                'shipped_orders' => (int)$stats['shipped_orders'],
                'delivered_orders' => (int)$stats['delivered_orders'],
                'urgent_orders' => (int)$stats['urgent_orders'],
                'total_revenue' => (float)$stats['total_revenue']
            ]
        ]);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action parameter']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>