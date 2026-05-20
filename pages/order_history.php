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

<!-- Page Hero -->
<div class="spx-page-hero">
    <div class="container position-relative">
        <h1 class="fw-bold mb-2"><i class="fas fa-shopping-bag me-2"></i>Order History</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/pages/my_account.php">My Account</a></li>
                <li class="breadcrumb-item active">Order History</li>
            </ol>
        </nav>
    </div>
</div>

<div class="spx-portal-wrap py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="spx-sidebar">
                    <div class="spx-sidebar-header">
                        <div class="spx-sidebar-avatar"><i class="fas fa-shopping-bag"></i></div>
                        <div class="spx-sidebar-name">Order History</div>
                        <div class="spx-sidebar-email"><?php echo $total_orders; ?> total orders</div>
                    </div>
                    <nav class="spx-sidebar-nav">
                        <a href="/pages/my_account.php"><i class="fas fa-th-large"></i>Overview</a>
                        <a href="#" class="active" data-filter="all" onclick="filterOrders('all', this);return false;"><i class="fas fa-list"></i>All Orders</a>
                        <a href="#" data-filter="pending" onclick="filterOrders('pending', this);return false;"><i class="fas fa-clock"></i>Pending</a>
                        <a href="#" data-filter="delivered" onclick="filterOrders('delivered', this);return false;"><i class="fas fa-check-circle"></i>Delivered</a>
                        <a href="#" data-filter="cancelled" onclick="filterOrders('cancelled', this);return false;"><i class="fas fa-times-circle"></i>Cancelled</a>
                        <div class="spx-sidebar-divider"></div>
                        <a href="messages.php"><i class="fas fa-comments"></i>Messages</a>
                    </nav>
                    <div class="px-3 py-3 border-top">
                        <div class="d-flex justify-content-between mb-1"><small class="text-muted">Total Orders</small><strong><?php echo $total_orders; ?></strong></div>
                        <div class="d-flex justify-content-between"><small class="text-muted">This Month</small><strong><?php echo count(array_filter($orders, fn($o) => date('Y-m', strtotime($o['created_at'])) === date('Y-m'))); ?></strong></div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="spx-panel mb-4">
                    <div class="spx-panel-header">
                        <h5 class="spx-panel-title" id="ordersPanelTitle">Your Orders</h5>
                        <select class="form-select form-select-sm" style="width:auto;" id="sortOrders">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                    <div class="spx-panel-body">

                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-bag fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">No orders found</h4>
                            <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                            <a href="/pages/shop.php" class="btn btn-primary"><i class="fas fa-shopping-cart me-2"></i>Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach ($orders as $order): ?>
                                <div class="spx-order-card mb-3"
                                     data-order-id="<?php echo $order['id']; ?>"
                                     data-status="<?php echo htmlspecialchars(strtolower($order['order_status'] ?? 'pending')); ?>"
                                     data-created="<?php echo htmlspecialchars($order['created_at']); ?>">
                                    <div class="row align-items-center g-2">
                                        <div class="col-md-3">
                                            <div class="spx-order-number">#<?php echo htmlspecialchars($order['order_number'] ?? str_pad($order['id'],6,'0',STR_PAD_LEFT)); ?></div>
                                            <div class="spx-order-date"><?php echo date('M j, Y', strtotime($order['created_at'])); ?> &middot; <?php echo $order['item_count']; ?> item<?php echo $order['item_count']!=1?'s':''; ?></div>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="spx-status-badge <?php echo strtolower($order['order_status']); ?>"><?php echo ucfirst($order['order_status']); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="spx-status-badge <?php echo strtolower($order['payment_status']); ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                                        </div>
                                        <div class="col-md-2">
                                            <strong class="text-primary">RWF <?php echo number_format($order['total_amount'],0); ?></strong>
                                        </div>
                                        <div class="col-md-3 text-md-end d-flex gap-2 justify-content-md-end">
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetails(<?php echo $order['id']; ?>)"><i class="fas fa-eye me-1"></i>Details</button>
                                            <?php if (strtolower($order['payment_status'])==='paid'): ?>
                                                <button class="btn btn-outline-success btn-sm" onclick="downloadInvoice(<?php echo $order['id']; ?>)"><i class="fas fa-download"></i></button>
                                            <?php endif; ?>
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
                                                <div class="mt-4">
                                                    <h6>Tracking Updates</h6>
                                                    <div id="order-timeline-<?php echo $order['id']; ?>">
                                                        <!-- Timeline will be loaded here -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div><!-- /orders-list -->
                        <div id="ordersFilterEmpty" class="orders-filter-empty text-center py-5" style="display:none;">
                            <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">No orders in this view</h5>
                            <p class="text-muted mb-0">Try another status filter.</p>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Order pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?php echo $current_page - 1; ?>"><i class="fas fa-chevron-left"></i></a></li>
                                    <?php endif; ?>
                                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                    <?php endfor; ?>
                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item"><a class="page-link" href="?page=<?php echo $current_page + 1; ?>"><i class="fas fa-chevron-right"></i></a></li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?><!-- /empty check -->
                    </div><!-- /spx-panel-body -->
                </div><!-- /spx-panel -->
            </div><!-- /col-lg-9 -->
        </div><!-- /row -->
    </div><!-- /container -->
