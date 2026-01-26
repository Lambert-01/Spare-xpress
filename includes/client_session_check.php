<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if customer is logged in
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['customer_name']) || !isset($_SESSION['customer_email'])) {
    // Store current URL for redirect after login
    $current_url = $_SERVER['REQUEST_URI'];
    header("Location: /pages/login.php?redirect=" . urlencode($current_url));
    exit();
}

// Verify customer still exists in database and is active
// This prevents issues if account was deleted while session is active
if (isset($_SESSION['customer_id'])) {
    include 'config.php';
    $stmt = $conn->prepare("SELECT id, customer_status FROM customers_enhanced WHERE id = ? AND customer_status = 'active'");
    $stmt->bind_param("i", $_SESSION['customer_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Customer account not found or inactive - destroy session
        session_destroy();
        header("Location: /pages/login.php?error=account_disabled");
        exit();
    }
    $stmt->close();
}
?>