<?php
// API endpoint to get cart data
// SPARE XPRESS LTD - Cart Data API

session_start();
include_once '../includes/config.php';

// Prevent any HTML output for API responses
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_total = 0;
$cart_count = 0;

foreach ($cart as $item) {
    $cart_total += $item['subtotal'];
    $cart_count += $item['quantity'];
}

echo json_encode([
    'success' => true,
    'cart' => $cart,
    'cart_count' => $cart_count,
    'cart_total' => $cart_total,
    'formatted_total' => 'RWF ' . number_format($cart_total, 0, '.', ',')
]);
?>