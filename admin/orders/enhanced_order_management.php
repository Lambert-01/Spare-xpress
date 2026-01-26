  <?php
  // Enhanced Order Management System for SPARE XPRESS LTD
  ob_start();
  include '../includes/auth.php';
  include '../includes/functions.php';
  include '../header.php';

// Handle status updates via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['order_status'];
    $status_notes = trim($_POST['status_notes'] ?? '');
    $tracking_number = trim($_POST['tracking_number'] ?? '');
    $courier_name = trim($_POST['courier_name'] ?? '');

    // Validate status
    $valid_statuses = ['pending', 'confirmed', 'processing', 'ready', 'packed', 'shipped', 'out_for_delivery', 'delivered', 'cancelled', 'refunded', 'failed'];
    if (!in_array($new_status, $valid_statuses)) {
        $_SESSION['error'] = 'Invalid order status';
        header('Location: enhanced_order_management.php');
        exit;
    }

    // Update order status
    $stmt = $conn->prepare("UPDATE orders_enhanced SET
        order_status = ?, status_updated_at = NOW(), status_updated_by = ?
        WHERE id = ?");

    $admin_id = 1; // TODO: Get from session
    $stmt->bind_param("sii", $new_status, $admin_id, $order_id);

    if ($stmt->execute()) {
        // Add timeline entry
        $timeline_stmt = $conn->prepare("INSERT INTO order_timeline
            (order_id, status, status_description, tracking_number, carrier_name, created_by)
            VALUES (?, ?, ?, ?, ?, ?)");

        $description = $status_notes ?: "Order status changed to " . ucfirst($new_status);
        $timeline_stmt->bind_param("issssi", $order_id, $new_status, $description, $tracking_number, $courier_name, $admin_id);
        $timeline_stmt->execute();

        // Update shipping info if shipped
        if ($new_status === 'shipped' && (!empty($tracking_number) || !empty($courier_name))) {
            $shipping_stmt = $conn->prepare("UPDATE orders_enhanced SET
                shipping_carrier = ?, tracking_number = ?, shipping_method = 'standard'
                WHERE id = ?");
            $shipping_stmt->bind_param("ssi", $courier_name, $tracking_number, $order_id);
            $shipping_stmt->execute();
        }

        $_SESSION['success'] = 'Order status updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update order status';
    }

    header('Location: enhanced_order_management.php');
    exit;
}

// Handle priority updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_priority'])) {
    $order_id = (int)$_POST['order_id'];
    $priority_level = $_POST['priority_level'];

    $valid_priorities = ['low', 'normal', 'high', 'urgent'];
    if (!in_array($priority_level, $valid_priorities)) {
        $_SESSION['error'] = 'Invalid priority level';
        header('Location: enhanced_order_management.php');
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders_enhanced SET priority_level = ? WHERE id = ?");
    $stmt->bind_param("si", $priority_level, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Order priority updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update order priority';
    }

    header('Location: enhanced_order_management.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $order_id = (int)$_GET['delete'];

    // Check if order can be deleted (only pending orders)
    $check_stmt = $conn->prepare("SELECT order_status FROM orders_enhanced WHERE id = ?");
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order && in_array($order['order_status'], ['pending', 'cancelled'])) {
        $stmt = $conn->prepare("DELETE FROM orders_enhanced WHERE id = ?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Order deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete order';
        }
    } else {
        $_SESSION['error'] = 'Cannot delete order that is being processed or delivered';
    }

    header('Location: enhanced_order_management.php');
    exit;
}

// Get orders with enhanced filtering
$where_conditions = [];
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';
$priority_filter = $_GET['priority'] ?? 'all';
$type_filter = $_GET['type'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

if (!empty($search)) {
    $where_conditions[] = "(o.order_number LIKE '%" . $conn->real_escape_string($search) . "%' OR
                          c.first_name LIKE '%" . $conn->real_escape_string($search) . "%' OR
                          c.last_name LIKE '%" . $conn->real_escape_string($search) . "%' OR
                          c.phone LIKE '%" . $conn->real_escape_string($search) . "%')";
}

if ($status_filter !== 'all') {
    $where_conditions[] = "o.order_status = '" . $conn->real_escape_string($status_filter) . "'";
}

if ($priority_filter !== 'all') {
    $where_conditions[] = "o.priority_level = '" . $conn->real_escape_string($priority_filter) . "'";
}

if ($type_filter !== 'all') {
    $where_conditions[] = "o.order_type = '" . $conn->real_escape_string($type_filter) . "'";
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(o.created_at) >= '" . $conn->real_escape_string($date_from) . "'";
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(o.created_at) <= '" . $conn->real_escape_string($date_to) . "'";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT o.*,
                 CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                 c.phone as customer_phone,
                 c.email as customer_email,
                 (SELECT COUNT(*) FROM order_items_enhanced WHERE order_id = o.id) as item_count,
                 (SELECT GROUP_CONCAT(DISTINCT oi.product_name SEPARATOR ', ')
                  FROM order_items_enhanced oi WHERE oi.order_id = o.id LIMIT 3) as product_names
          FROM orders_enhanced o
          LEFT JOIN customers_enhanced c ON o.customer_id = c.id
          $where_clause
          ORDER BY
              CASE o.priority_level
                  WHEN 'urgent' THEN 1
                  WHEN 'high' THEN 2
                  WHEN 'normal' THEN 3
                  WHEN 'low' THEN 4
              END,
              o.created_at DESC";

$result = $conn->query($query);

// Get statistics
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced")->fetch_assoc()['count'],
    'pending' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status = 'pending'")->fetch_assoc()['count'],
    'processing' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status IN ('confirmed', 'processing')")->fetch_assoc()['count'],
    'shipped' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status IN ('shipped', 'out_for_delivery')")->fetch_assoc()['count'],
    'delivered' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status = 'delivered'")->fetch_assoc()['count'],
    'cancelled' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status IN ('cancelled', 'failed')")->fetch_assoc()['count'],
    'urgent' => $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE priority_level = 'urgent' AND order_status NOT IN ('delivered', 'cancelled')")->fetch_assoc()['count'],
    'revenue' => $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders_enhanced WHERE payment_status = 'paid' AND DATE(created_at) = CURDATE()")->fetch_assoc()['total']
];

function getStatusBadge($status, $priority = 'normal') {
    $base_classes = "badge ";

    $status_classes = [
        'pending' => 'bg-warning text-dark',
        'confirmed' => 'bg-info',
        'processing' => 'bg-primary',
        'ready' => 'bg-info',
        'packed' => 'bg-info',
        'shipped' => 'bg-info',
        'out_for_delivery' => 'bg-info',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger',
        'refunded' => 'bg-secondary',
        'failed' => 'bg-danger'
    ];

    $class = $status_classes[$status] ?? 'bg-secondary';

    if ($priority === 'urgent') {
        $class .= ' border border-danger';
    }

    return $base_classes . $class;
}

function getPriorityBadge($priority) {
    $badges = [
        'low' => '<span class="badge bg-secondary">Low</span>',
        'normal' => '<span class="badge bg-info">Normal</span>',
        'high' => '<span class="badge bg-warning text-dark">High</span>',
        'urgent' => '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Urgent</span>'
    ];
    return $badges[$priority] ?? '<span class="badge bg-secondary">Normal</span>';
}

function getPaymentBadge($status) {
    $badges = [
        'unpaid' => '<span class="badge bg-danger">Unpaid</span>',
        'partial' => '<span class="badge bg-warning text-dark">Partial</span>',
        'paid' => '<span class="badge bg-success">Paid</span>',
        'refunded' => '<span class="badge bg-secondary">Refunded</span>',
        'failed' => '<span class="badge bg-danger">Failed</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-receipt-fill text-primary me-3"></i>
                Enhanced Order Management
            </h1>
            <p class="text-muted mb-0 fs-5">Professional order processing with timeline tracking and priority management</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" onclick="exportOrders()">
                <i class="bi bi-download me-1"></i>Export Orders
            </button>
            <button class="btn btn-info" onclick="viewAnalytics()">
                <i class="bi bi-graph-up me-1"></i>Analytics
            </button>
        </div>
    </div>

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

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center p-3">
                    <div class="card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <h4 class="card-value mb-1"><?php echo number_format($stats['total']); ?></h4>
                    <p class="card-title small mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center p-3">
                    <div class="card-icon bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                        <i class="bi bi-clock fs-4"></i>
                    </div>
                    <h4 class="card-value mb-1"><?php echo number_format($stats['pending']); ?></h4>
                    <p class="card-title small mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center p-3">
                    <div class="card-icon bg-info bg-opacity-10 text-info mx-auto mb-2">
                        <i class="bi bi-gear fs-4"></i>
                    </div>
                    <h4 class="card-value mb-1"><?php echo number_format($stats['processing']); ?></h4>
                    <p class="card-title small mb-0">Processing</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center p-3">
                    <div class="card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                    <h4 class="card-value mb-1"><?php echo number_format($stats['shipped']); ?></h4>
                    <p class="card-title small mb-0">Shipped</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center p-3">
                    <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <h4 class="card-value mb-1"><?php echo number_format($stats['delivered']); ?></h4>
                    <p class="card-title small mb-0">Delivered</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-6">
            <div class="stats-card">
                <div class="card-body text-center p-3">
                    <div class="card-icon bg-danger bg-opacity-10 text-danger mx-auto mb-2">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>
                    <h4 class="card-value mb-1"><?php echo number_format($stats['urgent']); ?></h4>
                    <p class="card-title small mb-0">Urgent</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="form-card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Search Orders</label>
                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Order ID, Customer name, Phone...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Priority</label>
                <select class="form-select" name="priority">
                    <option value="all" <?php echo $priority_filter === 'all' ? 'selected' : ''; ?>>All Priority</option>
                    <option value="urgent" <?php echo $priority_filter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                    <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="normal" <?php echo $priority_filter === 'normal' ? 'selected' : ''; ?>>Normal</option>
                    <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Type</label>
                <select class="form-select" name="type">
                    <option value="all" <?php echo $type_filter === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="stock" <?php echo $type_filter === 'stock' ? 'selected' : ''; ?>>Stock Orders</option>
                    <option value="on_demand" <?php echo $type_filter === 'on_demand' ? 'selected' : ''; ?>>On-Demand</option>
                    <option value="emergency" <?php echo $type_filter === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="enhanced_order_management.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="form-card">
        <div class="table-responsive">
            <table class="table table-hover" id="ordersTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="order_number">
                            <i class="bi bi-hash"></i>
                            <span>Order ID</span>
                        </th>
                        <th class="sortable" data-column="customer_name">
                            <i class="bi bi-person"></i>
                            <span>Customer</span>
                        </th>
                        <th>
                            <i class="bi bi-tag"></i>
                            <span>Type</span>
                        </th>
                        <th>
                            <i class="bi bi-box-seam"></i>
                            <span>Items</span>
                        </th>
                        <th>
                            <i class="bi bi-credit-card"></i>
                            <span>Payment</span>
                        </th>
                        <th class="sortable" data-column="order_status">
                            <i class="bi bi-truck"></i>
                            <span>Status</span>
                        </th>
                        <th class="sortable" data-column="priority_level">
                            <i class="bi bi-star"></i>
                            <span>Priority</span>
                        </th>
                        <th class="sortable" data-column="total_amount">
                            <i class="bi bi-cash"></i>
                            <span>Total</span>
                        </th>
                        <th class="sortable" data-column="created_at">
                            <i class="bi bi-calendar"></i>
                            <span>Date</span>
                        </th>
                        <th>
                            <i class="bi bi-gear"></i>
                            <span>Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr class="<?php echo $order['priority_level'] === 'urgent' ? 'table-warning' : ''; ?>">
                            <td data-label="Order ID">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2"><?php echo htmlspecialchars($order['order_number']); ?></span>
                                    <?php if ($order['priority_level'] === 'urgent'): ?>
                                        <i class="bi bi-exclamation-triangle-fill text-danger" title="Urgent Order"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Customer">
                                <div class="fw-semibold text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($order['customer_name'] ?: 'Walk-in Customer'); ?>">
                                    <?php echo htmlspecialchars($order['customer_name'] ?: 'Walk-in Customer'); ?>
                                </div>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_phone'] ?: 'N/A'); ?></small>
                            </td>
                            <td data-label="Type">
                                <?php
                                $type_badges = [
                                    'stock' => '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Stock</span>',
                                    'on_demand' => '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>On-Demand</span>',
                                    'emergency' => '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Emergency</span>',
                                    'bulk' => '<span class="badge bg-info"><i class="bi bi-stack me-1"></i>Bulk</span>'
                                ];
                                echo $type_badges[$order['order_type']] ?? '<span class="badge bg-secondary">Unknown</span>';
                                ?>
                            </td>
                            <td data-label="Items">
                                <div class="d-flex flex-column">
                                    <span class="badge bg-info mb-1">
                                        <i class="bi bi-box-seam me-1"></i><?php echo $order['item_count']; ?> item(s)
                                    </span>
                                    <small class="text-muted text-truncate" style="max-width: 120px;" title="<?php echo htmlspecialchars($order['product_names']); ?>">
                                        <?php echo htmlspecialchars(substr($order['product_names'], 0, 25)); ?><?php echo strlen($order['product_names']) > 25 ? '...' : ''; ?>
                                    </small>
                                </div>
                            </td>
                            <td data-label="Payment">
                                <div class="d-flex flex-column">
                                    <?php echo getPaymentBadge($order['payment_status']); ?>
                                    <small class="text-muted mt-1">
                                        <i class="bi bi-credit-card me-1"></i><?php echo ucfirst(str_replace('_', ' ', $order['payment_method'])); ?>
                                    </small>
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="badge status-badge <?php echo getStatusBadge($order['order_status'], $order['priority_level']); ?>"
                                      style="cursor: pointer; font-size: 0.75rem;"
                                      onclick="changeOrderStatus(<?php echo $order['id']; ?>, '<?php echo $order['order_status']; ?>')"
                                      title="Click to change status">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $order['order_status'])); ?>
                                </span>
                            </td>
                            <td data-label="Priority">
                                <?php echo getPriorityBadge($order['priority_level']); ?>
                            </td>
                            <td data-label="Total">
                                <div class="fw-bold text-primary fs-6">
                                    <i class="bi bi-cash me-1"></i>RWF <?php echo number_format($order['total_amount'], 0); ?>
                                </div>
                            </td>
                            <td data-label="Date">
                                <div class="d-flex flex-column">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                            </td>
                            <td data-label="Actions">
                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                    <button class="btn btn-outline-primary btn-sm action-btn" onclick="viewOrder(<?php echo $order['id']; ?>)" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-sm action-btn" onclick="viewTimeline(<?php echo $order['id']; ?>)" title="View Timeline">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm action-btn" onclick="changePriority(<?php echo $order['id']; ?>, '<?php echo $order['priority_level']; ?>')" title="Change Priority">
                                        <i class="bi bi-star"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm action-btn" onclick="viewInvoice(<?php echo $order['id']; ?>)" title="View Invoice">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if (in_array($order['order_status'], ['pending', 'cancelled'])): ?>
                                    <button class="btn btn-outline-danger btn-sm action-btn" onclick="deleteOrder(<?php echo $order['id']; ?>, '<?php echo $order['order_number']; ?>')" title="Delete Order">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($result->num_rows === 0): ?>
        <div class="table-empty">
            <i class="bi bi-receipt"></i>
            <h4>No Orders Found</h4>
            <p>No orders match your current filters. Try adjusting your search criteria.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Order Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-repeat me-2"></i>Update Order Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="statusOrderId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Order Status</label>
                            <select class="form-select" name="order_status" id="orderStatus" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="processing">Processing</option>
                                <option value="ready">Ready for Shipping</option>
                                <option value="packed">Packed</option>
                                <option value="shipped">Shipped</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="trackingFields" style="display: none;">
                            <label class="form-label fw-semibold">Courier Name</label>
                            <input type="text" class="form-control" name="courier_name" placeholder="e.g., DHL Rwanda">
                            <div class="mt-2">
                                <label class="form-label fw-semibold">Tracking Number</label>
                                <input type="text" class="form-control" name="tracking_number" placeholder="Enter tracking number">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Status Notes (Optional)</label>
                            <textarea class="form-control" name="status_notes" rows="3" placeholder="Add notes about this status change..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Priority Modal -->
<div class="modal fade" id="priorityModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-star me-2"></i>Change Order Priority
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="priorityOrderId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Priority Level</label>
                        <select class="form-select" name="priority_level" id="priorityLevel" required>
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_priority" class="btn btn-primary">Update Priority</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.status-badge:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.stats-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.card-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.card-title {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
}

/* Enhanced table styles for better responsiveness and compactness */
.table-responsive {
    overflow-x: auto;
    margin: 0;
}

#ordersTable {
    min-width: 1000px;
    font-size: 0.875rem;
}

