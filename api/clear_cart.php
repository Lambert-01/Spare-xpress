<?php
// API endpoint to clear all items from cart
// SPARE XPRESS LTD - Clear Cart API

ob_start();
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start(['secure' => false]);

// Prevent any HTML output for API responses
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Clear the cart
unset($_SESSION['cart']);

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'Cart cleared successfully'
]);
?>
