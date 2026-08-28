<?php
// Toggle Status API - Cascade active/inactive for brands, models, products
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

include_once '../includes/config.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$type = $input['type'] ?? '';       // 'brand', 'model', 'product'
$id = (int)($input['id'] ?? 0);
$is_active = (int)($input['is_active'] ?? -1);

if (empty($type) || $id <= 0 || $is_active < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields: type, id, is_active']);
    exit;
}

$affected = ['brands' => 0, 'models' => 0, 'products' => 0];

try {
    $conn->begin_transaction();

    if ($type === 'brand') {
        // Update brand status
        $stmt = $conn->prepare("UPDATE vehicle_brands_enhanced SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_active, $id);
        $stmt->execute();
        $affected['brands'] = $stmt->affected_rows;
        $stmt->close();

        // CASCADE: Deactivating a brand deactivates ALL its models and products
        if ($is_active === 0) {
            $stmt = $conn->prepare("UPDATE vehicle_models_enhanced SET is_active = 0 WHERE brand_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $affected['models'] = $stmt->affected_rows;
            $stmt->close();

            $stmt = $conn->prepare("UPDATE products_enhanced SET is_active = 0 WHERE brand_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $affected['products'] = $stmt->affected_rows;
            $stmt->close();
        }

    } elseif ($type === 'model') {
        // Update model status
        $stmt = $conn->prepare("UPDATE vehicle_models_enhanced SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_active, $id);
        $stmt->execute();
        $affected['models'] = $stmt->affected_rows;

        // Get brand_id for this model
        $stmt2 = $conn->prepare("SELECT brand_id FROM vehicle_models_enhanced WHERE id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $brand_id = $stmt2->get_result()->fetch_assoc()['brand_id'] ?? 0;
        $stmt2->close();
        $stmt->close();

        // CASCADE: Deactivating a model deactivates its products
        if ($is_active === 0) {
            $stmt = $conn->prepare("UPDATE products_enhanced SET is_active = 0 WHERE model_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $affected['products'] = $stmt->affected_rows;
            $stmt->close();
        }

        // AUTO-ACTIVATE: If activating a model, check if its brand is active
        if ($is_active === 1 && $brand_id > 0) {
            $stmt = $conn->prepare("UPDATE vehicle_brands_enhanced SET is_active = 1 WHERE id = ? AND is_active = 0");
            $stmt->bind_param("i", $brand_id);
            $stmt->execute();
            $affected['brands'] = $stmt->affected_rows;
            $stmt->close();
        }

    } elseif ($type === 'product') {
        // Update product status
        $stmt = $conn->prepare("UPDATE products_enhanced SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_active, $id);
        $stmt->execute();
        $affected['products'] = $stmt->affected_rows;

        // Get brand_id and model_id
        $stmt2 = $conn->prepare("SELECT brand_id, model_id FROM products_enhanced WHERE id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $prod = $stmt2->get_result()->fetch_assoc();
        $brand_id = $prod['brand_id'] ?? 0;
        $model_id = $prod['model_id'] ?? 0;
        $stmt2->close();
        $stmt->close();

        // AUTO-ACTIVATE: If activating a product, also activate its brand and model
        if ($is_active === 1) {
            if ($brand_id > 0) {
                $stmt = $conn->prepare("UPDATE vehicle_brands_enhanced SET is_active = 1 WHERE id = ? AND is_active = 0");
                $stmt->bind_param("i", $brand_id);
                $stmt->execute();
                $affected['brands'] = $stmt->affected_rows;
                $stmt->close();
            }
            if ($model_id > 0) {
                $stmt = $conn->prepare("UPDATE vehicle_models_enhanced SET is_active = 1 WHERE id = ? AND is_active = 0");
                $stmt->bind_param("i", $model_id);
                $stmt->execute();
                $affected['models'] = $stmt->affected_rows;
                $stmt->close();
            }
        }

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid type. Must be: brand, model, or product']);
        exit;
    }

    $conn->commit();

    $message = $is_active ? 'Activated' : 'Deactivated';
    $cascade_msg = '';
    if ($type === 'brand' && $is_active === 0) {
        $cascade_msg = " (cascade: {$affected['models']} models, {$affected['products']} products deactivated)";
    } elseif ($type === 'model' && $is_active === 0) {
        $cascade_msg = " (cascade: {$affected['products']} products deactivated)";
    } elseif ($type === 'product' && $is_active === 1) {
        if ($affected['brands'] > 0 || $affected['models'] > 0) {
            $cascade_msg = " (auto-activated parent brand/model)";
        }
    }

    echo json_encode([
        'success' => true,
        'message' => ucfirst($type) . ' ' . $message . $cascade_msg,
        'is_active' => $is_active,
        'affected' => $affected
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
