<?php
ob_start();
include '../includes/auth.php';
include '../includes/functions.php';
require_once __DIR__ . '/../../includes/logger.php';

// Get order ID
$order_id = (int)($_POST['order_id'] ?? $_GET['id'] ?? 0);
if (!$order_id) {
    header('Location: /admin/orders/enhanced_order_management.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    spx_log('view_order_post', [
        'order_id' => $order_id,
        'keys' => array_keys($_POST),
        'session_admin' => $_SESSION['admin'] ?? null,
        'admin_id' => $_SESSION['admin_id'] ?? null,
        'path' => $_SERVER['REQUEST_URI'] ?? null,
    ]);
}

// Handle status updates via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_edit'])) {
    $new_status = trim((string)($_POST['order_status'] ?? ''));
    $status_notes = trim((string)($_POST['status_notes'] ?? ''));
    $tracking_number = trim((string)($_POST['tracking_number'] ?? ''));
    $courier_name = trim((string)($_POST['courier_name'] ?? ''));
    $payment_status = trim((string)($_POST['payment_status'] ?? ''));

    $valid_statuses = ['pending', 'confirmed', 'processing', 'ready', 'packed', 'shipped', 'out_for_delivery', 'delivered', 'cancelled', 'refunded', 'failed'];
    $valid_payments = ['paid', 'unpaid', 'partial', 'refunded', 'failed'];

    if (!in_array($new_status, $valid_statuses, true)) {
        spx_log('order_edit_invalid_status', ['order_id' => $order_id, 'status' => $new_status]);
        $_SESSION['error'] = 'Invalid order status selected';
        header("Location: view_order.php?id=$order_id");
        exit;
    }

    if (!in_array($payment_status, $valid_payments, true)) {
        spx_log('order_edit_invalid_payment', ['order_id' => $order_id, 'payment_status' => $payment_status]);
        $_SESSION['error'] = 'Invalid payment status selected';
        header("Location: view_order.php?id=$order_id");
        exit;
    }

    try {
        $current_stmt = $conn->prepare("SELECT order_status, payment_status, shipping_carrier, tracking_number FROM orders_enhanced WHERE id = ?");
        $current_stmt->bind_param("i", $order_id);
        $current_stmt->execute();
        $current = $current_stmt->get_result()->fetch_assoc();

        if (!$current) {
            spx_log('order_edit_missing_order', ['order_id' => $order_id]);
            $_SESSION['error'] = 'Order not found';
            header("Location: /admin/orders/enhanced_order_management.php");
            exit;
        }

        $admin_id = (int)($_SESSION['admin_id'] ?? 1);

        $showTracking = in_array($new_status, ['shipped', 'out_for_delivery'], true);
        if (!$showTracking) {
            // Ignore any tracking fields posted when status doesn't require them.
            $tracking_number = '';
            $courier_name = '';
        }

        spx_log('order_edit_update_attempt', [
            'order_id' => $order_id,
            'admin_id' => $admin_id,
            'new_status' => $new_status,
            'payment_status' => $payment_status,
            'has_notes' => $status_notes !== '',
            'has_tracking' => ($tracking_number !== '' || $courier_name !== ''),
        ]);

        $conn->begin_transaction();

        $changed = [];

        if ($new_status !== (string)$current['order_status']) {
            $stmt = $conn->prepare("UPDATE orders_enhanced SET order_status = ?, status_updated_at = NOW(), status_updated_by = ? WHERE id = ?");
            $stmt->bind_param("sii", $new_status, $admin_id, $order_id);
            if (!$stmt->execute()) {
                throw new RuntimeException('Order status update failed: ' . ($stmt->error ?: 'Unknown error'));
            }
            $changed[] = 'status';
            spx_log('order_edit_status_update_ok', ['order_id' => $order_id, 'affected_rows' => $stmt->affected_rows]);
        }

        if ($payment_status !== (string)$current['payment_status']) {
            $stmt = $conn->prepare("UPDATE orders_enhanced SET payment_status = ? WHERE id = ?");
            $stmt->bind_param("si", $payment_status, $order_id);
            if (!$stmt->execute()) {
                throw new RuntimeException('Payment status update failed: ' . ($stmt->error ?: 'Unknown error'));
            }
            $changed[] = 'payment';
            spx_log('order_edit_payment_update_ok', ['order_id' => $order_id, 'affected_rows' => $stmt->affected_rows]);
        }

        if ($showTracking && ($tracking_number !== '' || $courier_name !== '')) {
            $stmt = $conn->prepare("UPDATE orders_enhanced SET shipping_carrier = ?, tracking_number = ? WHERE id = ?");
            $stmt->bind_param("ssi", $courier_name, $tracking_number, $order_id);
            if (!$stmt->execute()) {
                throw new RuntimeException('Shipping update failed: ' . ($stmt->error ?: 'Unknown error'));
            }
            $changed[] = 'shipping';
            spx_log('order_edit_shipping_update_ok', ['order_id' => $order_id, 'affected_rows' => $stmt->affected_rows]);
        }

        $shouldTimeline = !empty($changed) || $status_notes !== '' || ($showTracking && ($tracking_number !== '' || $courier_name !== ''));
        if ($shouldTimeline) {
            $timeline_stmt = $conn->prepare("INSERT INTO order_timeline (order_id, action, details, user_id, user_type) VALUES (?, ?, ?, ?, 'admin')");
            $description = $status_notes ?: (!empty($changed) ? "Order updated by admin" : "Order updated");
            $details = json_encode([
                'description' => $description,
                'tracking_number' => $tracking_number ?: null,
                'carrier_name' => $courier_name ?: null,
            ], JSON_UNESCAPED_UNICODE);
            if ($details === false) {
                $details = $description;
            }
            $timeline_stmt->bind_param("issi", $order_id, $new_status, $details, $admin_id);
            $ok = $timeline_stmt->execute();
            spx_log('order_edit_timeline_insert', [
                'order_id' => $order_id,
                'ok' => (bool)$ok,
                'error' => $timeline_stmt->error ?: null,
            ]);
        }

        $conn->commit();

        if (empty($changed) && $status_notes === '') {
            $_SESSION['success'] = 'No changes to save';
        } else {
            $labels = [];
            if (in_array('status', $changed, true)) $labels[] = 'status';
            if (in_array('payment', $changed, true)) $labels[] = 'payment';
            if (in_array('shipping', $changed, true)) $labels[] = 'shipping';
            $_SESSION['success'] = $labels ? ('Saved: ' . implode(', ', $labels)) : 'Order updated successfully';
        }

        header("Location: view_order.php?id=$order_id");
        exit;
    } catch (Throwable $e) {
        if (isset($conn) && $conn instanceof mysqli) {
            try { $conn->rollback(); } catch (Throwable $ignored) {}
        }
        spx_log('order_edit_update_exception', ['order_id' => $order_id, 'error' => $e->getMessage()]);
        $_SESSION['error'] = 'Failed to save changes: ' . $e->getMessage();
        header("Location: view_order.php?id=$order_id");
        exit;
    }
}

