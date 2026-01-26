<?php
// API Endpoint: Get Model Data for Editing
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $model_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($model_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid model ID']);
        exit;
    }

    // Get model data with brand information
    $stmt = $conn->prepare("
        SELECT m.*, b.brand_name
        FROM vehicle_models_enhanced m
        LEFT JOIN vehicle_brands_enhanced b ON m.brand_id = b.id
        WHERE m.id = ?
    ");
    $stmt->bind_param("i", $model_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $model = $result->fetch_assoc();


        echo json_encode([
            'success' => true,
            'model' => $model
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Model not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>