#ordersTable th, #ordersTable td {
    padding: 0.25rem 0.5rem;
    vertical-align: middle;
    white-space: nowrap;
}

/* Column widths for better space utilization */
#ordersTable th:nth-child(1), #ordersTable td:nth-child(1) { width: 8%; min-width: 80px; } /* Order ID */
#ordersTable th:nth-child(2), #ordersTable td:nth-child(2) { width: 12%; min-width: 100px; } /* Customer */
#ordersTable th:nth-child(3), #ordersTable td:nth-child(3) { width: 8%; min-width: 70px; } /* Type */
#ordersTable th:nth-child(4), #ordersTable td:nth-child(4) { width: 10%; min-width: 90px; } /* Items */
#ordersTable th:nth-child(5), #ordersTable td:nth-child(5) { width: 10%; min-width: 90px; } /* Payment */
#ordersTable th:nth-child(6), #ordersTable td:nth-child(6) { width: 10%; min-width: 90px; } /* Status */
#ordersTable th:nth-child(7), #ordersTable td:nth-child(7) { width: 8%; min-width: 70px; } /* Priority */
#ordersTable th:nth-child(8), #ordersTable td:nth-child(8) { width: 10%; min-width: 90px; } /* Total */
#ordersTable th:nth-child(9), #ordersTable td:nth-child(9) { width: 10%; min-width: 90px; } /* Date */
#ordersTable th:nth-child(10), #ordersTable td:nth-child(10) { width: 14%; min-width: 120px; } /* Actions */

