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
    $brand_name = isset($_GET['brand']) ? trim($_GET['brand']) : '';

    // If brand name is provided but not ID, look up the ID
    if (empty($brand_id) && !empty($brand_name)) {
        $brand_lookup_sql = "SELECT id FROM vehicle_brands_enhanced WHERE brand_name = ? AND is_active = 1 LIMIT 1";
        $brand_stmt = $conn->prepare($brand_lookup_sql);
        $brand_stmt->bind_param("s", $brand_name);
        $brand_stmt->execute();
        $brand_result = $brand_stmt->get_result();
        
        if ($brand_result->num_rows > 0) {
            $brand_row = $brand_result->fetch_assoc();
            $brand_id = (int)$brand_row['id'];
        }
        $brand_stmt->close();
    }

    if (empty($brand_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Brand ID is required'
        ]);
        exit;
    }

    // Get models for the specified brand with product count
    $model_sql = "SELECT m.id, m.model_name, COUNT(DISTINCT p.id) as count
                  FROM vehicle_models_enhanced m
                  LEFT JOIN products_enhanced p ON p.model_id = m.id
                  WHERE m.brand_id = ? AND m.is_active = 1
                  GROUP BY m.id, m.model_name
                  HAVING count > 0
                  ORDER BY m.model_name";

    $stmt = $conn->prepare($model_sql);
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $models = [];
    while ($row = $result->fetch_assoc()) {
        $models[] = [
            'id' => (int)$row['id'],
            'name' => $row['model_name'],
            'count' => (int)$row['count']
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