<?php
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Get order ID
$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header('Location: /admin/orders/enhanced_order_management.php');
    exit;
}

// Fetch order details
$order_query = "SELECT o.*, CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.phone as customer_phone, c.email as customer_email, c.address as customer_address
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
        $admin_id = 1; // TODO: Get from session
        $note_stmt->bind_param("issii", $order_id, $note_type, $note_content, $is_visible, $admin_id);

        if ($note_stmt->execute()) {
            $_SESSION['success'] = 'Note added successfully';
            header("Location: view_order.php?id=$order_id");
            exit;
        } else {
            $_SESSION['error'] = 'Failed to add note';
        }
    }
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-receipt-fill text-primary me-2"></i>
                Order Details
            </h2>
            <p class="text-muted mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="generate_invoice.php?id=<?php echo $order_id; ?>" class="btn btn-success" target="_blank">
                <i class="bi bi-eye me-1"></i>View Invoice
            </a>
            <a href="/admin/orders/enhanced_order_management.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Orders
            </a>
        </div>
    </div>

    <!-- Order Status Banner -->
    <div class="alert alert-<?php
        echo match($order['order_status']) {
            'pending' => 'warning',
            'processing' => 'info',
            'ready' => 'primary',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'failed' => 'danger',
            default => 'secondary'
        };
    ?> mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2 fs-5"></i>
            <div>
                <strong>Order Status:</strong> <?php echo ucfirst($order['order_status']); ?>
                <?php if ($order['tracking_number']): ?>
                    <br><small>Tracking: <?php echo htmlspecialchars($order['tracking_number']); ?> (<?php echo htmlspecialchars($order['shipping_carrier']); ?>)</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Customer Information -->
        <div class="col-lg-4">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-circle text-primary fs-4 me-3"></i>
                    <h5 class="mb-0">Customer Information</h5>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Name</label>
                    <p class="mb-2"><?php echo htmlspecialchars($order['customer_name'] ?: 'Walk-in Customer'); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Phone</label>
                    <p class="mb-2">
                        <?php if ($order['customer_phone']): ?>
                            <a href="tel:<?php echo htmlspecialchars($order['customer_phone']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($order['customer_phone']); ?>
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Email</label>
                    <p class="mb-2">
                        <?php if ($order['customer_email']): ?>
                            <a href="mailto:<?php echo htmlspecialchars($order['customer_email']); ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($order['customer_email']); ?>
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($order['shipping_address']): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Shipping Address</label>
                    <p class="mb-2">
                        <?php echo htmlspecialchars($order['shipping_address']); ?><br>
                        <?php echo htmlspecialchars($order['shipping_city']); ?>, <?php echo htmlspecialchars($order['shipping_sector']); ?>
                    </p>
                </div>
                <?php endif; ?>

                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar-event me-1"></i>
                        Ordered on <?php echo date('M d, Y \a\t H:i', strtotime($order['created_at'])); ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-box-seam text-success fs-4 me-3"></i>
                    <h5 class="mb-0">Order Items</h5>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Details</th>
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
                                    <?php if ($item['product_image']): ?>
                                        <img src="<?php echo htmlspecialchars($item['product_image']); ?>"
                                             alt="Product" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <small class="text-muted">
                                        <?php if ($item['product_brand']) echo htmlspecialchars($item['product_brand']) . ' • '; ?>
                                        <?php if ($item['product_model']) echo htmlspecialchars($item['product_model']) . ' • '; ?>
                                        <?php if ($item['product_category']) echo htmlspecialchars($item['product_category']); ?>
                                    </small>
                                </td>
                                <td class="text-center"><?php echo $item['quantity']; ?></td>
                                <td class="text-end">RWF <?php echo number_format($item['unit_price'], 0); ?></td>
                                <td class="text-end fw-semibold">RWF <?php echo number_format($item_total, 0); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="4" class="text-end fw-semibold">Subtotal:</td>
                                <td class="text-end fw-semibold">RWF <?php echo number_format($order['subtotal'], 0); ?></td>
                            </tr>
                            <?php if ($order['tax_amount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end">Tax:</td>
                                <td class="text-end">RWF <?php echo number_format($order['tax_amount'], 0); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($order['shipping_fee'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end">Shipping:</td>
                                <td class="text-end">RWF <?php echo number_format($order['shipping_fee'], 0); ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if ($order['discount_amount'] > 0): ?>
                            <tr>
                                <td colspan="4" class="text-end text-success">Discount:</td>
                                <td class="text-end text-success">-RWF <?php echo number_format($order['discount_amount'], 0); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="border-top">
                                <td colspan="4" class="text-end fw-bold fs-5">Total:</td>
                                <td class="text-end fw-bold fs-5 text-primary">RWF <?php echo number_format($order['total_amount'], 0); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment & Shipping Information -->
    <div class="row g-4 mt-2">
        <div class="col-lg-6">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-credit-card text-info fs-4 me-3"></i>
                    <h5 class="mb-0">Payment Information</h5>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Payment Status</label>
                        <span class="badge bg-<?php
                            echo match($order['payment_status']) {
                                'paid' => 'success',
                                'partial' => 'warning',
                                'unpaid' => 'danger',
                                'refunded' => 'secondary',
                                default => 'secondary'
                            };
                        ?> fs-6 px-3 py-2">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Payment Method</label>
                        <p class="mb-0"><?php echo ucfirst($order['payment_method']); ?></p>
                    </div>
                    <?php if ($order['transaction_id']): ?>
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-muted">Transaction ID</label>
                        <p class="mb-0 font-monospace"><?php echo htmlspecialchars($order['transaction_id']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Payment History -->
                <?php if ($payments->num_rows > 0): ?>
                <div class="mt-4">
                    <h6 class="mb-3">Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($payment = $payments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('M d, H:i', strtotime($payment['payment_date'] ?: $payment['created_at'])); ?></td>
                                    <td><?php echo ucfirst($payment['payment_method']); ?></td>
                                    <td>RWF <?php echo number_format($payment['amount'], 0); ?></td>
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

        <div class="col-lg-6">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-truck text-warning fs-4 me-3"></i>
                    <h5 class="mb-0">Shipping Information</h5>
                </div>

                <?php if ($order['shipping_carrier'] || $order['tracking_number']): ?>
                <div class="row g-3">
                    <?php if ($order['shipping_carrier']): ?>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Courier</label>
                        <p class="mb-0"><?php echo htmlspecialchars($order['shipping_carrier']); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($order['tracking_number']): ?>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold small text-muted">Tracking Number</label>
                        <p class="mb-0 font-monospace"><?php echo htmlspecialchars($order['tracking_number']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p class="text-muted mb-0">No shipping information available yet.</p>
                <?php endif; ?>

                <?php if ($order['delivery_notes']): ?>
                <div class="mt-3">
                    <label class="form-label fw-semibold small text-muted">Delivery Notes</label>
                    <p class="mb-0"><?php echo htmlspecialchars($order['delivery_notes']); ?></p>
                </div>
                <?php endif; ?>

                <!-- Order Tracking History -->
                <?php if ($tracking_history->num_rows > 0): ?>
                <div class="mt-4">
                    <h6 class="mb-3">Tracking History</h6>
                    <div class="timeline">
                        <?php while ($tracking = $tracking_history->fetch_assoc()): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?php
                                echo match($tracking['status']) {
                                    'pending' => 'warning',
                                    'processing' => 'info',
                                    'ready' => 'primary',
                                    'shipped' => 'info',
                                    'delivered' => 'success',
                                    'cancelled' => 'danger',
                                    'failed' => 'danger',
                                    default => 'secondary'
                                };
                            ?>"></div>
                            <div class="timeline-content">
                                <div class="fw-semibold"><?php echo ucfirst($tracking['status']); ?></div>
                                <div class="small text-muted"><?php echo date('M d, Y H:i', strtotime($tracking['created_at'])); ?></div>
                                <?php if ($tracking['status_description']): ?>
                                <div class="small"><?php echo htmlspecialchars($tracking['status_description']); ?></div>
                                <?php endif; ?>
                                <?php if ($tracking['tracking_number']): ?>
                                <div class="small text-info"><i class="bi bi-truck me-1"></i>Tracking: <?php echo htmlspecialchars($tracking['tracking_number']); ?> (<?php echo htmlspecialchars($tracking['carrier_name']); ?>)</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Order Notes -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="form-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-sticky text-secondary fs-4 me-3"></i>
                        <h5 class="mb-0">Order Notes</h5>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Note
                    </button>
                </div>

                <?php if ($order_notes->num_rows > 0): ?>
                <div class="notes-container">
                    <?php while ($note = $order_notes->fetch_assoc()): ?>
                    <div class="note-item border-start border-<?php
                        echo match($note['note_type']) {
                            'internal' => 'primary',
                            'customer' => 'success',
                            'packing' => 'warning',
                            'issue' => 'danger',
                            default => 'secondary'
                        };
                    ?> border-4 ps-3 py-2 mb-3">
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
                        <p class="mb-0"><?php echo htmlspecialchars($note['note_content']); ?></p>
                        <?php if ($note['is_visible_to_customer']): ?>
                        <small class="text-info"><i class="bi bi-eye me-1"></i>Visible to customer</small>
                        <?php endif; ?>
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

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Order Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Note Type</label>
                        <select class="form-select" name="note_type" required>
                            <option value="internal">Internal Note</option>
                            <option value="customer">Customer Communication</option>
                            <option value="packing">Packing Instructions</option>
                            <option value="issue">Issue Report</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note Content</label>
                        <textarea class="form-control" name="note_content" rows="4" placeholder="Enter your note..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="isVisible">
                            <label class="form-check-label" for="isVisible">
                                Make this note visible to the customer
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_note" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 6px;
    border-left: 3px solid #dee2f2;
}

.notes-container {
    max-height: 400px;
    overflow-y: auto;
}

.note-item {
    background: #f8f9fa;
    border-radius: 6px;
}
</style>

<?php include '../footer.php'; ?>