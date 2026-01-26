<?php
// Enhanced All Orders Management for SPARE XPRESS LTD
ob_start();
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $request_id = (int)$_POST['request_id'];
    $status = $_POST['request_status'];
    $quoted_price = isset($_POST['quoted_price']) ? (float)$_POST['quoted_price'] : null;
    $status_notes = trim($_POST['status_notes'] ?? '');

    $update_fields = ["status = ?"];
    $params = [$status];
    $types = "s";

    if ($quoted_price !== null) {
        $update_fields[] = "quoted_price = ?";
        $params[] = $quoted_price;
        $types .= "d";
    }

    $update_fields[] = "updated_at = NOW()";
    $params[] = $request_id;
    $types .= "i";

    $query = "UPDATE order_requests SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Add timeline entry
        $timeline_stmt = $conn->prepare("INSERT INTO request_timeline
            (request_id, status, status_description, created_by)
            VALUES (?, ?, ?, ?)");

        $description = $status_notes ?: "Request status changed to " . ucfirst($status);
        $admin_id = 1;
        $timeline_stmt->bind_param("issi", $request_id, $status, $description, $admin_id); // admin_id = 1
        $timeline_stmt->execute();

        $_SESSION['success'] = 'Request status updated successfully';
    } else {
        $_SESSION['error'] = 'Failed to update request status';
    }

    header('Location: order_demand_list.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $request_id = (int)$_GET['delete'];

    // Check if request can be deleted (only pending requests)
    $check_stmt = $conn->prepare("SELECT status FROM order_requests WHERE id = ?");
    $check_stmt->bind_param("i", $request_id);
    $check_stmt->execute();
    $result_check = $check_stmt->get_result();
    $request = $result_check->fetch_assoc();

    if ($request && in_array($request['status'], ['pending', 'cancelled'])) {
        $stmt = $conn->prepare("DELETE FROM order_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Request deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete request';
        }
    } else {
        $_SESSION['error'] = 'Cannot delete request that is being processed or delivered';
    }

    header('Location: order_demand_list.php');
    exit;
}

// Get orders with filtering
$where_conditions = [];
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';
$type_filter = $_GET['type'] ?? 'all';
$brand_filter = $_GET['brand'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

if (!empty($search)) {
    $where_conditions[] = "(id LIKE '%" . $conn->real_escape_string($search) . "%' OR
                           customer_name LIKE '%" . $conn->real_escape_string($search) . "%' OR
                           part_name LIKE '%" . $conn->real_escape_string($search) . "%' OR
                           phone_number LIKE '%" . $conn->real_escape_string($search) . "%')";
}

if ($status_filter !== 'all') {
    $where_conditions[] = "status = '" . $conn->real_escape_string($status_filter) . "'";
}

if ($type_filter !== 'all') {
    $where_conditions[] = "order_type = '" . $conn->real_escape_string($type_filter) . "'";
}

if ($brand_filter !== 'all') {
    $where_conditions[] = "vehicle_brand = '" . $conn->real_escape_string($brand_filter) . "'";
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= '" . $conn->real_escape_string($date_from) . "'";
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= '" . $conn->real_escape_string($date_to) . "'";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT * FROM order_requests
           $where_clause
           ORDER BY
               CASE order_type
                   WHEN 'urgent' THEN 1
                   WHEN 'normal' THEN 2
               END,
               created_at DESC";
$result = $conn->query($query);

// Get statistics
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as count FROM order_requests")->fetch_assoc()['count'],
    'pending' => $conn->query("SELECT COUNT(*) as count FROM order_requests WHERE status = 'pending'")->fetch_assoc()['count'],
    'processing' => $conn->query("SELECT COUNT(*) as count FROM order_requests WHERE status IN ('sourcing', 'quoted', 'approved')")->fetch_assoc()['count'],
    'shipped' => $conn->query("SELECT COUNT(*) as count FROM order_requests WHERE status IN ('shipped', 'out_for_delivery')")->fetch_assoc()['count'],
    'delivered' => $conn->query("SELECT COUNT(*) as count FROM order_requests WHERE status = 'delivered'")->fetch_assoc()['count'],
    'cancelled' => $conn->query("SELECT COUNT(*) as count FROM order_requests WHERE status = 'cancelled'")->fetch_assoc()['count'],
    'urgent' => $conn->query("SELECT COUNT(*) as count FROM order_requests WHERE order_type = 'urgent' AND status NOT IN ('delivered', 'cancelled')")->fetch_assoc()['count']
];

