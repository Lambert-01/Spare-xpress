<?php
// Process checkout and create order
// SPARE XPRESS LTD - Order Processing

require_once __DIR__ . '/../includes/session_init.php';
spx_session_start();
include '../includes/config.php';

// Always respond with JSON (avoid HTML warnings breaking fetch().json())
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to continue.']);
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
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Calculate shipping based on region (server-controlled)
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

// Generate order ID
$order_id = 'SPX-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

// Start transaction
$conn->begin_transaction();

try {
    // SECURITY: never trust prices, names or stock from the browser. Re-derive
    // every price-sensitive field from the database and validate stock here.
    $validated_items = [];
    $subtotal = 0;

    $lookup = $conn->prepare(
        "SELECT p.id, p.product_name, p.main_image,
                COALESCE(p.regular_price, p.price, 0) AS unit_price,
                p.stock_quantity, COALESCE(p.price_request_only, 0) AS price_request_only,
                b.brand_name, m.model_name
         FROM products_enhanced p
         LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
         LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
         WHERE p.id = ?"
    );

    foreach ($cart_data as $item) {
        $product_id = (int)($item['id'] ?? 0);
        $quantity   = (int)($item['quantity'] ?? 0);

        if ($product_id <= 0 || $quantity <= 0) {
            throw new Exception('Invalid cart item.');
        }

        $lookup->bind_param('i', $product_id);
        $lookup->execute();
        $product_row = $lookup->get_result()->fetch_assoc();

        if (!$product_row || !empty($product_row['price_request_only'])) {
            throw new Exception('One or more cart items require a price request and cannot be checked out directly.');
        }

        $available = (int)$product_row['stock_quantity'];
        if ($available <= 0) {
            throw new Exception('"' . $product_row['product_name'] . '" is out of stock.');
        }
        if ($quantity > $available) {
            throw new Exception('Only ' . $available . ' of "' . $product_row['product_name'] . '" are in stock.');
        }

        $unit_price   = (float)$product_row['unit_price'];
        $line_total   = $unit_price * $quantity;
        $subtotal    += $line_total;

        $validated_items[] = [
            'id'           => $product_id,
            'name'         => $product_row['product_name'],
            'brand'        => $product_row['brand_name'] ?? '',
            'model'        => $product_row['model_name'] ?? '',
            'image'        => $product_row['main_image'] ?? '',
            'vehicle_year' => isset($item['selected_year']) ? (int)$item['selected_year'] : null,
            'unit_price'   => $unit_price,
            'quantity'     => $quantity,
            'subtotal'     => $line_total,
        ];
    }
    $lookup->close();

    if (empty($validated_items)) {
        throw new Exception('Cart is empty.');
    }

    $total_amount = $subtotal + $shipping_cost;

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

    $customer_id = (int)$_SESSION['customer_id'];
    $order_stmt = $conn->prepare(
        "INSERT INTO orders_enhanced (
            order_number, customer_id, order_type, order_status, payment_status, payment_method,
            subtotal, shipping_fee, total_amount, shipping_address, shipping_city,
            delivery_notes, special_instructions
        ) VALUES (?, ?, 'stock', 'pending', 'unpaid', ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $order_stmt->bind_param(
        'sisdddssss',
        $order_id, $customer_id, $payment_enum,
        $subtotal, $shipping_cost, $total_amount,
        $full_address, $city, $order_notes, $vehicle_info
    );
    if (!$order_stmt->execute()) {
        throw new Exception("Order insert failed: " . $conn->error);
    }
    $order_db_id = $conn->insert_id;
    $order_stmt->close();

    // Insert order items from the validated (server-side) values
    $item_stmt = $conn->prepare(
        "INSERT INTO order_items_enhanced (
            order_id, product_id, product_name, product_brand, product_model,
            vehicle_year, product_image, unit_price, quantity, subtotal
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    foreach ($validated_items as $item) {
        $item_stmt->bind_param(
            'iisssissdd',
            $order_db_id, $item['id'], $item['name'], $item['brand'], $item['model'],
            $item['vehicle_year'], $item['image'], $item['unit_price'], $item['quantity'], $item['subtotal']
        );
        if (!$item_stmt->execute()) {
            throw new Exception("Order item insert failed: " . $conn->error);
        }
    }
    $item_stmt->close();

    // Decrement stock from validated quantities. The WHERE guard ensures the
    // row still has enough stock; if a concurrent order drained it, affected_rows
    // is 0 and we abort rather than oversell.
    $stock_sql = "UPDATE products_enhanced SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
    $stmt = $conn->prepare($stock_sql);

    foreach ($validated_items as $item) {
        $stmt->bind_param('iii', $item['quantity'], $item['id'], $item['quantity']);
        $stmt->execute();
        if ($stmt->affected_rows !== 1) {
            throw new Exception('"' . $item['name'] . '" just went out of stock. Please review your cart.');
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
        $emailSent = $emailService->sendOrderInvoice($email, $customerName, $order_id, $pdfPath, [
            'phone' => $phone,
            'payment_method' => ucwords(str_replace('_', ' ', $payment_method)),
            'subtotal' => $subtotal,
            'shipping_fee' => $shipping_cost,
            'total_amount' => $total_amount,
            'items' => $cart_data,
        ]);

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

    } catch (Throwable $e) {
        // Log production errors for monitoring
        error_log("[PRODUCTION] Email error for order $order_id: " . $e->getMessage());
        // Don't fail the order if email fails - customer can still access order
    }

    // Send success response
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order placed successfully! Invoice sent to your email.',
        'total_amount' => $total_amount
    ]);

} catch (Throwable $e) {
    // Rollback transaction on error
    $conn->rollback();

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process order: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
