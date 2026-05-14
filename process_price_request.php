<?php
require_once __DIR__ . '/includes/session_init.php';
spx_session_start(['secure' => false]);

include __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$product_id = (int)($_POST['product_id'] ?? 0);
$part_name = trim((string)($_POST['part_name'] ?? ''));
$full_name = trim((string)($_POST['full_name'] ?? ''));
$phone_number = trim((string)($_POST['phone_number'] ?? ''));
$car_model = trim((string)($_POST['car_model'] ?? ''));
$quantity = (int)($_POST['quantity'] ?? 0);

$errors = [];

if ($part_name === '' && $product_id > 0) {
    $stmt = $conn->prepare("SELECT product_name FROM products_enhanced WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row && !empty($row['product_name'])) {
            $part_name = (string)$row['product_name'];
        }
    }
}

if ($part_name === '') $errors[] = 'Part name is required';
if ($full_name === '') $errors[] = 'Name is required';
if ($phone_number === '') $errors[] = 'Phone number is required';
if ($car_model === '') $errors[] = 'Car model is required';
if ($quantity < 1) $errors[] = 'Quantity must be at least 1';

if ($phone_number !== '' && !preg_match('/^\+?250[0-9]{9}$/', $phone_number)) {
    $errors[] = 'Please enter a valid Rwandan phone number (e.g., +250790123456)';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit();
}

try {
    $conn->query("
        CREATE TABLE IF NOT EXISTS price_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NULL,
            product_id INT NULL,
            product_name VARCHAR(255) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            customer_name VARCHAR(255) NOT NULL,
            phone_number VARCHAR(32) NOT NULL,
            car_model VARCHAR(255) NOT NULL,
            status ENUM('pending','quoted','approved','deposit_paid','delivered','cancelled') NOT NULL DEFAULT 'pending',
            quoted_price DECIMAL(12,2) NULL,
            currency VARCHAR(10) NOT NULL DEFAULT 'RWF',
            notes TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NULL DEFAULT NULL,
            INDEX idx_status (status),
            INDEX idx_created_at (created_at),
            INDEX idx_product_id (product_id),
            INDEX idx_customer_id (customer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $customer_id = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;

    $stmt = $conn->prepare("
        INSERT INTO price_requests
            (customer_id, product_id, product_name, quantity, customer_name, phone_number, car_model, status, created_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");

    if (!$stmt) {
        throw new Exception('Failed to prepare request insert: ' . ($conn->error ?: 'Unknown error'));
    }

    $stmt->bind_param(
        "iisisss",
        $customer_id,
        $product_id,
        $part_name,
        $quantity,
        $full_name,
        $phone_number,
        $car_model
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to submit price request: ' . ($stmt->error ?: 'Unknown error'));
    }

    $request_id = (int)$conn->insert_id;

    echo json_encode([
        'success' => true,
        'message' => "Request submitted! Ref: PR-$request_id. Our team will calculate today's price and contact you."
    ]);
    exit();
} catch (Throwable $e) {
    error_log('process_price_request failed: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while submitting your request. Please try again.'
    ]);
    exit();
}