</div><!-- /spx-portal-wrap -->


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
.spx-order-card { cursor: pointer; }
.orders-filter-empty {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 10px;
}
.customer-timeline {
    border-left: 2px solid #e9ecef;
    margin-left: 8px;
    padding-left: 14px;
}
.customer-timeline-item {
    position: relative;
    background: #f8f9fa;
    border-radius: 8px;
    padding: 10px 12px;
    margin-bottom: 10px;
}
.customer-timeline-item::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 14px;
    width: 10px;
    height: 10px;
    background: #0d6efd;
    border-radius: 50%;
    border: 2px solid #fff;
}
.customer-timeline-item.note::before {
    background: #198754;
}
@media (max-width: 768px) { .spx-sidebar { position: static !important; } }
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
        loadOrderTimeline(orderId);
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

function loadOrderTimeline(orderId) {
    const timelineContainer = document.getElementById(`order-timeline-${orderId}`);
    if (!timelineContainer || timelineContainer.innerHTML.trim() !== '') {
        return;
    }

    timelineContainer.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm" role="status"></div></div>';

    fetch(`/api/get_order_timeline.php?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                timelineContainer.innerHTML = '<p class="text-muted small">Unable to load tracking updates.</p>';
                return;
            }

            const timeline = data.timeline || [];
            const notes = data.notes || [];

            if (timeline.length === 0 && notes.length === 0) {
                timelineContainer.innerHTML = '<p class="text-muted small">No tracking updates yet.</p>';
                return;
            }

            let html = '<div class="customer-timeline">';
            timeline.forEach(item => {
                const label = String(item.status || 'update').replace(/_/g, ' ');
                const date = item.created_at ? new Date(item.created_at).toLocaleString() : '';
                html += `
                    <div class="customer-timeline-item">
                        <div class="fw-semibold text-capitalize">${escapeHtml(label)}</div>
                        <div class="small text-muted">${escapeHtml(date)}</div>
                        ${item.description ? `<div class="small">${escapeHtml(item.description)}</div>` : ''}
                        ${item.tracking_number ? `<div class="small text-info"><i class="fas fa-truck me-1"></i>${escapeHtml(item.tracking_number)} ${item.carrier_name ? `(${escapeHtml(item.carrier_name)})` : ''}</div>` : ''}
                    </div>
                `;
            });

            notes.forEach(note => {
                const date = note.created_at ? new Date(note.created_at).toLocaleString() : '';
                html += `
                    <div class="customer-timeline-item note">
                        <div class="fw-semibold">Message from support</div>
                        <div class="small text-muted">${escapeHtml(date)}</div>
                        <div class="small">${escapeHtml(note.content || '')}</div>
                    </div>
                `;
            });
            html += '</div>';
            timelineContainer.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading order timeline:', error);
            timelineContainer.innerHTML = '<p class="text-danger small">Error loading tracking updates.</p>';
        });
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, ch => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    }[ch]));
}

// Download invoice
function downloadInvoice(orderId) {
    window.open(`/api/download_invoice.php?order_id=${orderId}`, '_blank');
}

const orderStatusGroups = {
    all: [],
    pending: ['pending', 'confirmed', 'processing', 'packed', 'ready', 'shipped', 'out_for_delivery'],
    delivered: ['delivered', 'completed'],
    cancelled: ['cancelled', 'canceled', 'failed', 'refunded']
};

let currentOrderFilter = 'all';

function matchesOrderFilter(card, filter) {
    if (filter === 'all') {
        return true;
    }

    const status = (card.dataset.status || '').toLowerCase();
    return (orderStatusGroups[filter] || [filter]).includes(status);
}

function setOrdersPanelTitle(filter, visibleCount) {
    const title = document.getElementById('ordersPanelTitle');
    if (!title) {
        return;
    }

    const labels = {
        all: 'Your Orders',
        pending: 'Pending Orders',
        delivered: 'Delivered Orders',
        cancelled: 'Cancelled Orders'
    };

    title.textContent = `${labels[filter] || 'Your Orders'} (${visibleCount})`;
}

// Filter orders
function filterOrders(status, selectedLink = null) {
    currentOrderFilter = status || 'all';

    document.querySelectorAll('.spx-sidebar-nav a[data-filter]').forEach(link => {
        link.classList.toggle('active', link.dataset.filter === currentOrderFilter);
    });

    if (selectedLink) {
        selectedLink.classList.add('active');
    }

    let visibleCount = 0;
    document.querySelectorAll('.spx-order-card').forEach(card => {
        const shouldShow = matchesOrderFilter(card, currentOrderFilter);
        card.style.display = shouldShow ? '' : 'none';

        if (shouldShow) {
            visibleCount++;
        } else {
            const details = card.querySelector('.order-details');
            if (details) {
                details.style.display = 'none';
            }
        }
    });

    const emptyState = document.getElementById('ordersFilterEmpty');
    if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? '' : 'none';
    }

    setOrdersPanelTitle(currentOrderFilter, visibleCount);
}

// Sort orders
function sortOrders() {
    const sortSelect = document.getElementById('sortOrders');
    const sortBy = sortSelect ? sortSelect.value : 'newest';
    const ordersList = document.querySelector('.orders-list');
    if (!ordersList) {
        return;
    }

    const cards = Array.from(ordersList.querySelectorAll('.spx-order-card'));
    cards.sort((a, b) => {
        const firstDate = new Date(a.dataset.created || 0).getTime();
        const secondDate = new Date(b.dataset.created || 0).getTime();
        return sortBy === 'oldest' ? firstDate - secondDate : secondDate - firstDate;
    });

    cards.forEach(card => ordersList.appendChild(card));
    filterOrders(currentOrderFilter);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sortOrders');
    if (sortSelect) {
        sortSelect.addEventListener('change', sortOrders);
    }

    filterOrders(currentOrderFilter);

    // Add click handlers to order cards
    document.querySelectorAll('.spx-order-card').forEach(card => {
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
