<?php
header('Content-Type: application/json');
include '../includes/config.php';
include '../admin/logs/error_log.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    ErrorLogger::logError("Invalid category ID in get_category", ['id' => $_GET['id'] ?? 'not set']);
    echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
    exit;
}

$category_id = (int)$_GET['id'];
ErrorLogger::logSuccess("Fetching category data for ID: $category_id");

$stmt = $conn->prepare("SELECT * FROM categories_enhanced WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    ErrorLogger::logError("Category not found for ID: $category_id");
    echo json_encode(['success' => false, 'message' => 'Category not found']);
    exit;
}

$category = $result->fetch_assoc();
ErrorLogger::logSuccess("Category data fetched successfully for ID: $category_id", ['category_name' => $category['category_name']]);

echo json_encode(['success' => true, 'category' => $category]);
?>