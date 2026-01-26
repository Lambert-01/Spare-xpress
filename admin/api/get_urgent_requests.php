<?php
// API Endpoint: Get Urgent Requests for Dashboard Alerts
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get urgent requests (high priority or pending for more than 24 hours)
    $query = "
        SELECT
            COUNT(*) as urgent_count,
            COUNT(CASE WHEN order_type = 'urgent' THEN 1 END) as urgent_priority,
            COUNT(CASE WHEN status = 'pending' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24 THEN 1 END) as overdue_pending,
            COUNT(CASE WHEN status = 'processing' AND TIMESTAMPDIFF(HOUR, updated_at, NOW()) > 48 THEN 1 END) as stuck_processing
        FROM order_requests
        WHERE (order_type = 'urgent'
                OR (status = 'pending' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 24)
                OR (status = 'processing' AND TIMESTAMPDIFF(HOUR, updated_at, NOW()) > 48))
        AND status NOT IN ('delivered', 'cancelled', 'failed')
    ";

    $result = $conn->query($query);
    $stats = $result->fetch_assoc();

    // Get recent urgent requests
    $urgent_requests_query = "
        SELECT o.id, o.part_name, o.customer_name, o.order_type, o.status,
               TIMESTAMPDIFF(HOUR, o.created_at, NOW()) as hours_old
        FROM order_requests o
        WHERE (o.order_type = 'urgent'
                OR (o.status = 'pending' AND TIMESTAMPDIFF(HOUR, o.created_at, NOW()) > 24)
                OR (o.status = 'processing' AND TIMESTAMPDIFF(HOUR, o.updated_at, NOW()) > 48))
        AND o.status NOT IN ('delivered', 'cancelled', 'failed')
        ORDER BY
            CASE
                WHEN o.order_type = 'urgent' THEN 1
                WHEN o.status = 'pending' AND TIMESTAMPDIFF(HOUR, o.created_at, NOW()) > 24 THEN 2
                WHEN o.status = 'processing' AND TIMESTAMPDIFF(HOUR, o.updated_at, NOW()) > 48 THEN 3
                ELSE 4
            END,
            o.created_at DESC
        LIMIT 5
    ";

    $urgent_result = $conn->query($urgent_requests_query);
    $urgent_requests = [];

    while ($request = $urgent_result->fetch_assoc()) {
        $urgent_requests[] = [
            'id' => $request['id'],
            'part_name' => $request['part_name'],
            'customer_name' => $request['customer_name'],
            'priority' => $request['order_type'],
            'status' => $request['status'],
            'hours_old' => $request['hours_old'],
            'urgency_reason' => $request['order_type'] === 'urgent' ? 'High Priority' :
                                ($request['status'] === 'pending' ? 'Overdue Pending' : 'Stuck in Processing')
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
        'urgent_requests' => $urgent_requests
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>