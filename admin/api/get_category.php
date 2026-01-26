<?php
// API Endpoint: Get Category Data for Editing
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Include authentication and database connection
include '../includes/auth.php';
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($category_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
        exit;
    }

    // Get category data
    $stmt = $conn->prepare("SELECT * FROM categories_enhanced WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();

        // Get parent category name if exists
        if ($category['parent_id']) {
            $parent_stmt = $conn->prepare("SELECT category_name FROM categories_enhanced WHERE id = ?");
            $parent_stmt->bind_param("i", $category['parent_id']);
            $parent_stmt->execute();
            $parent_result = $parent_stmt->get_result();
            if ($parent_result->num_rows > 0) {
                $parent = $parent_result->fetch_assoc();
                $category['parent_name'] = $parent['category_name'];
            }
        }

        echo json_encode([
            'success' => true,
            'category' => $category
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Category not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>