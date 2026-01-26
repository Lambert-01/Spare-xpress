<?php
// API Endpoint: Get Urgent Orders for Dashboard Alerts
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get urgent orders (high priority or pending for more than 24 hours)
    $query = "
        SELECT
            COUNT(*) as urgent_count,
            COUNT(CASE WHEN priority_level = 'urgent' THEN 1 END) as urgent_priority,
            COUNT(CASE WHEN order_status = 'pending' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24 THEN 1 END) as overdue_pending,
            COUNT(CASE WHEN order_status = 'processing' AND TIMESTAMPDIFF(HOUR, status_updated_at, NOW()) > 48 THEN 1 END) as stuck_processing
        FROM orders_enhanced
        WHERE (priority_level = 'urgent'
                OR (order_status = 'pending' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24)
                OR (order_status = 'processing' AND TIMESTAMPDIFF(HOUR, status_updated_at, NOW()) > 48))
        AND order_status NOT IN ('delivered', 'cancelled', 'failed')
    ";

    $result = $conn->query($query);
    $stats = $result->fetch_assoc();

    // Get recent urgent orders - FIXED QUERY
    $urgent_orders_query = "
        SELECT o.id, o.order_number, CONCAT(c.first_name, ' ', c.last_name) as customer_name,
               o.priority_level, o.order_status,
               TIMESTAMPDIFF(HOUR, o.created_at, NOW()) as hours_old
        FROM orders_enhanced o
        LEFT JOIN customers_enhanced c ON o.customer_id = c.id
        WHERE (o.priority_level = 'urgent'
                OR (o.order_status = 'pending' AND TIMESTAMPDIFF(HOUR, o.created_at, NOW()) > 24)
                OR (o.order_status = 'processing' AND TIMESTAMPDIFF(HOUR, o.status_updated_at, NOW()) > 48))
        AND o.order_status NOT IN ('delivered', 'cancelled', 'failed')
        ORDER BY
            CASE
                WHEN o.priority_level = 'urgent' THEN 1
                WHEN o.order_status = 'pending' AND TIMESTAMPDIFF(HOUR, o.created_at, NOW()) > 24 THEN 2
                WHEN o.order_status = 'processing' AND TIMESTAMPDIFF(HOUR, o.status_updated_at, NOW()) > 48 THEN 3
                ELSE 4
            END,
            o.created_at DESC
        LIMIT 5
    ";

    $urgent_result = $conn->query($urgent_orders_query);
    $urgent_orders = [];

    while ($order = $urgent_result->fetch_assoc()) {
        $urgent_orders[] = [
            'id' => $order['id'],
            'order_number' => $order['order_number'],
            'customer_name' => $order['customer_name'],
            'priority' => $order['priority_level'],
            'status' => $order['order_status'],
            'hours_old' => $order['hours_old'],
            'urgency_reason' => $order['priority_level'] === 'urgent' ? 'High Priority' :
                                ($order['order_status'] === 'pending' ? 'Overdue Pending' : 'Stuck in Processing')
        ];
    }

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_urgent' => (int)$stats['urgent_count'],
            'urgent_priority' => (int)$stats['urgent_priority'],
            'overdue_pending' => (int)$stats['overdue_pending'],
            'stuck_processing' => (int)$stats['stuck_processing']
        ],
        'urgent_orders' => $urgent_orders
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>