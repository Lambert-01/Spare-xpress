<?php
// API endpoint to add products to cart
// SPARE XPRESS LTD - Cart Management API

session_start();
include_once '../includes/config.php';

// Prevent any HTML output for API responses
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID';
        echo json_encode($response);
        exit;
    }

    if ($quantity <= 0) {
        $response['message'] = 'Invalid quantity';
        echo json_encode($response);
        exit;
    }

    try {
        // Get product details from database
         $sql = "SELECT p.*, b.brand_name, m.model_name, c.category_name
                 FROM products_enhanced p
                 LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
                 LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
                 LEFT JOIN categories_enhanced c ON p.category_id = c.id
                 WHERE p.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response['message'] = 'Product not found';
            echo json_encode($response);
            exit;
        }

        $product = $result->fetch_assoc();
        $stmt->close();

        // Check stock availability
         if ($product['stock_quantity'] < $quantity && $product['stock_quantity'] > 0) {
             $response['message'] = 'Only ' . $product['stock_quantity'] . ' items available in stock';
             echo json_encode($response);
             exit;
         }

        // Format image path
        $image_path = $product['main_image'];
        if (!empty($image_path) && !str_starts_with($image_path, '/')) {
            $image_path = '/' . $image_path;
        }

        // Create cart item
        $cart_item = [
            'id' => $product['id'],
            'name' => $product['product_name'],
            'price' => (float)$product['regular_price'],
            'brand' => $product['brand_name'],
            'model' => $product['model_name'],
            'category' => $product['category_name'],
            'image' => $image_path ?: '/img/no-image.png',
            'quantity' => $quantity,
            'stock' => (int)$product['stock_quantity'],
            'max_stock' => (int)$product['stock_quantity'],
            'subtotal' => (float)$product['regular_price'] * $quantity
        ];

        // Check if product already in cart
         $found = false;
         foreach ($_SESSION['cart'] as &$item) {
             if ($item['id'] == $product_id) {
                 $new_quantity = $item['quantity'] + $quantity;
                 if ($product['stock_quantity'] > 0 && $new_quantity > $product['stock_quantity']) {
                     $response['message'] = 'Cannot add more items. Only ' . $product['stock_quantity'] . ' available.';
                     echo json_encode($response);
                     exit;
                 }
                 $item['quantity'] = $new_quantity;
                 $item['subtotal'] = $item['price'] * $new_quantity;
                 $found = true;
                 break;
             }
         }

        if (!$found) {
            $_SESSION['cart'][] = $cart_item;
        }

        // Calculate cart totals
        $cart_total = 0;
        $cart_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_total += $item['subtotal'];
            $cart_count += $item['quantity'];
        }

        $response = [
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $cart_count,
            'cart_total' => number_format($cart_total, 0, '.', ','),
            'cart_item' => $cart_item
        ];

    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>