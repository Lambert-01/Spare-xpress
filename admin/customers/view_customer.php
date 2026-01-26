<?php
// Customer Portal View - Admin Panel
ob_start();
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

$customer_id = $_GET['id'] ?? null;

if (!$customer_id) {
    header("Location: enhanced_customer_management.php");
    exit;
}

// Get customer details
$customer_query = $conn->prepare("
    SELECT c.*, COUNT(o.id) as total_orders, SUM(o.total_amount) as total_spent
    FROM customers_enhanced c
    LEFT JOIN orders_enhanced o ON c.id = o.customer_id AND o.payment_status = 'paid'
    WHERE c.id = ?
    GROUP BY c.id
");
$customer_query->bind_param("i", $customer_id);
$customer_query->execute();
$customer = $customer_query->get_result()->fetch_assoc();

if (!$customer) {
    header("Location: enhanced_customer_management.php");
    exit;
}

// Get recent orders
$orders_query = $conn->prepare("
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders_enhanced o
    LEFT JOIN order_items_enhanced oi ON o.id = oi.order_id
    WHERE o.customer_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$orders_query->bind_param("i", $customer_id);
$orders_query->execute();
$recent_orders = $orders_query->get_result();

// Get recent conversations
$conversations_query = $conn->prepare("
    SELECT conv.*, COUNT(m.id) as message_count, MAX(m.created_at) as last_message
    FROM conversations conv
    LEFT JOIN messages m ON conv.id = m.conversation_id
    WHERE conv.client_id = ?
    GROUP BY conv.id
    ORDER BY last_message DESC
    LIMIT 3
");
$conversations_query->bind_param("i", $customer_id);
$conversations_query->execute();
$recent_conversations = $conversations_query->get_result();
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-person-badge-fill text-primary me-3"></i>
                Customer Portal
            </h1>
            <p class="text-muted mb-0 fs-5">Read-only view of <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>'s account</p>
        </div>
        <div class="text-end">
            <a href="enhanced_customer_management.php" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i>Back to Customers
            </a>
            <button class="btn btn-primary" onclick="messageCustomer(<?php echo $customer_id; ?>)">
                <i class="bi bi-chat-dots me-1"></i>Send Message
            </button>
        </div>
    </div>

    <!-- Customer Overview -->
    <div class="row g-4 mb-5">
        <!-- Customer Info Card -->
        <div class="col-xl-4">
            <div class="form-card h-100">
                <div class="card-body text-center">
                    <div class="customer-avatar-large mb-3">
                        <i class="bi bi-person-circle fs-1 text-secondary"></i>
                    </div>
                    <h4 class="mb-1"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h4>
                    <p class="text-muted mb-3">Customer ID: <?php echo $customer['id']; ?></p>

                    <div class="row g-3 text-start">
                        <div class="col-12">
                            <strong>Email:</strong><br>
                            <a href="mailto:<?php echo $customer['email']; ?>"><?php echo htmlspecialchars($customer['email']); ?></a>
                        </div>
                        <div class="col-12">
                            <strong>Phone:</strong><br>
                            <a href="tel:<?php echo $customer['phone']; ?>"><?php echo htmlspecialchars($customer['phone']); ?></a>
                        </div>
                        <div class="col-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-<?php echo $customer['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($customer['status']); ?>
                            </span>
                        </div>
                        <div class="col-6">
                            <strong>Joined:</strong><br>
                            <small><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-xl-8">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="stats-card kpi-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                                <i class="bi bi-receipt-fill fs-1"></i>
                            </div>
                            <h3 class="card-value text-primary mb-2"><?php echo number_format($customer['total_orders']); ?></h3>
                            <p class="card-title mb-0">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card kpi-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                                <i class="bi bi-cash-stack fs-1"></i>
                            </div>
                            <h3 class="card-value text-success mb-2">RWF <?php echo number_format($customer['total_spent'] ?? 0, 0); ?></h3>
                            <p class="card-title mb-0">Total Spent</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card kpi-card h-100">
                        <div class="card-body text-center p-4">
                            <div class="card-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                                <i class="bi bi-chat-dots-fill fs-1"></i>
                            </div>
                            <h3 class="card-value text-info mb-2"><?php echo $recent_conversations->num_rows; ?></h3>
                            <p class="card-title mb-0">Conversations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row g-4 mb-5">
        <div class="col-xl-6">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-1">
                        <i class="bi bi-receipt text-primary me-2"></i>
                        Recent Orders
                    </h5>
                    <p class="text-muted small mb-0">Latest customer orders</p>
                </div>
                <div class="card-body">
                    <?php if ($recent_orders->num_rows > 0): ?>
                        <div class="order-timeline">
                            <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                <div class="timeline-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($order['order_number']); ?></span>
                                            <span class="badge bg-<?php
                                                echo match($order['order_status']) {
                                                    'pending' => 'warning',
                                                    'processing' => 'info',
                                                    'shipped' => 'primary',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?> badge-sm ms-2"><?php echo ucfirst($order['order_status']); ?></span>
                                        </div>
                                        <small class="text-muted"><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></small>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-box-seam me-1"></i><?php echo $order['item_count']; ?> items
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-cash me-1"></i>RWF <?php echo number_format($order['total_amount'], 0); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-receipt text-muted fs-1 mb-3"></i>
                            <p class="text-muted">No orders yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Conversations -->
        <div class="col-xl-6">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-1">
                        <i class="bi bi-chat-dots text-primary me-2"></i>
                        Recent Conversations
                    </h5>
                    <p class="text-muted small mb-0">Communication history</p>
                </div>
                <div class="card-body">
                    <?php if ($recent_conversations->num_rows > 0): ?>
                        <div class="conversation-list">
                            <?php while ($conv = $recent_conversations->fetch_assoc()): ?>
                                <div class="conversation-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div>
                                            <span class="fw-semibold">Conversation #<?php echo $conv['id']; ?></span>
                                            <span class="badge bg-info badge-sm ms-2"><?php echo $conv['message_count']; ?> messages</span>
                                        </div>
                                        <small class="text-muted"><?php echo $conv['last_message'] ? date('M d, H:i', strtotime($conv['last_message'])) : 'No messages'; ?></small>
                                    </div>
                                    <div class="text-muted small">
                                        Started: <?php echo date('M d, Y', strtotime($conv['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-chat-dots text-muted fs-1 mb-3"></i>
                            <p class="text-muted">No conversations yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.customer-avatar-large {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0,123,255,0.1);
    margin: 0 auto;
}

.order-timeline, .conversation-list {
    max-height: 300px;
    overflow-y: auto;
}

.timeline-item:last-child, .conversation-item:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
}
</style>

<script>
function messageCustomer(customerId) {
    window.location.href = `/admin/notifications/notification_manager.php?customer_id=${customerId}`;
}
</script>

<?php
ob_end_flush();
include '../footer.php';
?>