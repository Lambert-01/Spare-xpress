<?php
// API Endpoint: Check for New Notifications
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get unread notifications count
    $unread_count = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0")->fetch_assoc()['count'];

    // Get recent unread notifications (last 10)
    $recent_notifications = $conn->query("
        SELECT id, type, created_at
        FROM notifications
        WHERE is_read = 0
        ORDER BY created_at DESC
        LIMIT 10
    ");

    $notifications = [];
    while ($notif = $recent_notifications->fetch_assoc()) {
        $notifications[] = [
            'id' => $notif['id'],
            'type' => $notif['type'],
            'title' => ucfirst($notif['type']) . ' Notification', // Generate title from type
            'message' => 'You have a new ' . $notif['type'] . ' notification.', // Default message
            'time' => date('M d, H:i', strtotime($notif['created_at'])),
            'timestamp' => strtotime($notif['created_at'])
        ];
    }

    // Get notification stats by type
    $stats_query = $conn->query("
        SELECT type, COUNT(*) as count
        FROM notifications
        WHERE is_read = 0
        GROUP BY type
    ");

    $stats = [];
    while ($stat = $stats_query->fetch_assoc()) {
        $stats[$stat['type']] = (int)$stat['count'];
    }

    echo json_encode([
        'success' => true,
        'unread_count' => (int)$unread_count,
        'notifications' => $notifications,
        'stats' => $stats
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>