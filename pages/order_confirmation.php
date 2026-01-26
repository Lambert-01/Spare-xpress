<?php
// Session check must happen BEFORE any HTML output
include '../includes/client_session_check.php';

// Get order ID from URL
$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    header('Location: shop.php');
    exit();
}

$page_title = 'Order Confirmation - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

// Get order details from database
include '../includes/config.php';
$order_sql = "SELECT o.*, GROUP_CONCAT(oi.product_name SEPARATOR ', ') as products
              FROM orders_enhanced o
              LEFT JOIN order_items_enhanced oi ON o.id = oi.order_id
              WHERE o.order_number = ? AND o.customer_id = ?
              GROUP BY o.id";

$stmt = $conn->prepare($order_sql);
$stmt->bind_param('si', $order_id, $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: shop.php');
    exit();
}


$order = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!-- Order Confirmation Header -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); position: relative; overflow: hidden;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 text-white fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <i class="fas fa-check-circle me-3"></i>Order Confirmed!
                </h1>
                <p class="lead text-white-50 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    Thank you for your order. We've received your order and will process it shortly.
                </p>
                <div class="d-flex gap-3 flex-wrap wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-clock fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Processing Time</h6>
                            <small class="text-white-50">24-48 hours</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-truck fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Delivery</h6>
                            <small class="text-white-50">2-5 business days</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-headset fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Support</h6>
                            <small class="text-white-50">24/7 available</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center wow fadeInUp" data-wow-delay="0.7s">
                <div class="position-relative">
                    <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 120px; height: 120px;">
                        <i class="fas fa-check-circle fa-4x text-white"></i>
                    </div>
                    <h3 class="text-white mb-0">Order #<?php echo htmlspecialchars($order_id); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- Order Summary -->
            <div class="col-lg-8">
                <div class="order-confirmation-card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-receipt me-2 text-primary"></i>Order Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- Order Info -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Order Number</label>
                                    <div class="info-value fw-bold text-primary"><?php echo htmlspecialchars($order_id); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Order Date</label>
                                    <div class="info-value"><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Payment Method</label>
                                    <div class="info-value">
                                        <?php
                                        $payment_methods = [
                                            'cash' => 'Cash on Delivery',
                                            'momo' => 'Mobile Money',
                                            'bank' => 'Bank Transfer',
                                            'card' => 'Credit/Debit Card'
                                        ];
                                        echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Order Status</label>
                                    <div class="info-value">
                                        <span class="badge bg-warning">Processing</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <h5 class="mb-3">Order Items</h5>
                        <div class="order-items">
                            <?php
                            // Get order items
                            include '../includes/config.php';
                            $items_sql = "SELECT * FROM order_items_enhanced WHERE order_id = ?";
                            $stmt = $conn->prepare($items_sql);
                            $stmt->bind_param('i', $order['id']);
                            $stmt->execute();
                            $items_result = $stmt->get_result();

                            while ($item = $items_result->fetch_assoc()) {
                                ?>
                                <div class="order-item d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <img src="<?php echo htmlspecialchars($item['product_image'] ?: '/img/no-image.png'); ?>"
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                         class="rounded me-3" style="width: 60px; height: 60px; object-fit: contain;">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($item['product_brand'] ?? ''); ?>
                                            <?php echo htmlspecialchars($item['product_model'] ?? ''); ?>
                                        </small>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                            <span class="fw-bold">RWF <?php echo number_format($item['subtotal'], 0, '.', ','); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            $stmt->close();
                            $conn->close();
                            ?>
                        </div>

                        <!-- Order Totals -->
                        <div class="order-totals mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>RWF <?php echo number_format($order['subtotal'], 0, '.', ','); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span>RWF <?php echo number_format($order['shipping_fee'], 0, '.', ','); ?></span>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span class="text-primary">RWF <?php echo number_format($order['total_amount'], 0, '.', ','); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Next Steps & Actions -->
            <div class="col-lg-4">
                <!-- Payment Instructions -->
                <?php if ($order['payment_method'] === 'bank'): ?>
                <div class="order-confirmation-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-university me-2"></i>Payment Instructions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>Bank Transfer Details:</h6>
                            <p class="mb-1"><strong>Bank:</strong> Bank of Kigali</p>
                            <p class="mb-1"><strong>Account Name:</strong> SPARE XPRESS LTD</p>
                            <p class="mb-1"><strong>Account Number:</strong> 00000-000000-00</p>
                            <p class="mb-1"><strong>Reference:</strong> <?php echo htmlspecialchars($order_id); ?></p>
                        </div>
                        <p class="small text-muted">Please include the order number as reference when making payment.</p>
                    </div>
                </div>
                <?php elseif ($order['payment_method'] === 'momo'): ?>
                <div class="order-confirmation-card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-mobile-alt me-2"></i>Mobile Money Payment
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h6>Mobile Money Details:</h6>
                            <p class="mb-1"><strong>Number:</strong> +250 792 865 114</p>
                            <p class="mb-1"><strong>Name:</strong> SPARE XPRESS LTD</p>
                            <p class="mb-1"><strong>Reference:</strong> <?php echo htmlspecialchars($order_id); ?></p>
                        </div>
                        <p class="small text-muted">Send payment to the number above with order number as reference.</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Delivery Information -->
                <div class="order-confirmation-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>Delivery Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="delivery-address">
                            <h6>Delivery Address:</h6>
                            <address class="mb-0">
                                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?><br>
                                <?php echo htmlspecialchars($order['shipping_city']); ?>, Rwanda
                            </address>
                        </div>
                        <?php if (!empty($order['delivery_notes'])): ?>
                        <div class="mt-3">
                            <h6>Delivery Notes:</h6>
                            <p class="small text-muted"><?php echo htmlspecialchars($order['delivery_notes']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="order-confirmation-card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/pages/shop.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                            </a>
                            <a href="/pages/cart.php" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-cart me-2"></i>View Cart
                            </a>
                            <a href="tel:+250790305394" class="btn btn-outline-success">
                                <i class="fas fa-phone me-2"></i>Call for Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-confirmation-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    overflow: hidden;
}

.order-confirmation-card .card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 1.5rem;
}

.order-confirmation-card .card-body {
    padding: 1.5rem;
}

.info-item {
    margin-bottom: 1rem;
}

.info-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
    color: #2d3748;
}

.order-items {
    max-height: 400px;
    overflow-y: auto;
}

.order-item {
    transition: all 0.3s ease;
}

.order-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    margin-left: -0.5rem;
    margin-right: -0.5rem;
}

.order-totals {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
}

.delivery-address address {
    font-style: normal;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .order-confirmation-card {
        margin-bottom: 1.5rem;
    }

    .order-confirmation-card .card-body {
        padding: 1rem;
    }
}
</style>

<?php include '../includes/footer.php'; ?>