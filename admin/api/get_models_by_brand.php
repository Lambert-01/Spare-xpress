<?php
// API Endpoint: Get Models by Brand for Product Management
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;

    if ($brand_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid brand ID']);
        exit;
    }

    // Get models for the specified brand
    $stmt = $conn->prepare("
        SELECT id, model_name, year_from, year_to, is_active
        FROM vehicle_models_enhanced
        WHERE brand_id = ? AND is_active = 1
        ORDER BY model_name
    ");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $models = [];
    while ($row = $result->fetch_assoc()) {
        $models[] = [
            'id' => $row['id'],
            'model_name' => $row['model_name'],
            'year_range' => ($row['year_from'] && $row['year_to']) ?
                $row['year_from'] . '-' . $row['year_to'] :
                ($row['year_from'] ? 'From ' . $row['year_from'] : 'Until ' . $row['year_to'])
        ];
    }

    echo json_encode([
        'success' => true,
        'models' => $models
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>