// Handle status updates via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $new_status = trim((string)($_POST['order_status'] ?? ''));
    $status_notes = trim($_POST['status_notes'] ?? '');
    $tracking_number = trim($_POST['tracking_number'] ?? '');
    $courier_name = trim($_POST['courier_name'] ?? '');

    $valid_statuses = ['pending', 'confirmed', 'processing', 'ready', 'packed', 'shipped', 'out_for_delivery', 'delivered', 'cancelled', 'refunded', 'failed'];
    if (!in_array($new_status, $valid_statuses, true)) {
        spx_log('order_status_invalid', ['order_id' => $order_id, 'status' => $new_status]);
        $_SESSION['error'] = 'Invalid order status selected';
        header("Location: view_order.php?id=$order_id");
        exit;
    }

    try {
        spx_log('order_status_update_attempt', [
            'order_id' => $order_id,
            'new_status' => $new_status,
            'tracking_number' => $tracking_number ?: null,
            'courier_name' => $courier_name ?: null,
        ]);

        $stmt = $conn->prepare("UPDATE orders_enhanced SET order_status = ?, status_updated_at = NOW(), status_updated_by = ? WHERE id = ?");
        $admin_id = (int)($_SESSION['admin_id'] ?? 1);
        $stmt->bind_param("sii", $new_status, $admin_id, $order_id);
        
        if ($stmt->execute()) {
            spx_log('order_status_update_ok', [
                'order_id' => $order_id,
                'affected_rows' => $stmt->affected_rows,
                'admin_id' => $admin_id,
            ]);

            // Add timeline entry (schema uses action/details/user_id/user_type)
            $timeline_stmt = $conn->prepare("INSERT INTO order_timeline (order_id, action, details, user_id, user_type) VALUES (?, ?, ?, ?, 'admin')");
            $description = $status_notes ?: "Order status changed to " . ucfirst($new_status);
            $details = json_encode([
                'description' => $description,
                'tracking_number' => $tracking_number ?: null,
                'carrier_name' => $courier_name ?: null
            ], JSON_UNESCAPED_UNICODE);
            if ($details === false) {
                $details = $description;
            }

            $timeline_stmt->bind_param("issi", $order_id, $new_status, $details, $admin_id);
            $timeline_ok = $timeline_stmt->execute();
            spx_log('order_timeline_insert', [
                'order_id' => $order_id,
                'ok' => (bool)$timeline_ok,
                'error' => $timeline_stmt->error ?: null,
            ]);
            
            // Update shipping info if shipped
            if ($new_status === 'shipped' && (!empty($tracking_number) || !empty($courier_name))) {
                $shipping_stmt = $conn->prepare("UPDATE orders_enhanced SET shipping_carrier = ?, tracking_number = ? WHERE id = ?");
                $shipping_stmt->bind_param("ssi", $courier_name, $tracking_number, $order_id);
                $ship_ok = $shipping_stmt->execute();
                spx_log('order_shipping_update', [
                    'order_id' => $order_id,
                    'ok' => (bool)$ship_ok,
                    'error' => $shipping_stmt->error ?: null,
                ]);
            }
            
            $_SESSION['success'] = 'Order status updated successfully';
            header("Location: view_order.php?id=$order_id");
            exit;
        }

        spx_log('order_status_update_failed', [
            'order_id' => $order_id,
            'error' => $stmt->error ?: null,
        ]);
        $_SESSION['error'] = 'Failed to update order status: ' . ($stmt->error ?: 'Unknown error');
        header("Location: view_order.php?id=$order_id");
        exit;
    } catch (Throwable $e) {
        spx_log('order_status_update_exception', [
            'order_id' => $order_id,
            'error' => $e->getMessage(),
        ]);
        $_SESSION['error'] = 'Failed to update order status: ' . $e->getMessage();
        header("Location: view_order.php?id=$order_id");
        exit;
    }
}

