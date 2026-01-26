<?php
include '../admin/includes/config.php';
include '../admin/includes/auth.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Invalid model ID requested: " . ($_GET['id'] ?? 'none') . "\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid model ID']);
    exit;
}

$model_id = (int)$_GET['id'];
file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Fetching model data for ID: $model_id\n", FILE_APPEND);

// Fetch model data
$stmt = $conn->prepare("SELECT * FROM vehicle_models_enhanced WHERE id = ?");
$stmt->bind_param("i", $model_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Model not found for ID: $model_id\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Model not found']);
    exit;
}

$model = $result->fetch_assoc();
file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Model data fetched successfully for ID: $model_id\n", FILE_APPEND);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'model' => $model]);
?>