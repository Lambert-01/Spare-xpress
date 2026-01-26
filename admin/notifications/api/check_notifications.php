<?php
// API Endpoint for checking new notifications
header('Content-Type: application/json');
include '../../includes/config.php';

// Get unread notification count
$result = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
$unread_count = $result->fetch_assoc()['count'];

// Get recent notifications (last 5)
$recent_notifications = $conn->query("
    SELECT id, notification_type, title, message, created_at, priority
    FROM notifications
    WHERE is_read = 0
    ORDER BY created_at DESC
    LIMIT 5
");

$notifications = [];
while ($notification = $recent_notifications->fetch_assoc()) {
    $notifications[] = [
        'id' => $notification['id'],
        'type' => $notification['notification_type'],
        'title' => $notification['title'],
        'message' => substr($notification['message'], 0, 100) . (strlen($notification['message']) > 100 ? '...' : ''),
        'time' => date('M d, H:i', strtotime($notification['created_at'])),
        'priority' => $notification['priority']
    ];
}

echo json_encode([
    'success' => true,
    'unread_count' => $unread_count,
    'new_count' => $unread_count, // For backward compatibility
    'recent_notifications' => $notifications
]);
?>