// Handle payment status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_status'])) {
    $payment_status = trim((string)($_POST['payment_status'] ?? ''));
    $valid_payments = ['paid', 'unpaid', 'partial', 'refunded', 'failed'];
    
    if (!in_array($payment_status, $valid_payments, true)) {
        spx_log('payment_status_invalid', ['order_id' => $order_id, 'payment_status' => $payment_status]);
        $_SESSION['error'] = 'Invalid payment status selected';
        header("Location: view_order.php?id=$order_id");
        exit;
    }

    try {
        spx_log('payment_status_update_attempt', ['order_id' => $order_id, 'payment_status' => $payment_status]);
        $stmt = $conn->prepare("UPDATE orders_enhanced SET payment_status = ? WHERE id = ?");
        $stmt->bind_param("si", $payment_status, $order_id);

        if ($stmt->execute()) {
            spx_log('payment_status_update_ok', [
                'order_id' => $order_id,
                'affected_rows' => $stmt->affected_rows,
            ]);
            $_SESSION['success'] = 'Payment status updated successfully';
            header("Location: view_order.php?id=$order_id");
            exit;
        }

        spx_log('payment_status_update_failed', [
            'order_id' => $order_id,
            'error' => $stmt->error ?: null,
        ]);
        $_SESSION['error'] = 'Failed to update payment status: ' . ($stmt->error ?: 'Unknown error');
        header("Location: view_order.php?id=$order_id");
        exit;
    } catch (Throwable $e) {
        spx_log('payment_status_update_exception', [
            'order_id' => $order_id,
            'error' => $e->getMessage(),
        ]);
        $_SESSION['error'] = 'Failed to update payment status: ' . $e->getMessage();
        header("Location: view_order.php?id=$order_id");
        exit;
    }
}

// Fetch order details
$order_query = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.phone as customer_phone, c.email as customer_email
                FROM orders_enhanced o
                LEFT JOIN customers_enhanced c ON o.customer_id = c.id
                WHERE o.id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = 'Order not found';
    header('Location: /admin/orders/enhanced_order_management.php');
    exit;
}

include '../header.php';

// Fetch order items
$items_query = "SELECT oi.*, p.product_name as original_name, p.main_image as product_image
                FROM order_items_enhanced oi
                LEFT JOIN products_enhanced p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id";
$items_stmt = $conn->prepare($items_query);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result();

// Fetch order tracking
$tracking_query = "SELECT * FROM order_timeline WHERE order_id = ? ORDER BY created_at DESC";
$tracking_stmt = $conn->prepare($tracking_query);
$tracking_stmt->bind_param("i", $order_id);
$tracking_stmt->execute();
$tracking_history = $tracking_stmt->get_result();

// Fetch order notes
$notes_query = "SELECT * FROM order_notes WHERE order_id = ? ORDER BY created_at DESC";
$notes_stmt = $conn->prepare($notes_query);
$notes_stmt->bind_param("i", $order_id);
$notes_stmt->execute();
$order_notes = $notes_stmt->get_result();

// Fetch payments
$payments_query = "SELECT * FROM payments WHERE order_id = ? ORDER BY payment_date DESC";
$payments_stmt = $conn->prepare($payments_query);
$payments_stmt->bind_param("i", $order_id);
$payments_stmt->execute();
$payments = $payments_stmt->get_result();

// Handle note addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $note_content = trim($_POST['note_content']);
    $note_type = $_POST['note_type'];
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;

    if (!empty($note_content)) {
        $note_stmt = $conn->prepare("INSERT INTO order_notes (order_id, note_type, note_content, is_visible_to_customer, created_by) VALUES (?, ?, ?, ?, ?)");
        $admin_id = 1;
        $note_stmt->bind_param("issii", $order_id, $note_type, $note_content, $is_visible, $admin_id);

        if ($note_stmt->execute()) {
            $_SESSION['success'] = 'Note added successfully';
            header("Location: view_order.php?id=$order_id");
            exit;
        }
    }
}

