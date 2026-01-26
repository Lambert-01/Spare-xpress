<?php
include '../includes/auth.php';
include '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if (!$notification_id) {
    echo json_encode(['success' => false, 'message' => 'Notification ID required']);
    exit;
}

try {
    $query = "
        SELECT
            n.*,
            CASE
                WHEN n.type = 'message' THEN c.first_name
                WHEN n.type = 'system' THEN cs.first_name
            END as first_name,
            CASE
                WHEN n.type = 'message' THEN c.last_name
                WHEN n.type = 'system' THEN cs.last_name
            END as last_name,
            CASE
                WHEN n.type = 'message' THEN c.email
                WHEN n.type = 'system' THEN cs.email
            END as email,
            CASE
                WHEN n.type = 'message' THEN c.phone
                WHEN n.type = 'system' THEN cs.phone
            END as phone,
            conv.last_message
        FROM notifications n
        LEFT JOIN customers_enhanced c ON n.user_id = c.id AND n.type = 'message'
        LEFT JOIN customers_enhanced cs ON n.user_id = cs.id AND n.type = 'system'
        LEFT JOIN conversations conv ON n.reference_id = conv.id AND n.type = 'message'
        WHERE n.id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $notification = $result->fetch_assoc();

        $response = ['success' => true, 'notification' => $notification];

        // If it's a message notification, include the full conversation messages
        if ($notification['type'] === 'message' && $notification['reference_id']) {
            $msg_query = "
                SELECT m.*, c.first_name, c.last_name
                FROM messages m
                LEFT JOIN conversations conv ON m.conversation_id = conv.id
                LEFT JOIN customers_enhanced c ON m.sender_type = 'client' AND c.id = conv.client_id
                WHERE m.conversation_id = ?
                ORDER BY m.created_at ASC
            ";
            $msg_stmt = $conn->prepare($msg_query);
            $msg_stmt->bind_param("i", $notification['reference_id']);
            $msg_stmt->execute();
            $messages = $msg_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $response['messages'] = $messages;
        }

        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Notification not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>