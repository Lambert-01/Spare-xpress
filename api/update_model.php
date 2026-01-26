<?php
file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Update model API called\n", FILE_APPEND);
include '../admin/includes/config.php';
include '../admin/includes/auth.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - After includes\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$id = (int)$_POST['model_id'];
$brand_id = (int)$_POST['brand_id'];
$model_name = trim($_POST['model_name']);
$slug = generateSlug($model_name);
$year_from = !empty($_POST['year_from']) ? (int)$_POST['year_from'] : null;
$year_to = !empty($_POST['year_to']) ? (int)$_POST['year_to'] : null;
$engine_types = isset($_POST['engine_types']) ? json_encode($_POST['engine_types']) : '[]';
$fuel_types = isset($_POST['fuel_types']) ? json_encode($_POST['fuel_types']) : '[]';
$transmission_types = isset($_POST['transmission_types']) ? json_encode($_POST['transmission_types']) : '[]';
$body_types = isset($_POST['body_types']) ? json_encode($_POST['body_types']) : '[]';
$compatibility_info = trim($_POST['compatibility_info']);
$technical_specs = trim($_POST['technical_specs']);

// Validate and format technical specs
if (!empty($technical_specs)) {
    $decoded = json_decode($technical_specs);
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        // Invalid JSON, set to empty JSON object
        $technical_specs = '{}';
        file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Invalid JSON for technical_specs, using empty object\n", FILE_APPEND);
    }
} else {
    $technical_specs = '{}';
}

$is_active = isset($_POST['is_active']) ? 1 : 0;

$model_image = $_POST['existing_model_image'] ?? '';

if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/models/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $file_extension = pathinfo($_FILES['model_image']['name'], PATHINFO_EXTENSION);
    $file_name = $slug . '_model.' . $file_extension;
    $target_path = $upload_dir . $file_name;
    if (move_uploaded_file($_FILES['model_image']['tmp_name'], $target_path)) {
            $model_image = '/uploads/models/' . $file_name;
            file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Image uploaded successfully for model ID: $id\n", FILE_APPEND);
        } else {
            file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Failed to upload image for model ID: $id\n", FILE_APPEND);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to upload model image.']);
            exit;
        }
}

// Build dynamic query for null handling
$set_parts = [
    "brand_id = ?",
    "model_name = ?",
    "slug = ?",
    "model_image = ?",
    "engine_types = ?",
    "fuel_types = ?",
    "transmission_types = ?",
    "body_types = ?",
    "compatibility_info = ?",
    "technical_specs = ?",
    "is_active = ?"
];
$params = [$brand_id, $model_name, $slug, $model_image, $engine_types, $fuel_types, $transmission_types, $body_types, $compatibility_info, $technical_specs, $is_active];
$types = 'isssssssssi';

if ($year_from !== null) {
    $set_parts[] = "year_from = ?";
    $params[] = $year_from;
    $types .= 'i';
} else {
    $set_parts[] = "year_from = NULL";
}

if ($year_to !== null) {
    $set_parts[] = "year_to = ?";
    $params[] = $year_to;
    $types .= 'i';
} else {
    $set_parts[] = "year_to = NULL";
}

$query = "UPDATE vehicle_models_enhanced SET " . implode(', ', $set_parts) . " WHERE id = ?";
$params[] = $id;
$types .= 'i';
file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Update query: $query\n", FILE_APPEND);

$stmt = $conn->prepare($query);
$bind_params = [$types];
foreach ($params as &$param) {
    $bind_params[] = &$param;
}
call_user_func_array([$stmt, 'bind_param'], $bind_params);

try {
    if ($stmt->execute()) {
        file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Model updated successfully for ID: $id\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Model updated successfully!']);
    } else {
        file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Failed to update model for ID: $id, Error: " . $conn->error . "\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update model: ' . $conn->error]);
    }
} catch (mysqli_sql_exception $e) {
    file_put_contents("../admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Exception updating model for ID: $id, Error: " . $e->getMessage() . "\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Failed to update model: ' . $e->getMessage()]);
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}
?>