<?php
// API Endpoint: Get Brand Data for Editing
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $brand_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($brand_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid brand ID']);
        exit;
    }

    // Get brand data
    $stmt = $conn->prepare("SELECT * FROM vehicle_brands_enhanced WHERE id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $brand = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'brand' => $brand
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Brand not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>