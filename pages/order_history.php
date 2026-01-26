<?php
// Session check must happen BEFORE any HTML output
include '../includes/client_session_check.php';

$page_title = 'Order History - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

// Initialize variables
$orders = [];
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($current_page - 1) * $per_page;

// Fetch orders for current customer
if (isset($_SESSION['customer_id'])) {
    // Get total count
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders_enhanced WHERE customer_id = ?");
    $count_stmt->bind_param("i", $_SESSION['customer_id']);
    $count_stmt->execute();
    $total_orders = $count_stmt->get_result()->fetch_assoc()['total'];
    $count_stmt->close();

    // Get orders with pagination
    $stmt = $conn->prepare("
        SELECT o.*,
               COUNT(oi.id) as item_count,
               COALESCE(SUM(oi.quantity * oi.unit_price), 0) as total_amount
        FROM orders_enhanced o
        LEFT JOIN order_items_enhanced oi ON o.id = oi.order_id
        WHERE o.customer_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("iii", $_SESSION['customer_id'], $per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();

    $total_pages = ceil($total_orders / $per_page);
}

// Get order status badge class
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'bg-warning text-dark';
        case 'confirmed': return 'bg-info';
        case 'processing': return 'bg-primary';
        case 'shipped': return 'bg-info';
        case 'delivered': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Get payment status badge class
function getPaymentBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending': return 'bg-warning text-dark';
        case 'paid': return 'bg-success';
        case 'failed': return 'bg-danger';
        case 'refunded': return 'bg-info';
        default: return 'bg-secondary';
    }
}
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="display-4 text-white fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <i class="fas fa-shopping-bag me-3"></i>Order History
                </h1>
                <nav aria-label="breadcrumb" class="wow fadeInUp" data-wow-delay="0.3s">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/pages/my_account.php">My Account</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Order History</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Order History Section Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="bg-white p-4 rounded-3 shadow-sm sticky-top" style="top: 20px;">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-shopping-bag fa-2x text-primary"></i>
                        </div>
                        <h5 class="mt-3 mb-1">Order History</h5>
                        <p class="text-muted small"><?php echo count($orders); ?> orders found</p>
                    </div>

                    <nav class="nav flex-column">
                        <a href="/pages/my_account.php" class="nav-link mb-2">
                            <i class="fas fa-user me-2"></i>My Account
                        </a>
                        <a href="#all-orders" class="nav-link active mb-2" onclick="filterOrders('all')">
                            <i class="fas fa-list me-2"></i>All Orders
                        </a>
                        <a href="#pending" class="nav-link mb-2" onclick="filterOrders('pending')">
                            <i class="fas fa-clock me-2"></i>Pending
                        </a>
                        <a href="#delivered" class="nav-link mb-2" onclick="filterOrders('delivered')">
                            <i class="fas fa-check-circle me-2"></i>Delivered
                        </a>
                        <a href="#cancelled" class="nav-link mb-2" onclick="filterOrders('cancelled')">
                            <i class="fas fa-times-circle me-2"></i>Cancelled
                        </a>
                    </nav>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Quick Stats</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Total Orders:</span>
                            <span class="fw-bold"><?php echo $total_orders; ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small">This Month:</span>
                            <span class="fw-bold"><?php echo count(array_filter($orders, function($order) {
                                return date('Y-m', strtotime($order['created_at'])) === date('Y-m');
                            })); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 wow fadeInRight" data-wow-delay="0.3s">
                <!-- Orders List -->
                <div class="bg-white p-4 rounded-3 shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Your Orders</h4>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="sortOrders" onchange="sortOrders()">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="amount_high">Amount: High to Low</option>
                                <option value="amount_low">Amount: Low to High</option>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No orders found</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your order history here.</p>
                            <a href="/pages/shop.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="order-card border rounded p-4 mb-3" data-order-id="<?php echo $order['id']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="order-number">
                                                <h6 class="mb-1">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h6>
                                                <small class="text-muted"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></small>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="order-items">
                                                <p class="mb-1 fw-bold"><?php echo $order['item_count']; ?> item<?php echo $order['item_count'] > 1 ? 's' : ''; ?></p>
                                                <small class="text-muted">Click to view details</small>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="order-total">
                                                <h6 class="mb-1 text-primary">RWF <?php echo number_format($order['total_amount'], 0, '.', ','); ?></h6>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="order-status">
                                                <span class="badge <?php echo getPaymentBadgeClass($order['payment_status']); ?> mb-1">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                                <br>
                                                <span class="badge <?php echo getStatusBadgeClass($order['order_status']); ?>">
                                                    <?php echo ucfirst($order['order_status']); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="order-actions d-flex gap-2">
                                                <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </button>
                                                <?php if (strtolower($order['payment_status']) === 'paid'): ?>
                                                    <button class="btn btn-outline-success btn-sm" onclick="downloadInvoice(<?php echo $order['id']; ?>)">
                                                        <i class="fas fa-download me-1"></i>Invoice
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Details (Initially Hidden) -->
                                    <div class="order-details mt-3" id="order-details-<?php echo $order['id']; ?>" style="display: none;">
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h6>Order Items</h6>
                                                <div id="order-items-<?php echo $order['id']; ?>">
                                                    <!-- Order items will be loaded here -->
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Order Information</h6>
                                                <div class="order-info">
                                                    <p class="mb-1"><strong>Order Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></p>
                                                    <p class="mb-1"><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method'] ?? 'N/A'); ?></p>
                                                    <p class="mb-1"><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($order['shipping_address'] ?? 'N/A')); ?></p>
                                                    <?php if (!empty($order['tracking_number'])): ?>
                                                        <p class="mb-1"><strong>Tracking Number:</strong> <?php echo htmlspecialchars($order['tracking_number']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Order pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Order History Section End -->

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="orderDetailsModalLabel">
                    <i class="fas fa-shopping-bag me-2"></i>Order Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" id="downloadInvoiceBtn">
                    <i class="fas fa-download me-1"></i>Download Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.order-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.order-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.1);
}

