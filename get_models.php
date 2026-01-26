<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'includes/config.php';

$brand_slug = $_GET['brand'] ?? '';

if (empty($brand_slug)) {
    echo json_encode([
        'success' => false,
        'error' => 'Brand parameter is required'
    ]);
    exit();
}

try {
    // First get the brand ID from the slug
    $brand_query = "SELECT id FROM vehicle_brands_enhanced WHERE slug = ? AND is_active = 1";
    $brand_stmt = $conn->prepare($brand_query);
    $brand_stmt->bind_param("s", $brand_slug);
    $brand_stmt->execute();
    $brand_result = $brand_stmt->get_result();
    
    if ($brand_result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'error' => 'Brand not found'
        ]);
        exit();
    }
    
    $brand_row = $brand_result->fetch_assoc();
    $brand_id = $brand_row['id'];
    
    // Now get all models for this brand
    $models_query = "SELECT model_name FROM vehicle_models_enhanced WHERE brand_id = ? AND is_active = 1 ORDER BY display_order, model_name";
    $models_stmt = $conn->prepare($models_query);
    $models_stmt->bind_param("i", $brand_id);
    $models_stmt->execute();
    $models_result = $models_stmt->get_result();
    
    $models = [];
    while ($model_row = $models_result->fetch_assoc()) {
        $models[] = $model_row['model_name'];
    }
    
    echo json_encode([
        'success' => true,
        'models' => $models
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>