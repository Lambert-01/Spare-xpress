<?php
// Bulk Brand Actions API for SPARE XPRESS LTD Admin Panel
header('Content-Type: application/json');
include '../../includes/config.php';
include '../includes/auth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get action and IDs
$action = $_POST['action'] ?? '';
$ids = $_POST['ids'] ?? [];

if (empty($action) || empty($ids) || !is_array($ids)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters']);
    exit;
}

// Validate action
$allowed_actions = ['activate', 'deactivate', 'delete'];
if (!in_array($action, $allowed_actions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Sanitize IDs
$sanitized_ids = array_map('intval', $ids);
$sanitized_ids = array_filter($sanitized_ids, function($id) { return $id > 0; });

if (empty($sanitized_ids)) {
    echo json_encode(['success' => false, 'message' => 'No valid IDs provided']);
    exit;
}

try {
    $conn->begin_transaction();

    $placeholders = str_repeat('?,', count($sanitized_ids) - 1) . '?';

    switch ($action) {
        case 'activate':
            $stmt = $conn->prepare("UPDATE vehicle_brands_enhanced SET is_active = 1 WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($sanitized_ids)), ...$sanitized_ids);
            $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $message = "Successfully activated $affected_rows brand(s)";
            break;

        case 'deactivate':
            $stmt = $conn->prepare("UPDATE vehicle_brands_enhanced SET is_active = 0 WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($sanitized_ids)), ...$sanitized_ids);
            $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $message = "Successfully deactivated $affected_rows brand(s)";
            break;

        case 'delete':
            // Check for associated models and products first
            $check_stmt = $conn->prepare("
                SELECT
                    COUNT(DISTINCT vm.id) as model_count,
                    COUNT(DISTINCT p.id) as product_count
                FROM vehicle_brands_enhanced vb
                LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
                LEFT JOIN products_enhanced p ON vb.id = p.brand_id
                WHERE vb.id IN ($placeholders)
            ");
            $check_stmt->bind_param(str_repeat('i', count($sanitized_ids)), ...$sanitized_ids);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result()->fetch_assoc();

            if ($check_result['model_count'] > 0 || $check_result['product_count'] > 0) {
                throw new Exception('Cannot delete brands with associated models or products. Please reassign or delete them first.');
            }

            $stmt = $conn->prepare("DELETE FROM vehicle_brands_enhanced WHERE id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($sanitized_ids)), ...$sanitized_ids);
            $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $message = "Successfully deleted $affected_rows brand(s)";
            break;
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => $message]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>