/* Action buttons styling */
.action-btn {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    margin: 0.125rem;
}

.btn-group-sm .btn {
    padding: 0.125rem 0.25rem;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    #ordersTable th:nth-child(2), #ordersTable td:nth-child(2) { width: 15%; }
    #ordersTable th:nth-child(4), #ordersTable td:nth-child(4) { width: 12%; }
    #ordersTable th:nth-child(5), #ordersTable td:nth-child(5) { width: 12%; }
    #ordersTable th:nth-child(10), #ordersTable td:nth-child(10) { width: 16%; }
}

@media (max-width: 768px) {
    #ordersTable th, #ordersTable td {
        padding: 0.125rem 0.25rem;
        font-size: 0.75rem;
    }
    .action-btn {
        padding: 0.1rem 0.2rem;
        font-size: 0.7rem;
    }
}

/* Fix for left margin overlap */
.admin-page {
    margin-left: 0;
    padding-left: 15px;
}

.form-card {
    margin-left: 0;
}
</style>

<script>
// Order management functions
function changeOrderStatus(orderId, currentStatus) {
    document.getElementById('statusOrderId').value = orderId;
    document.getElementById('orderStatus').value = currentStatus;

    // Show tracking fields for shipped status
    const trackingFields = document.getElementById('trackingFields');
    const statusSelect = document.getElementById('orderStatus');

    function toggleTrackingFields() {
        const showTracking = ['shipped', 'out_for_delivery'].includes(statusSelect.value);
        trackingFields.style.display = showTracking ? 'block' : 'none';
    }

    statusSelect.addEventListener('change', toggleTrackingFields);
    toggleTrackingFields();

    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function changePriority(orderId, currentPriority) {
    document.getElementById('priorityOrderId').value = orderId;
    document.getElementById('priorityLevel').value = currentPriority;
    new bootstrap.Modal(document.getElementById('priorityModal')).show();
}

function deleteOrder(orderId, orderNumber) {
    if (confirm(`Are you sure you want to delete order ${orderNumber}? This action cannot be undone.`)) {
        window.location.href = `?delete=${orderId}`;
    }
}

function viewOrder(orderId) {
    // TODO: Implement order detail view
    window.open(`view_order.php?id=${orderId}`, '_blank');
}

function viewTimeline(orderId) {
    // TODO: Implement order timeline view
    alert('Order timeline view coming soon!');
}

function viewInvoice(orderId) {
    // View existing invoice
    window.open(`generate_invoice.php?id=${orderId}`, '_blank');
}

function exportOrders() {
    // TODO: Implement export functionality
    alert('Export feature coming soon!');
}

function viewAnalytics() {
    // TODO: Implement analytics view
    window.open('analytics.php', '_blank');
}

// Auto-refresh functionality
setInterval(function() {
    // Check for urgent orders every 60 seconds
    fetch('../api/get_urgent_orders.php')
        .then(response => response.json())
        .then(data => {
            if (data.urgent_count > 0) {
                showNotification(`You have ${data.urgent_count} urgent order(s) requiring attention!`, 'danger');
            }
        })
        .catch(error => console.log('Auto-refresh check failed'));
}, 60000);

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 8000);
}
</script>

<?php include '../footer.php'; ?>