<?php
require_once __DIR__ . '/../includes/auth.php';
include '../includes/config.php';

header('Content-Type: application/json');

$brand_id = isset($_GET['brand_id']) ? (int) $_GET['brand_id'] : 0;

if ($brand_id <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM vehicle_models_enhanced WHERE brand_id = ? ORDER BY display_order, model_name");
$stmt->bind_param('i', $brand_id);
$stmt->execute();
$result = $stmt->get_result();

$models = [];
while ($row = $result->fetch_assoc()) {
    $models[] = $row;
}

echo json_encode($models);
