<?php
// Process checkout and create order
// SPARE XPRESS LTD - Order Processing

session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Get POST data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$region = trim($_POST['region'] ?? '');
$vehicle_info = trim($_POST['vehicle_info'] ?? '');
$order_notes = trim($_POST['order_notes'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');

// Get cart data
$cart_data = json_decode($_POST['cart_data'] ?? '[]', true);

// Validate required fields
$errors = [];
if (empty($first_name)) $errors[] = 'First name is required';
if (empty($last_name)) $errors[] = 'Last name is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if (empty($phone)) $errors[] = 'Phone number is required';
if (empty($address)) $errors[] = 'Delivery address is required';
if (empty($city)) $errors[] = 'City is required';
if (empty($region)) $errors[] = 'Region is required';
if (empty($payment_method)) $errors[] = 'Payment method is required';
if (empty($cart_data)) $errors[] = 'Cart is empty';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Calculate totals
$subtotal = 0;
$special_order_items = 0;
foreach ($cart_data as $item) {
    $subtotal += $item['subtotal'];
    if ($item['stock'] == 0) {
        $special_order_items++;
    }
}

// Calculate shipping based on region
$shipping_cost = 0;
switch ($region) {
    case 'kigali':
        $shipping_cost = 5000;
        break;
    case 'northern':
    case 'southern':
    case 'eastern':
    case 'western':
        $shipping_cost = 15000;
        break;
}

$total_amount = $subtotal + $shipping_cost;
$deposit_required = $special_order_items > 0 ? ceil($total_amount * 0.5) : 0;

// Generate order ID
$order_id = 'SPX-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

// Start transaction
$conn->begin_transaction();

try {
    // Insert order
    $customer_name = $first_name . ' ' . $last_name;
    $full_address = $address . ', ' . $city . ', ' . ucfirst($region) . ' Province';

    // Convert payment method to enum value
    $payment_enum = match($payment_method) {
        'bank_transfer' => 'bank',
        'mobile_money' => 'momo',
        'cash_delivery' => 'cash',
        default => 'cash'
    };

    // Use direct SQL INSERT to avoid prepared statement issues
    $order_sql = "INSERT INTO orders_enhanced (
        order_number, customer_id, order_type, order_status, payment_status, payment_method,
        subtotal, shipping_fee, total_amount, shipping_address, shipping_city,
        delivery_notes, special_instructions
    ) VALUES (
        '" . $conn->real_escape_string($order_id) . "',
        " . (int)$_SESSION['customer_id'] . ",
        'stock',
        'pending',
        'unpaid',
        '" . $conn->real_escape_string($payment_enum) . "',
        " . (float)$subtotal . ",
        " . (float)$shipping_cost . ",
        " . (float)$total_amount . ",
        '" . $conn->real_escape_string($full_address) . "',
        '" . $conn->real_escape_string($city) . "',
        '" . $conn->real_escape_string($order_notes) . "',
        '" . $conn->real_escape_string($vehicle_info) . "'
    )";

    if (!$conn->query($order_sql)) {
        throw new Exception("Order insert failed: " . $conn->error);
    }

    $order_db_id = $conn->insert_id;

    // Insert order items with vehicle year - USE ENHANCED order_items TABLE
    foreach ($cart_data as $item) {
        $vehicle_year = isset($item['selected_year']) ? (int)$item['selected_year'] : null;

        $item_sql = "INSERT INTO order_items_enhanced (
            order_id, product_id, product_name, product_brand, product_model,
            vehicle_year, product_image, unit_price, quantity, subtotal
        ) VALUES (
            " . (int)$order_db_id . ",
            " . (int)$item['id'] . ",
            '" . $conn->real_escape_string($item['name']) . "',
            '" . $conn->real_escape_string($item['brand']) . "',
            '" . $conn->real_escape_string($item['model']) . "',
            " . ($vehicle_year ? (int)$vehicle_year : 'NULL') . ",
            '" . $conn->real_escape_string($item['image']) . "',
            " . (float)$item['price'] . ",
            " . (int)$item['quantity'] . ",
            " . (float)$item['subtotal'] . "
        )";

        if (!$conn->query($item_sql)) {
            throw new Exception("Order item insert failed: " . $conn->error);
        }
    }

    // Update product stock (for in-stock items)
    $stock_sql = "UPDATE products_enhanced SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity > 0";
    $stmt = $conn->prepare($stock_sql);

    foreach ($cart_data as $item) {
        if ($item['stock'] > 0) {
            $stmt->bind_param('ii', $item['quantity'], $item['id']);
            $stmt->execute();
        }
    }
    $stmt->close();

    // Clear cart
    unset($_SESSION['cart']);

    // Commit transaction
    $conn->commit();

    // Generate and send invoice email (Production)
    try {
        // Generate PDF invoice
        require_once '../includes/invoice_generator.php';
        $pdfPath = generateOrderInvoice($order_db_id);

        // Send email with invoice
        require_once '../includes/email.php';
        $emailService = new EmailService();

        $customerName = $first_name . ' ' . $last_name;
        $emailSent = $emailService->sendOrderInvoice($email, $customerName, $order_id, $pdfPath);

        if ($emailSent) {
            // Log successful email in production
            error_log("[PRODUCTION] Invoice email sent to: $email for order: $order_id");
        } else {
            // Log failed email for monitoring
            error_log("[PRODUCTION] Failed to send invoice email to: $email for order: $order_id");
        }

        // Clean up PDF file after sending
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }

    } catch (Exception $e) {
        // Log production errors for monitoring
        error_log("[PRODUCTION] Email error for order $order_id: " . $e->getMessage());
        // Don't fail the order if email fails - customer can still access order
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order placed successfully! Invoice sent to your email.',
        'total_amount' => $total_amount,
        'deposit_required' => $deposit_required
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    echo json_encode([
        'success' => false,
        'message' => 'Failed to process order: ' . $e->getMessage()
    ]);
}

$conn->close();
?>