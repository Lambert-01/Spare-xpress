<?php
// API endpoint to get models for a specific brand
// SPARE XPRESS LTD - Get Models by Brand API

include_once '../includes/config.php';

// Prevent any HTML output for API responses
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;

    if (empty($brand_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand ID is required'
        ]);
        exit;
    }

    // Get models for the specified brand
    $model_sql = "SELECT m.id, m.model_name
                  FROM vehicle_models_enhanced m
                  WHERE m.brand_id = ? AND m.is_active = 1
                  ORDER BY m.model_name";

    $stmt = $conn->prepare($model_sql);
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $models = [];
    while ($row = $result->fetch_assoc()) {
        $models[] = [
            'id' => (int)$row['id'],
            'model_name' => $row['model_name']
        ];
    }

    echo json_encode([
        'success' => true,
        'models' => $models
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>