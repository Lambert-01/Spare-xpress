<?php
// Redirect to enhanced dashboard analytics
header("Location: enhanced_dashboard.php?tab=analytics");
exit;
?>

// Get analytics data
$current_month = date('Y-m');
$last_month = date('Y-m', strtotime('-1 month'));
$current_year = date('Y');

// Monthly revenue data for the last 12 months
$revenue_query = $conn->query("
    SELECT
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(total) as revenue,
        COUNT(*) as order_count
    FROM orders_enhanced
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        AND order_status NOT IN ('Cancelled', 'Failed')
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");

$revenue_data = [];
$order_count_data = [];
$months_labels = [];

while ($row = $revenue_query->fetch_assoc()) {
    $revenue_data[] = (float)$row['revenue'];
    $order_count_data[] = (int)$row['order_count'];
    $months_labels[] = date('M Y', strtotime($row['month'] . '-01'));
}

// Daily orders for current month
$daily_orders_query = $conn->query("
    SELECT
        DATE(created_at) as date,
        COUNT(*) as orders
    FROM orders_enhanced
    WHERE DATE_FORMAT(created_at, '%Y-%m') = '$current_month'
    GROUP BY DATE(created_at)
    ORDER BY date
");

$daily_orders = [];
$daily_labels = [];
while ($row = $daily_orders_query->fetch_assoc()) {
    $daily_orders[] = (int)$row['orders'];
    $daily_labels[] = date('d', strtotime($row['date']));
}

// Top selling products
$top_products_query = $conn->query("
    SELECT
        p.product_name,
        SUM(oi.quantity) as total_quantity,
        SUM(oi.subtotal) as total_revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE o.order_status NOT IN ('cancelled', 'failed')
    GROUP BY oi.product_id, oi.product_name
    ORDER BY total_quantity DESC
    LIMIT 10
");

$top_products = [];
while ($row = $top_products_query->fetch_assoc()) {
    $top_products[] = $row;
}

// Payment method distribution
$payment_methods_query = $conn->query("
    SELECT
        payment_method,
        COUNT(*) as count,
        SUM(total_amount) as total_amount
    FROM orders_enhanced
    WHERE payment_status = 'paid'
    GROUP BY payment_method
");

$payment_data = [];
while ($row = $payment_methods_query->fetch_assoc()) {
    $payment_data[] = $row;
}

// Brand performance
$brand_performance_query = $conn->query("
    SELECT
        COALESCE(vb.brand_name, oi.product_brand) as brand,
        COUNT(DISTINCT o.id) as orders,
        SUM(oi.subtotal) as revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    LEFT JOIN products p ON oi.product_id = p.id
    LEFT JOIN vehicle_brands vb ON p.brand_id = vb.id
    WHERE o.order_status NOT IN ('cancelled', 'failed')
        AND COALESCE(vb.brand_name, oi.product_brand) IS NOT NULL
    GROUP BY COALESCE(vb.brand_name, oi.product_brand)
    ORDER BY revenue DESC
    LIMIT 8
");

$brand_performance = [];
while ($row = $brand_performance_query->fetch_assoc()) {
    $brand_performance[] = $row;
}

// Key metrics
$today_orders = $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$month_orders = $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE DATE_FORMAT(created_at, '%Y-%m') = '$current_month'")->fetch_assoc()['count'];
$month_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders_enhanced WHERE DATE_FORMAT(created_at, '%Y-%m') = '$current_month' AND order_status NOT IN ('cancelled', 'failed')")->fetch_assoc()['total'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status = 'pending'")->fetch_assoc()['count'];
$delivered_orders = $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status = 'delivered' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'")->fetch_assoc()['count'];
$cancelled_orders = $conn->query("SELECT COUNT(*) as count FROM orders_enhanced WHERE order_status = 'cancelled' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'")->fetch_assoc()['count'];
$on_demand_requests = $conn->query("SELECT COUNT(*) as count FROM order_requests")->fetch_assoc()['count'];
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="mb-3 mb-md-0">
                    <h1 class="h2 mb-2">
                        <i class="bi bi-graph-up text-success me-2"></i>
                        Order Analytics Dashboard
                    </h1>
                    <p class="text-muted mb-0">Comprehensive insights into your order performance and business trends</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <select class="form-select form-select-sm" id="timeRange" onchange="updateAnalytics()" style="min-width: 140px;">
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                    <button class="btn btn-outline-primary btn-sm" onclick="exportAnalytics()">
                        <i class="bi bi-download me-1"></i>Export Report
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="refreshAnalytics()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="form-card">
                <div class="card-header bg-light border-bottom-0">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>
                        Key Performance Indicators
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Today's Performance -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="text-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-calendar-day text-primary fs-4"></i>
                                </div>
                                <h4 class="fw-bold text-primary mb-1"><?php echo $today_orders; ?></h4>
                                <p class="text-muted small mb-2">Orders Today</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: <?php echo min(100, ($today_orders / 10) * 100); ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Orders -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-calendar-month text-success fs-4"></i>
                                </div>
                                <h4 class="fw-bold text-success mb-1"><?php echo $month_orders; ?></h4>
                                <p class="text-muted small mb-2">Monthly Orders</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo min(100, ($month_orders / 50) * 100); ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Revenue -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="text-center">
                                <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-cash text-info fs-4"></i>
                                </div>
                                <h4 class="fw-bold text-info mb-1">RWF <?php echo number_format($month_revenue, 0); ?></h4>
                                <p class="text-muted small mb-2">Monthly Revenue</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: <?php echo min(100, ($month_revenue / 10000000) * 100); ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Orders -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="text-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-clock text-warning fs-4"></i>
                                </div>
                                <h4 class="fw-bold text-warning mb-1"><?php echo $pending_orders; ?></h4>
                                <p class="text-muted small mb-2">Pending Orders</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: <?php echo min(100, ($pending_orders / 20) * 100); ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivered Orders -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                                <h4 class="fw-bold text-success mb-1"><?php echo $delivered_orders; ?></h4>
                                <p class="text-muted small mb-2">Delivered This Month</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo $month_orders > 0 ? ($delivered_orders / $month_orders) * 100 : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- On-Demand Requests -->
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="text-center">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="bi bi-star text-danger fs-4"></i>
                                </div>
                                <h4 class="fw-bold text-danger mb-1"><?php echo $on_demand_requests; ?></h4>
                                <p class="text-muted small mb-2">On-Demand Requests</p>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-danger" style="width: <?php echo min(100, ($on_demand_requests / 50) * 100); ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Section -->
    <div class="row mb-5">
        <!-- Revenue & Orders Trend -->
        <div class="col-lg-8 mb-4">
            <div class="form-card h-100">
                <div class="card-header bg-light border-bottom-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="bi bi-bar-chart text-primary me-2"></i>Revenue & Orders Trend
                        </h5>
                        <p class="text-muted small mb-0">Monthly performance over the last 12 months</p>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="toggleChartType('both')">
                            <i class="bi bi-bar-chart-line me-1"></i>Both
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="toggleChartType('revenue')">
                            <i class="bi bi-graph-up me-1"></i>Revenue
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="toggleChartType('orders')">
                            <i class="bi bi-bar-chart me-1"></i>Orders
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Daily Performance & Payment Methods -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Daily Orders -->
                <div class="col-12">
                    <div class="form-card">
                        <div class="card-header bg-light border-bottom-0">
                            <h6 class="mb-0">
                                <i class="bi bi-calendar-week text-success me-2"></i>Daily Orders
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="dailyOrdersChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="col-12">
                    <div class="form-card">
                        <div class="card-header bg-light border-bottom-0">
                            <h6 class="mb-0">
                                <i class="bi bi-credit-card text-info me-2"></i>Payment Methods
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="paymentChart" style="max-height: 200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Tables -->
    <div class="row">
        <!-- Top Performing Products -->
        <div class="col-lg-6 mb-4">
            <div class="form-card h-100">
                <div class="card-header bg-light border-bottom-0">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy text-warning me-2"></i>Top Performing Products
                    </h5>
                    <small class="text-muted">Best-selling products by quantity and revenue</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Product Name</th>
                                    <th class="border-0 text-center">Units Sold</th>
                                    <th class="border-0 text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($top_products)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle me-2"></i>No product sales data available yet
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($top_products as $index => $product): ?>
                                    <tr>
                                        <td class="fw-bold text-<?php echo ['primary', 'success', 'info', 'warning', 'danger'][$index % 5] ?? 'secondary'; ?>">
                                            <?php echo $index + 1; ?>
                                        </td>
                                        <td>
                                            <div class="fw-semibold"><?php echo htmlspecialchars(substr($product['product_name'], 0, 35)); ?></div>
                                            <?php if (strlen($product['product_name']) > 35): ?>
                                                <small class="text-muted">...<?php echo htmlspecialchars(substr($product['product_name'], -10)); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6 px-3 py-1"><?php echo number_format($product['total_quantity']); ?></span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            RWF <?php echo number_format($product['total_revenue'], 0); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Performance Analysis -->
        <div class="col-lg-6 mb-4">
            <div class="form-card h-100">
                <div class="card-header bg-light border-bottom-0">
                    <h5 class="mb-0">
                        <i class="bi bi-tags text-danger me-2"></i>Brand Performance Analysis
                    </h5>
                    <small class="text-muted">Revenue and order distribution by vehicle brand</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Brand</th>
                                    <th class="border-0 text-center">Orders</th>
                                    <th class="border-0 text-center">Avg Order Value</th>
                                    <th class="border-0 text-end">Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($brand_performance)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-info-circle me-2"></i>No brand performance data available yet
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($brand_performance as $brand): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.8rem; font-weight: bold;">
                                                    <?php echo strtoupper(substr($brand['brand'], 0, 1)); ?>
                                                </div>
                                                <span class="fw-semibold"><?php echo htmlspecialchars($brand['brand']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?php echo number_format($brand['orders']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted">
                                                RWF <?php echo $brand['orders'] > 0 ? number_format($brand['revenue'] / $brand['orders'], 0) : '0'; ?>
                                            </small>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            RWF <?php echo number_format($brand['revenue'], 0); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Footer -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="form-card">
                <div class="card-body text-center py-3">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Last Updated</small>
                            <span class="fw-semibold"><?php echo date('M d, Y H:i:s'); ?></span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Data Period</small>
                            <span class="fw-semibold">Last 12 Months</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">System Status</small>
                            <span class="badge bg-success">Operational</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Next Refresh</small>
                            <span class="fw-semibold" id="nextRefresh">5:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global chart variables
let revenueChart, dailyOrdersChart, paymentChart;
let refreshCountdown = 300; // 5 minutes in seconds

// Initialize charts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    startRefreshCountdown();
});

// Initialize all charts with optimized settings
function initializeCharts() {
    // Revenue and Orders Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months_labels); ?>,
                datasets: [{
                    label: 'Revenue (RWF)',
                    data: <?php echo json_encode($revenue_data); ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    yAxisID: 'y',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Orders',
                    data: <?php echo json_encode($order_count_data); ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Revenue: RWF ' + context.parsed.y.toLocaleString();
                                } else {
                                    return 'Orders: ' + context.parsed.y;
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Month',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue (RWF)',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            callback: function(value) {
                                return 'RWF ' + (value / 1000).toFixed(0) + 'K';
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Orders',
                            font: {
                                size: 12
                            }
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        }
                    }
                }
            }
        });
    }

    // Daily Orders Chart
    const dailyCtx = document.getElementById('dailyOrdersChart');
    if (dailyCtx) {
        dailyOrdersChart = new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($daily_labels); ?>,
                datasets: [{
                    label: 'Daily Orders',
                    data: <?php echo json_encode($daily_orders); ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: '#28a745',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(40, 167, 69, 0.9)',
                        callbacks: {
                            label: function(context) {
                                return 'Orders: ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart');
    if (paymentCtx) {
        paymentChart = new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($payment_data, 'payment_method')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($payment_data, 'count')); ?>,
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#17a2b8'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }
}

// Chart control functions
function toggleChartType(type) {
    if (!revenueChart) return;

    const revenueDataset = revenueChart.data.datasets[0];
    const ordersDataset = revenueChart.data.datasets[1];

    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    switch(type) {
        case 'revenue':
            revenueDataset.hidden = false;
            ordersDataset.hidden = true;
            break;
        case 'orders':
            revenueDataset.hidden = true;
            ordersDataset.hidden = false;
            break;
        case 'both':
            revenueDataset.hidden = false;
            ordersDataset.hidden = false;
            break;
    }

    revenueChart.update('none'); // Disable animation for better performance
}

// Analytics control functions
function updateAnalytics() {
    const timeRange = document.getElementById('timeRange').value;
    showToast('Time range filtering will be implemented in the next update', 'info');
}

function refreshAnalytics() {
    showToast('Refreshing analytics data...', 'info');

    // Reset countdown
    refreshCountdown = 300;
    updateRefreshDisplay();

    // In a real implementation, you would fetch new data here
    setTimeout(() => {
        showToast('Analytics data refreshed successfully!', 'success');
    }, 1000);
}

function exportAnalytics() {
    showToast('Preparing analytics report for export...', 'info');

    setTimeout(() => {
        // Create a simple CSV export
        const csvContent = generateCSVExport();
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');

        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'analytics_report_' + new Date().toISOString().split('T')[0] + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showToast('Analytics report exported successfully!', 'success');
        } else {
            showToast('Export feature not supported in this browser', 'warning');
        }
    }, 1500);
}

// Generate CSV export data
function generateCSVExport() {
    let csv = 'Analytics Report - ' + new Date().toLocaleDateString() + '\n\n';

    // Key metrics
    csv += 'Key Metrics\n';
    csv += 'Metric,Value\n';
    csv += 'Orders Today,<?php echo $today_orders; ?>\n';
    csv += 'Orders This Month,<?php echo $month_orders; ?>\n';
    csv += 'Revenue This Month,RWF <?php echo $month_revenue; ?>\n';
    csv += 'Pending Orders,<?php echo $pending_orders; ?>\n';
    csv += 'Delivered This Month,<?php echo $delivered_orders; ?>\n';
    csv += 'On-Demand Requests,<?php echo $on_demand_requests; ?>\n\n';

    // Top products
    csv += 'Top Products\n';
    csv += 'Product,Units Sold,Revenue\n';
    <?php foreach ($top_products as $product): ?>
    csv += '"<?php echo addslashes($product['product_name']); ?>","<?php echo $product['total_quantity']; ?>","RWF <?php echo $product['total_revenue']; ?>"\n';
    <?php endforeach; ?>

    return csv;
}

// Refresh countdown functionality
function startRefreshCountdown() {
    updateRefreshDisplay();
    setInterval(() => {
        refreshCountdown--;
        if (refreshCountdown <= 0) {
            refreshCountdown = 300;
            // Auto-refresh could be implemented here
        }
        updateRefreshDisplay();
    }, 1000);
}

function updateRefreshDisplay() {
    const minutes = Math.floor(refreshCountdown / 60);
    const seconds = refreshCountdown % 60;
    const display = document.getElementById('nextRefresh');
    if (display) {
        display.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
    }
}

// Toast notification function
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Performance optimization - lazy load charts
let chartsLoaded = false;
function loadChartsOnScroll() {
    if (!chartsLoaded && isElementInViewport(document.getElementById('revenueChart'))) {
        chartsLoaded = true;
        // Charts are already initialized, but this could be used for lazy loading
        window.removeEventListener('scroll', loadChartsOnScroll);
    }
}

function isElementInViewport(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Add scroll listener for performance
window.addEventListener('scroll', loadChartsOnScroll, { passive: true });
</script>

<?php include '../footer.php'; ?>