function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'ready' => 'info',
        'packed' => 'info',
        'shipped' => 'info',
        'out_for_delivery' => 'info',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'refunded' => 'secondary',
        'failed' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getPaymentColor($status) {
    $colors = [
        'paid' => 'success',
        'partial' => 'warning',
        'unpaid' => 'danger',
        'refunded' => 'secondary',
        'failed' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}
?>

<div class="admin-page">
    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="enhanced-header-card mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                        <i class="bi bi-receipt-cutoff fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h1 class="mb-1 fw-bold text-dark">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-<?php echo getStatusColor($order['order_status']); ?> fs-6 px-3 py-2">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                            <span class="badge bg-<?php echo getPaymentColor($order['payment_status']); ?> fs-6 px-3 py-2">
                                <i class="bi bi-credit-card me-1"></i>
                                <?php echo ucfirst($order['payment_status']); ?>
                            </span>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?php echo date('M d, Y \a\t H:i', strtotime($order['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#editOrderModal">
                        <i class="bi bi-pencil-square me-2"></i>Edit Order
                    </button>
                    <a href="generate_invoice.php?id=<?php echo $order_id; ?>" class="btn btn-warning btn-lg" target="_blank">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Invoice
                    </a>
                    <a href="/admin/orders/enhanced_order_management.php" class="btn btn-outline-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card bg-gradient-primary">
                <div class="stat-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">RWF <?php echo number_format($order['total_amount'], 0); ?></h3>
                    <p class="stat-label">Total Amount</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-gradient-info">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value"><?php echo $order_items->num_rows; ?></h3>
                    <p class="stat-label">Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-gradient-success">
                <div class="stat-icon">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value"><?php echo htmlspecialchars($order['customer_name'] ?: 'Walk-in'); ?></h3>
                    <p class="stat-label">Customer</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card bg-gradient-warning">
                <div class="stat-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">
                        <?php
                            $updatedAt = $order['status_updated_at'] ?: $order['updated_at'] ?: $order['created_at'];
                            echo date('M d, H:i', strtotime($updatedAt));
                        ?>
                    </h3>
                    <p class="stat-label">Last Update</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Customer Information -->
        <div class="col-lg-4">
            <div class="enhanced-card h-100">
                <div class="card-header-modern">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-4 text-primary me-2"></i>
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="customer-avatar-large mb-3 mx-auto">
                        <i class="bi bi-person-fill fs-1 text-primary"></i>
                    </div>
                    <h4 class="text-center mb-3"><?php echo htmlspecialchars($order['customer_name'] ?: 'Walk-in Customer'); ?></h4>
                    
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-icon bg-primary bg-opacity-10">
                                <i class="bi bi-telephone text-primary"></i>
                            </div>
                            <div class="info-content">
                                <label>Phone</label>
                                <?php if ($order['customer_phone']): ?>
                                    <a href="tel:<?php echo htmlspecialchars($order['customer_phone']); ?>">
                                        <?php echo htmlspecialchars($order['customer_phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-icon bg-success bg-opacity-10">
                                <i class="bi bi-envelope text-success"></i>
                            </div>
                            <div class="info-content">
                                <label>Email</label>
                                <?php if ($order['customer_email']): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>">
                                        <?php echo htmlspecialchars($order['customer_email']); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($order['shipping_address']): ?>
                        <div class="info-item">
                            <div class="info-icon bg-warning bg-opacity-10">
                                <i class="bi bi-geo-alt text-warning"></i>
                            </div>
                            <div class="info-content">
                                <label>Shipping Address</label>
                                <span><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                                <?php if ($order['shipping_city']): ?>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($order['shipping_city']); ?><?php echo $order['shipping_sector'] ? ', ' . htmlspecialchars($order['shipping_sector']) : ''; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="enhanced-card h-100">
                <div class="card-header-modern">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam fs-4 text-success me-2"></i>
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                </div>
                <div class="card-body-modern p-0">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal = 0;
                                while ($item = $order_items->fetch_assoc()):
                                    $item_total = $item['unit_price'] * $item['quantity'];
                                    $subtotal += $item_total;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($item['product_image']): ?>
                                                <img src="<?php echo htmlspecialchars($item['product_image']); ?>"
                                                     alt="Product" class="rounded-2 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded-2 d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image text-muted fs-4"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                <small class="text-muted">
                                                    <?php if ($item['product_brand']) echo htmlspecialchars($item['product_brand']) . ' • '; ?>
                                                    <?php if ($item['product_model']) echo htmlspecialchars($item['product_model']) . ' • '; ?>
                                                    <?php if ($item['product_category']) echo htmlspecialchars($item['product_category']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6"><?php echo $item['quantity']; ?></span>
                                    </td>
                                    <td class="text-end">RWF <?php echo number_format($item['unit_price'], 0); ?></td>
                                    <td class="text-end fw-bold">RWF <?php echo number_format($item_total, 0); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">Subtotal:</td>
                                    <td class="text-end fw-semibold">RWF <?php echo number_format($order['subtotal'], 0); ?></td>
                                </tr>
                                <?php if ($order['tax_amount'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end">Tax:</td>
                                    <td class="text-end">RWF <?php echo number_format($order['tax_amount'], 0); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($order['shipping_fee'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end">Shipping:</td>
                                    <td class="text-end">RWF <?php echo number_format($order['shipping_fee'], 0); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($order['discount_amount'] > 0): ?>
                                <tr>
                                    <td colspan="3" class="text-end text-success">Discount:</td>
                                    <td class="text-end text-success">-RWF <?php echo number_format($order['discount_amount'], 0); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                                    <td class="text-end fw-bold fs-5 text-primary">RWF <?php echo number_format($order['total_amount'], 0); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment & Shipping -->
    <div class="row g-4 mt-2">
        <div class="col-lg-6" id="payments">
            <div class="enhanced-card">
                <div class="card-header-modern">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-credit-card fs-4 text-info me-2"></i>
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="payment-stat">
                                <div class="payment-icon bg-<?php echo getPaymentColor($order['payment_status']); ?> bg-opacity-10">
                                    <i class="bi bi-<?php echo $order['payment_status'] === 'paid' ? 'check-circle' : 'clock'; ?> text-<?php echo getPaymentColor($order['payment_status']); ?> fs-3"></i>
                                </div>
                                <div>
                                    <label class="text-muted small">Payment Status</label>
                                    <h4 class="mb-0 text-<?php echo getPaymentColor($order['payment_status']); ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="payment-stat">
                                <div class="payment-icon bg-primary bg-opacity-10">
                                    <i class="bi bi-credit-card-frontend text-primary fs-3"></i>
                                </div>
                                <div>
                                    <label class="text-muted small">Payment Method</label>
                                    <h4 class="mb-0"><?php echo ucfirst($order['payment_method'] ?: 'N/A'); ?></h4>
                                </div>
                            </div>
                        </div>
                        <?php if ($order['transaction_id']): ?>
                        <div class="col-12">
                            <div class="payment-stat">
                                <div class="payment-icon bg-warning bg-opacity-10">
                                    <i class="bi bi-hash text-warning fs-3"></i>
                                </div>
                                <div>
                                    <label class="text-muted small">Transaction ID</label>
                                    <h6 class="mb-0 font-monospace"><?php echo htmlspecialchars($order['transaction_id']); ?></h6>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment History -->
                    <?php if ($payments->num_rows > 0): ?>
                    <div class="mt-4">
                        <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Payment History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-modern">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($payment = $payments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('M d, H:i', strtotime($payment['payment_date'] ?: $payment['created_at'])); ?></td>
                                        <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                        <td class="text-end">RWF <?php echo number_format($payment['amount'], 0); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $payment['payment_status'] === 'completed' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($payment['payment_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="enhanced-card">
                <div class="card-header-modern">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck fs-4 text-warning me-2"></i>
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                </div>
                <div class="card-body-modern">
                    <?php if ($order['shipping_carrier'] || $order['tracking_number']): ?>
                    <div class="row g-4">
                        <?php if ($order['shipping_carrier']): ?>
                        <div class="col-6">
                            <div class="shipping-stat">
                                <i class="bi bi-truck text-primary fs-2"></i>
                                <div>
                                    <label class="text-muted small">Courier</label>
                                    <h5 class="mb-0"><?php echo htmlspecialchars($order['shipping_carrier']); ?></h5>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($order['tracking_number']): ?>
                        <div class="col-6">
                            <div class="shipping-stat">
                                <i class="bi bi-upc-scan text-success fs-2"></i>
                                <div>
                                    <label class="text-muted small">Tracking Number</label>
                                    <h6 class="mb-0 font-monospace"><?php echo htmlspecialchars($order['tracking_number']); ?></h6>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-truck text-muted fs-1 mb-2"></i>
                        <p class="text-muted mb-0">No shipping information available yet.</p>
                    </div>
                    <?php endif; ?>

                    <?php if ($order['delivery_notes']): ?>
                    <div class="mt-4">
                        <label class="text-muted small"><i class="bi bi-chat-text me-1"></i>Delivery Notes</label>
                        <p class="mb-0 bg-light p-3 rounded-2"><?php echo htmlspecialchars($order['delivery_notes']); ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Order Timeline -->
                    <div class="mt-4" id="timeline">
                        <h6 class="mb-3"><i class="bi bi-clock-history me-2"></i>Order Timeline</h6>
                        <?php if ($tracking_history->num_rows > 0): ?>
                        <div class="enhanced-timeline">
                            <?php while ($tracking = $tracking_history->fetch_assoc()): ?>
                            <?php
                                $status = $tracking['action'] ?? 'pending';
                                $details = [];
                                if (!empty($tracking['details'])) {
                                    $decoded = json_decode($tracking['details'], true);
                                    if (is_array($decoded)) {
                                        $details = $decoded;
                                    } else {
                                        $details = ['description' => $tracking['details']];
                                    }
                                }
                            ?>
                            <div class="timeline-step">
                                <div class="timeline-marker bg-<?php echo getStatusColor($status); ?>">
                                    <i class="bi bi-<?php echo $status === 'delivered' ? 'check' : 'circle'; ?>"></i>
                                </div>
                                <div class="timeline-content-modern">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></h6>
                                        <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($tracking['created_at'])); ?></small>
                                    </div>
                                    <?php if (!empty($details['description'])): ?>
                                    <p class="mb-1 text-muted small"><?php echo htmlspecialchars($details['description']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($details['tracking_number'])): ?>
                                    <small class="text-info"><i class="bi bi-truck me-1"></i><?php echo htmlspecialchars($details['tracking_number']); ?> (<?php echo htmlspecialchars($details['carrier_name'] ?? 'N/A'); ?>)</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center py-3">No timeline history available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Notes -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="enhanced-card">
                <div class="card-header-modern">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-sticky fs-4 text-secondary me-2"></i>
                            <h5 class="mb-0">Order Notes</h5>
                        </div>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                            <i class="bi bi-plus-circle me-1"></i>Add Note
                        </button>
                    </div>
                </div>
                <div class="card-body-modern">
                    <?php if ($order_notes->num_rows > 0): ?>
                    <div class="row g-3">
                        <?php while ($note = $order_notes->fetch_assoc()): ?>
                        <div class="col-md-6">
                            <div class="note-card border-start border-4 border-<?php
                                echo match($note['note_type']) {
                                    'internal' => 'primary',
                                    'customer' => 'success',
                                    'packing' => 'warning',
                                    'issue' => 'danger',
                                    default => 'secondary'
                                };
                            ?>">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-<?php
                                        echo match($note['note_type']) {
                                            'internal' => 'primary',
                                            'customer' => 'success',
                                            'packing' => 'warning',
                                            'issue' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>"><?php echo ucfirst($note['note_type']); ?></span>
                                    <small class="text-muted"><?php echo date('M d, H:i', strtotime($note['created_at'])); ?></small>
                                </div>
                                <p class="mb-2"><?php echo htmlspecialchars($note['note_content']); ?></p>
                                <?php if ($note['is_visible_to_customer']): ?>
                                <small class="text-info"><i class="bi bi-eye me-1"></i>Visible to customer</small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-sticky text-muted fs-1 mb-2"></i>
                        <p class="text-muted mb-0">No notes added yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content order-edit-modal">
            <div class="modal-header modal-header-modern">
                <div class="w-100">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <h5 class="modal-title mb-1" id="editOrderModalLabel">
                                <i class="bi bi-pencil-square me-2"></i>Edit Order
                            </h5>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-light text-dark border">#<?php echo htmlspecialchars($order['order_number']); ?></span>
                                <span class="badge bg-<?php echo getStatusColor($order['order_status']); ?>">Status: <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?></span>
                                <span class="badge bg-<?php echo getPaymentColor($order['payment_status']); ?>">Payment: <?php echo ucfirst($order['payment_status']); ?></span>
                                <small class="text-white-50">
                                    <i class="bi bi-calendar3 me-1"></i><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="order-edit-nav mt-3 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-light" data-spx-scroll="#editStatusCard">
                            <i class="bi bi-truck me-1"></i>Status & Shipping
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-spx-scroll="#editPaymentCard">
                            <i class="bi bi-credit-card me-1"></i>Payment
                        </button>
                    </div>
                </div>
            </div>

            <form method="POST" action="view_order.php?id=<?php echo $order_id; ?>" id="editOrderForm">
                <input type="hidden" name="order_id" value="<?php echo (int)$order_id; ?>">
                <input type="hidden" name="update_order_edit" value="1">

                <div class="modal-body modal-body-modern">
                    <div class="alert alert-light border mb-3 py-2 px-3 small">
                        <i class="bi bi-info-circle me-1"></i>
                        One <strong>Save</strong> updates <strong>Status</strong> + <strong>Payment</strong> together.
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="edit-card h-100" id="editStatusCard">
                                <div class="edit-card-header">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-truck me-2"></i>Status & Shipping</h6>
                                    <span class="badge bg-<?php echo getStatusColor($order['order_status']); ?> bg-opacity-75">
                                        Current: <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                    </span>
                                </div>
                                <div class="edit-card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Order Status</label>
                                            <select class="form-select form-select-lg" name="order_status" id="orderStatusSelect" required>
                                                <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $order['order_status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="ready" <?php echo $order['order_status'] === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                                <option value="packed" <?php echo $order['order_status'] === 'packed' ? 'selected' : ''; ?>>Packed</option>
                                                <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="out_for_delivery" <?php echo $order['order_status'] === 'out_for_delivery' ? 'selected' : ''; ?>>Out for delivery</option>
                                                <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                <option value="refunded" <?php echo $order['order_status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                                <option value="failed" <?php echo $order['order_status'] === 'failed' ? 'selected' : ''; ?>>Failed</option>
                                            </select>
                                            <div class="form-text">Tip: when set to <strong>Shipped</strong> or <strong>Out for delivery</strong>, add courier + tracking.</div>
                                        </div>

                                        <div class="col-md-6" id="trackingFields" style="display: none;">
                                            <label class="form-label fw-semibold">Courier Name</label>
                                            <input type="text" class="form-control form-control-lg" name="courier_name" value="<?php echo htmlspecialchars($order['shipping_carrier'] ?? ''); ?>" placeholder="e.g., DHL, Rider">
                                        </div>
                                        <div class="col-md-6" id="trackingNumberField" style="display: none;">
                                            <label class="form-label fw-semibold">Tracking Number</label>
                                            <input type="text" class="form-control form-control-lg" name="tracking_number" value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>" placeholder="Enter tracking number">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Status Notes (Optional)</label>
                                            <textarea class="form-control" name="status_notes" rows="3" placeholder="Internal note about this update..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="edit-card h-100" id="editPaymentCard">
                                <div class="edit-card-header">
                                    <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Payment</h6>
                                    <span class="badge bg-<?php echo getPaymentColor($order['payment_status']); ?> bg-opacity-75">
                                        Current: <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </div>
                                <div class="edit-card-body">
                                    <label class="form-label fw-semibold">Payment Status</label>
                                    <div class="payment-grid">
                                        <input type="radio" class="btn-check" name="payment_status" id="payment_paid_modal" value="paid" <?php echo $order['payment_status'] === 'paid' ? 'checked' : ''; ?> required>
                                        <label class="payment-pill payment-pill-success" for="payment_paid_modal">
                                            <div><i class="bi bi-check-circle-fill me-2"></i><span>Paid</span></div>
                                            <small>Payment received</small>
                                        </label>

                                        <input type="radio" class="btn-check" name="payment_status" id="payment_unpaid_modal" value="unpaid" <?php echo $order['payment_status'] === 'unpaid' ? 'checked' : ''; ?>>
                                        <label class="payment-pill payment-pill-danger" for="payment_unpaid_modal">
                                            <div><i class="bi bi-x-circle-fill me-2"></i><span>Unpaid</span></div>
                                            <small>Not received</small>
                                        </label>

                                        <input type="radio" class="btn-check" name="payment_status" id="payment_partial_modal" value="partial" <?php echo $order['payment_status'] === 'partial' ? 'checked' : ''; ?>>
                                        <label class="payment-pill payment-pill-warning" for="payment_partial_modal">
                                            <div><i class="bi bi-exclamation-circle-fill me-2"></i><span>Partial</span></div>
                                            <small>Partly paid</small>
                                        </label>

                                        <input type="radio" class="btn-check" name="payment_status" id="payment_refunded_modal" value="refunded" <?php echo $order['payment_status'] === 'refunded' ? 'checked' : ''; ?>>
                                        <label class="payment-pill payment-pill-secondary" for="payment_refunded_modal">
                                            <div><i class="bi bi-arrow-left-circle-fill me-2"></i><span>Refunded</span></div>
                                            <small>Returned</small>
                                        </label>

                                        <input type="radio" class="btn-check" name="payment_status" id="payment_failed_modal" value="failed" <?php echo $order['payment_status'] === 'failed' ? 'checked' : ''; ?>>
                                        <label class="payment-pill payment-pill-dark" for="payment_failed_modal">
                                            <div><i class="bi bi-shield-exclamation me-2"></i><span>Failed</span></div>
                                            <small>Error</small>
                                        </label>
                                    </div>

                                    <div class="form-text mt-2">This updates the order’s <code>payment_status</code>. Add payment records under Payment History if you track transactions.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer modal-footer-modern">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-lg me-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header-modern">
                <h5 class="modal-title"><i class="bi bi-sticky me-2"></i>Add Order Note</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="view_order.php?id=<?php echo $order_id; ?>">
                <div class="modal-body-modern">
                    <div class="mb-3">
                        <label class="form-label">Note Type</label>
                        <select class="form-select" name="note_type" required>
                            <option value="internal">📝 Internal Note</option>
                            <option value="customer">💬 Customer Communication</option>
                            <option value="packing">📦 Packing Instructions</option>
                            <option value="issue">⚠️ Issue Report</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note Content</label>
                        <textarea class="form-control" name="note_content" rows="4" placeholder="Enter your note..." required></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_visible" id="isVisible">
                        <label class="form-check-label" for="isVisible">
                            Make this note visible to the customer
                        </label>
                    </div>
                </div>
                <div class="modal-footer-modern">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_note" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Enhanced Header Card */
.enhanced-header-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 25px;
    color: white;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
}

.header-icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Stat Cards */
.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-gradient-primary .stat-icon { background: rgba(102, 126, 234, 0.1); color: #667eea; }
.bg-gradient-info .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.bg-gradient-success .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.bg-gradient-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0;
}

/* Enhanced Card */
.enhanced-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.card-header-modern {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 18px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.card-header-modern h5 {
    font-weight: 600;
    color: #1e293b;
}

.card-body-modern {
    padding: 24px;
}

/* Customer Avatar */
.customer-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-content {
    flex: 1;
}

.info-content label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    display: block;
    margin-bottom: 2px;
}

.info-content a, .info-content span {
    font-weight: 500;
    color: #1e293b;
    text-decoration: none;
}

/* Table Modern */
.table-modern {
    margin: 0;
}

.table-modern thead th {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    padding: 14px 16px;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.table-modern tbody td {
    padding: 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.table-modern tfoot td {
    padding: 12px 16px;
}

.table-modern .total-row {
    background: #f8fafc;
}

/* Payment & Shipping Stats */
.payment-stat, .shipping-stat {
    display: flex;
    align-items: center;
    gap: 12px;
}

.payment-icon, .shipping-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Enhanced Timeline */
.enhanced-timeline {
    position: relative;
    padding-left: 30px;
}

.enhanced-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #667eea, #764ba2);
}

.timeline-step {
    position: relative;
    margin-bottom: 25px;
}

.timeline-step:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    box-shadow: 0 0 0 4px white;
}

.timeline-content-modern {
    background: #f8fafc;
    padding: 15px;
    border-radius: 12px;
    border-left: 3px solid #e2e8f0;
}

/* Note Card */
.note-card {
    background: #f8fafc;
    padding: 16px;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.note-card:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Edit Order Modal */
.order-edit-modal {
    border-radius: 18px;
    overflow: hidden;
}

.order-edit-modal #editOrderForm {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
}

.order-edit-modal #editOrderForm .modal-body {
    min-height: 0;
}

.edit-card {
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
}

.edit-card-header {
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(180deg, rgba(13,110,253,0.07) 0%, rgba(13,110,253,0.02) 100%);
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
}

.edit-card-body {
    padding: 16px;
}

.payment-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
}

@media (min-width: 768px) {
    .payment-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.spx-btn-fluid {
    width: 100%;
}

@media (min-width: 768px) {
    .spx-btn-fluid {
        width: auto;
    }
}

.order-edit-nav .btn {
    border-radius: 999px;
    padding: 8px 12px;
    border: 1px solid rgba(255,255,255,0.25);
}

.order-edit-nav .btn:hover {
    transform: translateY(-1px);
}

.order-edit-modal .modal-body-modern {
    padding: 18px;
}

@media (max-width: 576px) {
    .order-edit-modal .modal-body-modern {
        padding: 14px;
    }

    .order-edit-modal .modal-footer-modern {
        flex-direction: column-reverse;
        align-items: stretch;
    }

    .order-edit-modal .modal-footer-modern .btn {
        width: 100%;
    }

    .order-edit-nav .btn {
        flex: 1 1 auto;
    }
}

.payment-pill {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 12px 12px;
    border-radius: 12px;
    border: 1px solid rgba(15, 23, 42, 0.12);
    background: #fff;
    cursor: pointer;
    user-select: none;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}

.payment-pill div {
    display: flex;
    align-items: center;
    font-weight: 700;
    line-height: 1.1;
}

.payment-pill small {
    color: #64748b;
    font-size: 12px;
}

.btn-check:checked + .payment-pill {
    transform: translateY(-1px);
    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.14);
    border-color: rgba(13, 110, 253, 0.55);
}

.payment-pill-success { border-left: 5px solid #198754; }
.payment-pill-danger { border-left: 5px solid #dc3545; }
.payment-pill-warning { border-left: 5px solid #ffc107; }
.payment-pill-secondary { border-left: 5px solid #6c757d; }
.payment-pill-dark { border-left: 5px solid #212529; }

/* Modal Enhancements */
.modal-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 24px;
    border: none;
}

.modal-header-modern .modal-title {
    font-weight: 600;
}

.modal-body-modern {
    padding: 24px;
}

.modal-footer-modern {
    background: #f8fafc;
    padding: 16px 24px;
    border: none;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

/* Payment Options */
.payment-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.payment-option .btn {
    border-radius: 12px;
    font-weight: 500;
}

.payment-option input:checked + .btn {
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

/* Tracking Fields Animation */
#trackingFields, #trackingNumberField {
    transition: all 0.3s ease;
}

/* Button Styles */
.btn-lg {
    padding: 12px 24px;
    font-weight: 500;
    border-radius: 12px;
}

/* Responsive */
@media (max-width: 768px) {
    .enhanced-header-card .row {
        text-align: center;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .stat-icon {
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
    }
    
    .stat-value {
        font-size: 1rem;
    }
}
</style>

<script>
(() => {
    const statusSelect = document.getElementById('orderStatusSelect');
    if (!statusSelect) return;

    const toggleTracking = () => {
        const showTracking = ['shipped', 'out_for_delivery'].includes(statusSelect.value);
        const courierEl = document.getElementById('trackingFields');
        const trackingEl = document.getElementById('trackingNumberField');
        const courierInput = courierEl ? courierEl.querySelector('input,select,textarea') : null;
        const trackingInput = trackingEl ? trackingEl.querySelector('input,select,textarea') : null;

        if (courierEl) courierEl.style.display = showTracking ? 'block' : 'none';
        if (trackingEl) trackingEl.style.display = showTracking ? 'block' : 'none';

        if (courierInput) courierInput.disabled = !showTracking;
        if (trackingInput) trackingInput.disabled = !showTracking;

        if (!showTracking) {
            if (courierInput && 'value' in courierInput) courierInput.value = '';
            if (trackingInput && 'value' in trackingInput) trackingInput.value = '';
        }
    };

    statusSelect.addEventListener('change', toggleTracking);
    toggleTracking();
})();

(() => {
    const modal = document.getElementById('editOrderModal');
    if (!modal) return;

    modal.querySelectorAll('[data-spx-scroll]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetSelector = btn.getAttribute('data-spx-scroll');
            if (!targetSelector) return;

            const target = modal.querySelector(targetSelector);
            const body = modal.querySelector('.modal-body-modern');
            if (!target || !body) return;

            const targetTop = target.getBoundingClientRect().top - body.getBoundingClientRect().top + body.scrollTop;
            body.scrollTo({
                top: Math.max(0, targetTop - 8),
                behavior: 'smooth',
            });
        });
    });
})();
</script>

<?php include '../footer.php'; ?>
