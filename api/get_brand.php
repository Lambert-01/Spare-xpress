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

include '../includes/config.php';
include '../admin/logs/error_log.php';

$brand_id = $_GET['id'] ?? null;

if (!$brand_id) {
    ErrorLogger::logError("Brand ID is required in get_brand", ['id' => $brand_id]);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Brand ID is required'
    ]);
    exit();
}

ErrorLogger::logSuccess("Fetching brand data for ID: $brand_id");

try {
    // Get brand details
    $query = "SELECT * FROM vehicle_brands_enhanced WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        ErrorLogger::logError("Brand not found for ID: $brand_id");
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Brand not found'
        ]);
        exit();
    }

    $brand = $result->fetch_assoc();
    ErrorLogger::logSuccess("Brand data fetched successfully for ID: $brand_id", ['brand_name' => $brand['brand_name']]);

    echo json_encode([
        'success' => true,
        'brand' => $brand
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>