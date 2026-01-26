<?php
// Enhanced Professional Admin Dashboard for SPARE XPRESS LTD
include_once 'includes/auth.php';
include_once 'includes/functions.php';
include_once 'header.php';

// Get dashboard statistics
$stats = [
    'total_orders' => countRows('orders_enhanced'),
    'pending_orders' => countRowsWhere('orders_enhanced', "order_status = 'pending'"),
    'processing_orders' => countRowsWhere('orders_enhanced', "order_status = 'processing'"),
    'shipped_orders' => countRowsWhere('orders_enhanced', "order_status = 'shipped'"),
    'delivered_orders' => countRowsWhere('orders_enhanced', "order_status = 'delivered'"),
    'cancelled_orders' => countRowsWhere('orders_enhanced', "order_status = 'cancelled'"),
    'total_products' => countRows('products_enhanced'),
    'low_stock_products' => countRowsWhere('products_enhanced', 'stock_quantity <= 5 AND stock_quantity > 0'),
    'out_of_stock_products' => countRowsWhere('products_enhanced', 'stock_quantity = 0'),
    'total_brands' => countRows('vehicle_brands_enhanced'),
    'total_models' => countRows('vehicle_models_enhanced'),
    'total_customers' => countRows('customers_enhanced'),
    'on_demand_requests' => countRowsWhere('on_demand_requests_enhanced', "request_status IN ('pending', 'sourcing', 'quoted')"),
    'unpaid_orders' => countRowsWhere('orders_enhanced', "payment_status = 'unpaid'"),
    'recent_orders_count' => countRowsWhere('orders_enhanced', "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")
];