function getStatusBadge($status) {
    $base_classes = "badge ";

    $status_classes = [
        'pending' => 'bg-warning text-dark',
        'sourcing' => 'bg-info',
        'quoted' => 'bg-primary',
        'approved' => 'bg-success',
        'shipped' => 'bg-info',
        'out_for_delivery' => 'bg-info',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];

    $class = $status_classes[$status] ?? 'bg-secondary';
    return $base_classes . $class;
}

?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-star-fill text-warning me-3"></i>
                Order Requests
            </h1>
            <p class="text-muted mb-0 fs-5">Manage special part requests that are not in stock</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" onclick="exportOrders()">
                <i class="bi bi-download me-1"></i>Export Orders
            </button>
            <a href="/admin/orders/enhanced_order_management.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Orders
            </a>
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
                <label class="form-label fw-semibold">Search Requests</label>
                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Request ID, Customer name, Part name...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="sourcing" <?php echo $status_filter === 'sourcing' ? 'selected' : ''; ?>>Sourcing</option>
                    <option value="quoted" <?php echo $status_filter === 'quoted' ? 'selected' : ''; ?>>Quoted</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Type</label>
                <select class="form-select" name="type">
                    <option value="all" <?php echo $type_filter === 'all' ? 'selected' : ''; ?>>All Types</option>
                    <option value="normal" <?php echo $type_filter === 'normal' ? 'selected' : ''; ?>>Normal</option>
                    <option value="urgent" <?php echo $type_filter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Brand</label>
                <select class="form-select" name="brand">
                    <option value="all" <?php echo $brand_filter === 'all' ? 'selected' : ''; ?>>All Brands</option>
                    <?php
                    $brands = $conn->query("SELECT DISTINCT vehicle_brand FROM order_requests ORDER BY vehicle_brand");
                    while ($brand = $brands->fetch_assoc()) {
                        $selected = $brand_filter === $brand['vehicle_brand'] ? 'selected' : '';
                        echo "<option value='{$brand['vehicle_brand']}' $selected>{$brand['vehicle_brand']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="order_demand_list.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="form-card">
        <div class="table-responsive">
            <table class="table table-hover" id="requestsTable">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">
                            <i class="bi bi-hash"></i>
                            <span>Request ID</span>
                        </th>
                        <th class="sortable" data-column="customer_name">
                            <i class="bi bi-person"></i>
                            <span>Customer</span>
                        </th>
                        <th>
                            <i class="bi bi-car"></i>
                            <span>Vehicle</span>
                        </th>
                        <th>
                            <i class="bi bi-gear"></i>
                            <span>Part Requested</span>
                        </th>
                        <th>
                            <i class="bi bi-tag"></i>
                            <span>Type</span>
                        </th>
                        <th class="sortable" data-column="status">
                            <i class="bi bi-truck"></i>
                            <span>Status</span>
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
                    <?php while ($request = $result->fetch_assoc()): ?>
                        <tr class="<?php echo $request['order_type'] === 'urgent' ? 'table-warning' : ''; ?>">
                            <td data-label="Request ID">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">#<?php echo htmlspecialchars($request['id']); ?></span>
                                    <?php if ($request['order_type'] === 'urgent'): ?>
                                        <i class="bi bi-exclamation-triangle-fill text-danger" title="Urgent Request"></i>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Customer">
                                <div class="fw-semibold text-truncate" style="max-width: 150px;" title="<?php echo htmlspecialchars($request['customer_name']); ?>">
                                    <?php echo htmlspecialchars($request['customer_name']); ?>
                                </div>
                                <small class="text-muted"><?php echo htmlspecialchars($request['phone_number']); ?></small>
                            </td>
                            <td data-label="Vehicle">
                                <div class="small">
                                    <strong><?php echo htmlspecialchars($request['vehicle_brand']); ?></strong><br>
                                    <?php echo htmlspecialchars($request['vehicle_model']); ?>
                                    <?php if ($request['year']): ?>
                                        (<?php echo $request['year']; ?>)
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Part Requested">
                                <div class="fw-semibold small"><?php echo htmlspecialchars(substr($request['part_name'], 0, 30)); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars(substr($request['part_description'] ?: 'No description', 0, 40)); ?>...</small>
                                <?php
                                $images = json_decode($request['images'], true);
                                if (!empty($images)): ?>
                                    <br><small class="text-info"><i class="bi bi-image me-1"></i><?php echo count($images); ?> image(s)</small>
                                <?php endif; ?>
                            </td>
                            <td data-label="Type">
                                <?php
                                $type_badges = [
                                    'normal' => '<span class="badge bg-info">Normal</span>',
                                    'urgent' => '<span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Urgent</span>'
                                ];
                                echo $type_badges[$request['order_type']] ?? '<span class="badge bg-secondary">Unknown</span>';
                                ?>
                            </td>
                            <td data-label="Status">
                                <span class="badge status-badge <?php echo getStatusBadge($request['status']); ?>"
                                      style="cursor: pointer; font-size: 0.75rem;"
                                      onclick="changeRequestStatus(<?php echo $request['id']; ?>, '<?php echo $request['status']; ?>')">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                </span>
                            </td>
                            <td data-label="Date">
                                <div class="d-flex flex-column">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <?php echo date('M d, Y', strtotime($request['created_at'])); ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i><?php echo date('H:i', strtotime($request['created_at'])); ?>
                                    </small>
                                </div>
                            </td>
                            <td data-label="Actions">
                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                    <button class="btn btn-outline-primary btn-sm action-btn" onclick="viewRequest(<?php echo $request['id']; ?>)" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-sm action-btn" onclick="viewTimeline(<?php echo $request['id']; ?>)" title="View Timeline">
                                        <i class="bi bi-clock-history"></i>
                                    </button>
                                    <button class="btn btn-outline-success btn-sm action-btn" onclick="generateQuote(<?php echo $request['id']; ?>)" title="Generate Quote">
                                        <i class="bi bi-cash"></i>
                                    </button>
                                    <?php if (in_array($request['status'], ['pending', 'cancelled'])): ?>
                                    <button class="btn btn-outline-danger btn-sm action-btn" onclick="deleteRequest(<?php echo $request['id']; ?>, '<?php echo $request['id']; ?>')" title="Delete Request">
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
            <i class="bi bi-star"></i>
            <h4>No Requests Found</h4>
            <p>No on-demand requests match your current filters. Try adjusting your search criteria.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-repeat me-2"></i>Update Request Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="requestId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Request Status</label>
                            <select class="form-select" name="request_status" id="requestStatus" required>
                                <option value="pending">Pending</option>
                                <option value="sourcing">Sourcing</option>
                                <option value="quoted">Quoted</option>
                                <option value="approved">Approved</option>
                                <option value="shipped">Shipped</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="quoteFields" style="display: none;">
                            <label class="form-label fw-semibold">Quoted Price (RWF)</label>
                            <input type="number" class="form-control" name="quoted_price" placeholder="Enter your quote" step="0.01">
                            <div class="form-text">Leave empty if not quoting yet</div>
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

<style>
.status-badge:hover {
    opacity: 0.8;
    transform: scale(1.05);
    transition: all 0.2s ease;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table-empty {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.table-empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
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
</style>

<script>
// Request management functions
function changeRequestStatus(requestId, currentStatus) {
    document.getElementById('requestId').value = requestId;
    document.getElementById('requestStatus').value = currentStatus;

    // Show quote fields for quoted status
    const quoteFields = document.getElementById('quoteFields');
    const statusSelect = document.getElementById('requestStatus');

    function toggleQuoteFields() {
        const showQuote = ['quoted', 'approved'].includes(statusSelect.value);
        quoteFields.style.display = showQuote ? 'block' : 'none';
    }

    statusSelect.addEventListener('change', toggleQuoteFields);
    toggleQuoteFields();

    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function deleteRequest(requestId, requestNumber) {
    if (confirm(`Are you sure you want to delete request ${requestNumber}? This action cannot be undone.`)) {
        window.location.href = `?delete=${requestId}`;
    }
}

function viewRequest(requestId) {
    // TODO: Implement request detail view
    window.open(`view_request.php?id=${requestId}`, '_blank');
}

function viewTimeline(requestId) {
    // TODO: Implement request timeline view
    alert('Request timeline view coming soon!');
}

function generateQuote(requestId) {
    // TODO: Implement quote generation
    alert('Quote generation coming soon!');
}

function exportRequests() {
    // TODO: Implement export functionality
    alert('Export feature coming soon!');
}

// Auto-refresh functionality
setInterval(function() {
    // Check for new urgent requests every 60 seconds
    fetch('../api/get_urgent_requests.php')
        .then(response => response.json())
        .then(data => {
            if (data.urgent_count > 0) {
                showNotification(`You have ${data.urgent_count} urgent request(s) requiring attention!`, 'warning');
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