.order-details {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 1000px;
    }
}

.nav-link {
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-link:hover,
.nav-link.active {
    color: #007bff;
    background-color: rgba(0, 123, 255, 0.1);
}

@media (max-width: 768px) {
    .order-card .row {
        text-align: center;
    }

    .order-actions {
        justify-content: center;
        margin-top: 1rem;
    }

    .sticky-top {
        position: static !important;
    }
}
</style>

<script>
// View order details
function viewOrderDetails(orderId) {
    const detailsDiv = document.getElementById(`order-details-${orderId}`);
    const isVisible = detailsDiv.style.display !== 'none';

    // Hide all other order details
    document.querySelectorAll('.order-details').forEach(div => {
        div.style.display = 'none';
    });

    if (!isVisible) {
        // Load order items if not already loaded
        loadOrderItems(orderId);
        detailsDiv.style.display = 'block';
    }
}

// Load order items via AJAX
function loadOrderItems(orderId) {
    const itemsContainer = document.getElementById(`order-items-${orderId}`);

    if (itemsContainer.innerHTML.trim() !== '') {
        return; // Already loaded
    }

    itemsContainer.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div></div>';

    fetch(`/api/get_order_items.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderOrderItems(itemsContainer, data.items);
            } else {
                itemsContainer.innerHTML = '<p class="text-muted">Unable to load order items.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading order items:', error);
            itemsContainer.innerHTML = '<p class="text-danger">Error loading order items.</p>';
        });
}

// Render order items
function renderOrderItems(container, items) {
    if (items.length === 0) {
        container.innerHTML = '<p class="text-muted">No items found in this order.</p>';
        return;
    }

    let html = '<div class="order-items-list">';
    items.forEach(item => {
        html += `
            <div class="order-item d-flex align-items-center mb-3 p-3 border rounded">
                <img src="${item.image || '/img/no-image.png'}" alt="${item.product_name}"
                     class="rounded me-3" style="width: 60px; height: 60px; object-fit: contain;">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${item.product_name}</h6>
                    <p class="text-muted mb-1">${item.brand || ''} ${item.model || ''}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Qty: ${item.quantity}</span>
                        <span class="fw-bold text-primary">RWF ${item.price.toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    container.innerHTML = html;
}

// Download invoice
function downloadInvoice(orderId) {
    window.open(`/api/download_invoice.php?order_id=${orderId}`, '_blank');
}

// Filter orders
function filterOrders(status) {
    // Update active nav link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    event.target.classList.add('active');

    // For now, just show all orders (filtering can be implemented with AJAX)
    alert(`Filter by ${status} - Feature coming soon!`);
}

// Sort orders
function sortOrders() {
    const sortBy = document.getElementById('sortOrders').value;
    alert(`Sort by ${sortBy} - Feature coming soon!`);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to order cards
    document.querySelectorAll('.order-card').forEach(card => {
        const orderId = card.dataset.orderId;
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons
            if (!e.target.closest('.btn')) {
                viewOrderDetails(orderId);
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>