// Get recent orders for timeline
$recent_orders = $conn->query("
    SELECT o.*, c.first_name, c.last_name, c.phone,
           COUNT(oi.id) as item_count
    FROM orders_enhanced o
    LEFT JOIN customers_enhanced c ON o.customer_id = c.id
    LEFT JOIN order_items_enhanced oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
");

// Get low stock alerts
$low_stock_alerts = $conn->query("
    SELECT p.product_name, p.stock_quantity as stock, b.brand_name
    FROM products_enhanced p
    JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
    WHERE p.stock_quantity <= 5 AND p.stock_quantity > 0
    ORDER BY p.stock_quantity ASC
    LIMIT 5
");

// Get pending tasks
$pending_tasks = [
    'pending_orders' => $stats['pending_orders'],
    'low_stock_items' => $stats['low_stock_products'],
    'unpaid_orders' => $stats['unpaid_orders'],
    'on_demand_requests' => $stats['on_demand_requests']
];

// Calculate revenue estimates (last 30 days)
$revenue_query = $conn->query("
    SELECT SUM(total_amount) as total_revenue,
           COUNT(*) as order_count
    FROM orders_enhanced
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    AND payment_status = 'paid'
");
$revenue_data = $revenue_query->fetch_assoc();
$monthly_revenue = $revenue_data['total_revenue'] ?? 0;
$monthly_orders = $revenue_data['order_count'] ?? 0;

// Get order trends for charts (last 7 days)
$order_trends = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $count = countRowsWhere('orders_enhanced', "DATE(created_at) = '$date'");
    $order_trends[] = [
        'date' => date('M d', strtotime($date)),
        'count' => $count
    ];
}
?>

<div class="admin-page">
    <!-- Enhanced Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-speedometer2-fill text-primary me-3"></i>
                Professional Dashboard
            </h1>
            <p class="text-muted mb-0 fs-5">Real-time insights and enterprise management overview</p>
        </div>
        <div class="text-end">
            <div class="d-flex align-items-center gap-3">
                <div class="text-center">
                    <div class="fw-bold text-success fs-4" id="live-clock"><?php echo date('H:i:s'); ?></div>
                    <small class="text-muted"><?php echo date('l, F j, Y'); ?></small>
                </div>
                <div class="vr"></div>
                <div class="text-center">
                    <div class="badge bg-success fs-6 px-3 py-2" id="system-status">
                        <i class="bi bi-circle-fill me-1"></i>System Online
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Alerts & Notifications -->
    <div class="alerts-container mb-4" id="real-time-alerts">
        <!-- Dynamic alerts will be loaded here -->
    </div>

    <!-- Key Performance Indicators -->
    <div class="row g-4 mb-5">
        <!-- Orders Overview -->
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card kpi-card orders-card h-100" data-bs-toggle="tooltip" title="Total orders in system">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="bi bi-receipt-fill fs-1"></i>
                    </div>
                    <h2 class="card-value text-primary mb-2" data-stat="total_orders"><?php echo number_format($stats['total_orders']); ?></h2>
                    <p class="card-title mb-3">Total Orders</p>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-sm">
                        <span class="text-success">
                            <i class="bi bi-arrow-up me-1"></i><?php echo $stats['delivered_orders']; ?> Delivered
                        </span>
                        <span class="text-warning">
                            <i class="bi bi-clock me-1"></i><?php echo $stats['pending_orders']; ?> Pending
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Overview -->
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card kpi-card revenue-card h-100" data-bs-toggle="tooltip" title="Monthly revenue (last 30 days)">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                    <h2 class="card-value text-success mb-2" data-stat="monthly_revenue">RWF <?php echo number_format($monthly_revenue, 0); ?></h2>
                    <p class="card-title mb-3">Monthly Revenue</p>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-sm">
                        <span class="text-info">
                            <i class="bi bi-graph-up me-1"></i><?php echo $monthly_orders; ?> Orders
                        </span>
                        <span class="text-muted">
                            <i class="bi bi-calendar me-1"></i>Last 30 Days
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card kpi-card inventory-card h-100" data-bs-toggle="tooltip" title="Current inventory status">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="bi bi-box-seam-fill fs-1"></i>
                    </div>
                    <h2 class="card-value text-warning mb-2" data-stat="total_products"><?php echo number_format($stats['total_products']); ?></h2>
                    <p class="card-title mb-3">Total Products</p>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: 75%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-sm">
                        <span class="text-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i><?php echo $stats['low_stock_products']; ?> Low Stock
                        </span>
                        <span class="text-secondary">
                            <i class="bi bi-x-circle me-1"></i><?php echo $stats['out_of_stock_products']; ?> Out
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Metrics -->
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card kpi-card customers-card h-100" data-bs-toggle="tooltip" title="Customer and brand metrics">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                    <h2 class="card-value text-info mb-2" data-stat="total_customers"><?php echo number_format($stats['total_customers']); ?></h2>
                    <p class="card-title mb-3">Total Customers</p>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 60%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-sm">
                        <span class="text-primary">
                            <i class="bi bi-tags me-1"></i><?php echo $stats['total_brands']; ?> Brands
                        </span>
                        <span class="text-success">
                            <i class="bi bi-car-front me-1"></i><?php echo $stats['total_models']; ?> Models
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="row g-4 mb-5">
        <!-- Order Trends Chart -->
        <div class="col-xl-8">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-graph-up text-primary me-2"></i>
                                Order Trends (Last 7 Days)
                            </h5>
                            <p class="text-muted small mb-0">Daily order volume and patterns</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-calendar me-1"></i>Last 7 Days
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('7d')">Last 7 Days</a></li>
                                <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('30d')">Last 30 Days</a></li>
                                <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('90d')">Last 90 Days</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="orderTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- System Health & Quick Actions -->
        <div class="col-xl-4">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-1">
                        <i class="bi bi-activity text-success me-2"></i>
                        System Health
                    </h5>
                    <p class="text-muted small mb-0">Real-time system status</p>
                </div>
                <div class="card-body">
                    <!-- System Health Indicators -->
                    <div class="health-indicators mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="health-dot bg-success me-3"></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">Database</div>
                                <div class="text-muted small">Operational</div>
                            </div>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="health-dot bg-success me-3"></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">Web Server</div>
                                <div class="text-muted small">PHP <?php echo phpversion(); ?></div>
                            </div>
                            <span class="badge bg-success">Running</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="health-dot bg-warning me-3"></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">Storage</div>
                                <div class="text-muted small">75% Used</div>
                            </div>
                            <span class="badge bg-warning">Monitor</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="health-dot bg-info me-3"></div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">API Status</div>
                                <div class="text-muted small">All Endpoints Active</div>
                            </div>
                            <span class="badge bg-info">Active</span>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <h6 class="mb-3">
                            <i class="bi bi-lightning-charge text-warning me-2"></i>
                            Quick Actions
                        </h6>
                        <div class="d-grid gap-2">
                            <a href="/admin/orders/enhanced_order_management.php" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-receipt me-2"></i>View Orders
                                <?php if ($stats['pending_orders'] > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $stats['pending_orders']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="/admin/products/enhanced_product_management.php" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-box-seam me-2"></i>Manage Inventory
                                <?php if ($stats['low_stock_products'] > 0): ?>
                                    <span class="badge bg-warning ms-2"><?php echo $stats['low_stock_products']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="/admin/orders/order_demand_list.php" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-star me-2"></i>On-Demand Requests
                                <?php if ($stats['on_demand_requests'] > 0): ?>
                                    <span class="badge bg-info ms-2"><?php echo $stats['on_demand_requests']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="/admin/notifications/notification_manager.php" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-bell me-2"></i>Notifications
                                <?php
                                $unread_notifications = countRowsWhere('notifications', 'is_read = 0');
                                if ($unread_notifications > 0) echo "<span class='badge bg-danger ms-2'>$unread_notifications</span>";
                                ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Activity & Alerts -->
    <div class="row g-4">
        <!-- Recent Orders Timeline -->
        <div class="col-xl-6">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-clock-history text-primary me-2"></i>
                                Recent Orders
                            </h5>
                            <p class="text-muted small mb-0">Latest customer orders and status updates</p>
                        </div>
                        <a href="/admin/orders/enhanced_order_management.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-right me-1"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="order-timeline" id="recent-orders-timeline">
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <div class="timeline-item mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-start">
                                    <div class="timeline-dot <?php
                                        echo match($order['order_status']) {
                                            'pending' => 'bg-warning',
                                            'processing' => 'bg-info',
                                            'shipped' => 'bg-primary',
                                            'delivered' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    ?> me-3"></div>
                                    <div class="flex-grow-1">
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
                                            <i class="bi bi-person me-1"></i><?php echo htmlspecialchars(($order['first_name'] . ' ' . $order['last_name']) ?: 'Walk-in Customer'); ?>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-box-seam me-1"></i><?php echo $order['item_count']; ?> items
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-cash me-1"></i>RWF <?php echo number_format($order['total_amount'], 0); ?>
                                        </div>
                                        <?php if ($order['special_instructions']): ?>
                                            <div class="text-warning small">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                <?php echo htmlspecialchars(substr($order['special_instructions'], 0, 50)); ?>...
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts & Pending Tasks -->
        <div class="col-xl-6">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">
                                <i class="bi bi-bell-fill text-warning me-2"></i>
                                Alerts & Tasks
                            </h5>
                            <p class="text-muted small mb-0">Important notifications and pending actions</p>
                        </div>
                        <span class="badge bg-danger" id="alerts-count">
                            <?php echo array_sum($pending_tasks); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Pending Tasks -->
                    <div class="pending-tasks mb-4">
                        <?php if ($pending_tasks['pending_orders'] > 0): ?>
                            <div class="alert alert-warning alert-sm mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong><?php echo $pending_tasks['pending_orders']; ?> Pending Orders</strong>
                                        <div class="small text-muted">Require immediate attention</div>
                                    </div>
                                    <a href="/admin/orders/enhanced_order_management.php?status=pending" class="btn btn-warning btn-sm">Review</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($pending_tasks['low_stock_items'] > 0): ?>
                            <div class="alert alert-danger alert-sm mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong><?php echo $pending_tasks['low_stock_items']; ?> Low Stock Items</strong>
                                        <div class="small text-muted">Restock required soon</div>
                                    </div>
                                    <a href="/admin/products/enhanced_product_management.php?stock=low" class="btn btn-danger btn-sm">Manage</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($pending_tasks['unpaid_orders'] > 0): ?>
                            <div class="alert alert-info alert-sm mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-cash me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong><?php echo $pending_tasks['unpaid_orders']; ?> Unpaid Orders</strong>
                                        <div class="small text-muted">Pending payment collection</div>
                                    </div>
                                    <a href="/admin/orders/enhanced_order_management.php?payment=unpaid" class="btn btn-info btn-sm">Collect</a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($pending_tasks['on_demand_requests'] > 0): ?>
                            <div class="alert alert-success alert-sm mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-star-fill me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong><?php echo $pending_tasks['on_demand_requests']; ?> On-Demand Requests</strong>
                                        <div class="small text-muted">Special parts requests</div>
                                    </div>
                                    <a href="/admin/orders/order_demand_list.php" class="btn btn-success btn-sm">Process</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Low Stock Alerts -->
                    <?php if ($low_stock_alerts->num_rows > 0): ?>
                        <div class="low-stock-alerts">
                            <h6 class="mb-3">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                Low Stock Alerts
                            </h6>
                            <?php while ($item = $low_stock_alerts->fetch_assoc()): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($item['brand_name']); ?></div>
                                    </div>
                                    <span class="badge bg-danger"><?php echo $item['stock']; ?> left</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.kpi-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.health-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.timeline-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}

.alert-sm {
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

#live-clock {
    font-family: 'Courier New', monospace;
    font-weight: bold;
}
</style>

<script>
// Initialize Chart.js
document.addEventListener('DOMContentLoaded', function() {
    // Order Trends Chart
    const ctx = document.getElementById('orderTrendsChart').getContext('2d');
    const orderTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($order_trends, 'date')); ?>,
            datasets: [{
                label: 'Orders',
                data: <?php echo json_encode(array_column($order_trends, 'count')); ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Live clock update
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('live-clock').textContent = timeString;
    }

    setInterval(updateClock, 1000);

    // Real-time dashboard updates
    let lastUpdateTimestamp = <?php echo time(); ?>;

    function updateDashboard() {
        fetch('api/dashboard_stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.timestamp > lastUpdateTimestamp) {
                    updateStats(data.stats);
                    updateRecentOrders(data.recent_orders);
                    updateLowStockAlerts(data.low_stock_alerts);
                    updateOrderTrends(data.order_trends);
                    updatePendingTasks(data.pending_tasks);

                    lastUpdateTimestamp = data.timestamp;

                    // Show update notification
                    showToast('Dashboard updated', 'info');
                }
            })
            .catch(error => {
                console.error('Error updating dashboard:', error);
            });
    }

    function updateStats(stats) {
        // Update KPI cards
        document.querySelector('[data-stat="total_orders"]').textContent = number_format(stats.total_orders);
        document.querySelector('[data-stat="monthly_revenue"]').textContent = 'RWF ' + number_format(stats.monthly_revenue);
        document.querySelector('[data-stat="total_products"]').textContent = number_format(stats.total_products);
        document.querySelector('[data-stat="total_customers"]').textContent = number_format(stats.total_customers);

        // Update progress bars and additional info
        updateProgressBars(stats);
    }

    function updateProgressBars(stats) {
        // Update delivered orders info
        const deliveredInfo = document.querySelector('.orders-card .text-success');
        if (deliveredInfo) {
            deliveredInfo.innerHTML = `<i class="bi bi-arrow-up me-1"></i>${stats.delivered_orders} Delivered`;
        }

        // Update pending orders info
        const pendingInfo = document.querySelector('.orders-card .text-warning');
        if (pendingInfo) {
            pendingInfo.innerHTML = `<i class="bi bi-clock me-1"></i>${stats.pending_orders} Pending`;
        }

        // Update inventory info
        const lowStockInfo = document.querySelector('.inventory-card .text-danger');
        if (lowStockInfo) {
            lowStockInfo.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${stats.low_stock_products} Low Stock`;
        }

        const outStockInfo = document.querySelector('.inventory-card .text-secondary');
        if (outStockInfo) {
            outStockInfo.innerHTML = `<i class="bi bi-x-circle me-1"></i>${stats.out_of_stock_products} Out`;
        }

        // Update customer metrics
        const brandsInfo = document.querySelector('.customers-card .text-primary');
        if (brandsInfo) {
            brandsInfo.innerHTML = `<i class="bi bi-tags me-1"></i>${stats.total_brands} Brands`;
        }

        const modelsInfo = document.querySelector('.customers-card .text-success');
        if (modelsInfo) {
            modelsInfo.innerHTML = `<i class="bi bi-car-front me-1"></i>${stats.total_models} Models`;
        }
    }

    function updateRecentOrders(orders) {
        const timelineContainer = document.getElementById('recent-orders-timeline');
        if (!timelineContainer) return;

        let html = '';
        orders.forEach(order => {
            const statusClass = getStatusClass(order.order_status);
            const statusBadge = getStatusBadge(order.order_status);

            html += `
                <div class="timeline-item mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-start">
                        <div class="timeline-dot ${statusClass} me-3"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <span class="fw-semibold">${order.order_number}</span>
                                    <span class="badge ${statusBadge} badge-sm ms-2">${ucfirst(order.order_status)}</span>
                                </div>
                                <small class="text-muted">${formatDate(order.created_at)}</small>
                            </div>
                            <div class="text-muted small mb-1">
                                <i class="bi bi-person me-1"></i>${order.customer_name}
                                <span class="mx-2">•</span>
                                <i class="bi bi-box-seam me-1"></i>${order.item_count} items
                                <span class="mx-2">•</span>
                                <i class="bi bi-cash me-1"></i>RWF ${number_format(order.total_amount, 0)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        timelineContainer.innerHTML = html;
    }

    function updateLowStockAlerts(alerts) {
        // Update alerts count
        const alertsCount = document.getElementById('alerts-count');
        if (alertsCount) {
            alertsCount.textContent = alerts.length;
        }

        // Update low stock alerts section
        const alertsContainer = document.querySelector('.low-stock-alerts');
        if (alertsContainer && alerts.length > 0) {
            let html = '<h6 class="mb-3"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Low Stock Alerts</h6>';
            alerts.forEach(alert => {
                html += `
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <div class="fw-semibold small">${alert.product_name}</div>
                            <div class="text-muted small">${alert.brand_name}</div>
                        </div>
                        <span class="badge bg-danger">${alert.stock} left</span>
                    </div>
                `;
            });
            alertsContainer.innerHTML = html;
        }
    }

    function updateOrderTrends(trends) {
        if (window.orderTrendsChart) {
            window.orderTrendsChart.data.labels = trends.map(t => t.date);
            window.orderTrendsChart.data.datasets[0].data = trends.map(t => t.count);
            window.orderTrendsChart.update();
        }
    }

    function updatePendingTasks(tasks) {
        const totalTasks = Object.values(tasks).reduce((a, b) => a + b, 0);

        // Update alerts count badge
        const alertsCount = document.getElementById('alerts-count');
        if (alertsCount) {
            alertsCount.textContent = totalTasks;
        }

        // Update pending tasks alerts
        updatePendingTaskAlert('.alert-warning', tasks.pending_orders, 'pending order(s)');
        updatePendingTaskAlert('.alert-danger', tasks.low_stock_items, 'low stock item(s)');
        updatePendingTaskAlert('.alert-info', tasks.unpaid_orders, 'unpaid order(s)');
        updatePendingTaskAlert('.alert-success', tasks.on_demand_requests, 'on-demand request(s)');
    }

    function updatePendingTaskAlert(selector, count, label) {
        const alert = document.querySelector(selector);
        if (alert) {
            if (count > 0) {
                alert.style.display = 'block';
                const strong = alert.querySelector('strong');
                if (strong) {
                    strong.textContent = `${count} ${label.replace('(s)', count > 1 ? 's' : '')}`;
                }
            } else {
                alert.style.display = 'none';
            }
        }
    }

    // Helper functions
    function getStatusClass(status) {
        const classes = {
            'pending': 'bg-warning',
            'processing': 'bg-info',
            'shipped': 'bg-primary',
            'delivered': 'bg-success',
            'cancelled': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    function getStatusBadge(status) {
        const badges = {
            'pending': 'bg-warning',
            'processing': 'bg-info',
            'shipped': 'bg-primary',
            'delivered': 'bg-success',
            'cancelled': 'bg-danger'
        };
        return badges[status] || 'bg-secondary';
    }

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function number_format(number, decimals = 0) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    }

    // Start real-time updates
    const updateInterval = localStorage.getItem('refresh-rate') || 30000;
    setInterval(updateDashboard, parseInt(updateInterval));

    // Initial update after 5 seconds
    setTimeout(updateDashboard, 5000);
});

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    const alertsContainer = document.getElementById('real-time-alerts');
    alertsContainer.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-remove after 10 seconds
    setTimeout(() => {
        const alert = alertsContainer.querySelector('.alert:last-child');
        if (alert) {
            alert.remove();
        }
    }, 10000);
}

function changeChartPeriod(period) {
    // TODO: Implement chart period change
    console.log('Changing chart period to:', period);
}
</script>

<?php include 'footer.php'; ?>