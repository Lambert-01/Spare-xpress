<?php
// API endpoint to update cart items (quantity changes, removals)
// SPARE XPRESS LTD - Cart Update API

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

    try {
        // Find and update the item in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                if ($quantity <= 0) {
                    // Remove item from cart
                    $item = null;
                    $response['message'] = 'Item removed from cart';
                } else {
                    // Check stock availability
                    if ($item['max_stock'] > 0 && $quantity > $item['max_stock']) {
                        $response['message'] = 'Cannot add more items. Only ' . $item['max_stock'] . ' available.';
                        echo json_encode($response);
                        exit;
                    }

                    // Update quantity and subtotal
                    $item['quantity'] = $quantity;
                    $item['subtotal'] = $item['price'] * $quantity;
                    $response['message'] = 'Cart updated successfully';
                }
                $found = true;
                break;
            }
        }

        if (!$found) {
            $response['message'] = 'Item not found in cart';
            echo json_encode($response);
            exit;
        }

        // Remove null items (deleted items)
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) {
            return $item !== null;
        });

        // Reindex array
        $_SESSION['cart'] = array_values($_SESSION['cart']);

        // Calculate cart totals
        $cart_total = 0;
        $cart_count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cart_total += $item['subtotal'];
            $cart_count += $item['quantity'];
        }

        $response['success'] = true;
        $response['cart_count'] = $cart_count;
        $response['cart_total'] = number_format($cart_total, 0, '.', ',');
        $response['formatted_total'] = 'RWF ' . number_format($cart_total, 0, '.', ',');

    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>