<?php
include_once '../../includes/session_init.php';
spx_session_start();
include_once '../includes/config.php';
include_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false]);
    exit;
}

$period = $_GET['period'] ?? '7d';
$days = match($period) { '30d' => 30, '90d' => 90, default => 7 };

// Stats
$stats = [
    'total_orders'        => (int)countRows('orders_enhanced'),
    'pending_orders'      => (int)countRowsWhere('orders_enhanced', "order_status = 'pending'"),
    'processing_orders'   => (int)countRowsWhere('orders_enhanced', "order_status = 'processing'"),
    'shipped_orders'      => (int)countRowsWhere('orders_enhanced', "order_status = 'shipped'"),
    'delivered_orders'    => (int)countRowsWhere('orders_enhanced', "order_status = 'delivered'"),
    'cancelled_orders'    => (int)countRowsWhere('orders_enhanced', "order_status = 'cancelled'"),
    'total_products'      => (int)countRows('products_enhanced'),
    'low_stock_products'  => (int)countRowsWhere('products_enhanced', 'stock_quantity <= 5 AND stock_quantity > 0'),
    'out_of_stock_products' => (int)countRowsWhere('products_enhanced', 'stock_quantity = 0'),
    'total_brands'        => (int)countRows('vehicle_brands_enhanced'),
    'total_models'        => (int)countRows('vehicle_models_enhanced'),
    'total_customers'     => (int)countRows('customers_enhanced'),
    'on_demand_requests'  => (int)countRowsWhere('on_demand_requests_enhanced', "request_status IN ('pending','sourcing','quoted')"),
    'unpaid_orders'       => (int)countRowsWhere('orders_enhanced', "payment_status = 'unpaid'"),
    'recent_orders_count' => (int)countRowsWhere('orders_enhanced', "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"),
];

// Revenue current period
$rev = $conn->query("SELECT COALESCE(SUM(total_amount),0) as rev, COUNT(*) as cnt FROM orders_enhanced WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND payment_status='paid'")->fetch_assoc();
$stats['monthly_revenue'] = (float)$rev['rev'];
$stats['monthly_orders']  = (int)$rev['cnt'];

// Revenue previous period (for trend)
$prev_rev = $conn->query("SELECT COALESCE(SUM(total_amount),0) as rev FROM orders_enhanced WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY) AND payment_status='paid'")->fetch_assoc();
$stats['prev_monthly_revenue'] = (float)$prev_rev['rev'];

// Previous period orders count (for trend)
$prev_orders = $conn->query("SELECT COUNT(*) as cnt FROM orders_enhanced WHERE created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc();
$stats['prev_total_orders'] = (int)$prev_orders['cnt'];

// Previous period customers (for trend)
$prev_customers = $conn->query("SELECT COUNT(*) as cnt FROM customers_enhanced WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc();
$stats['prev_total_customers'] = (int)($prev_customers['cnt'] ?? 0);

// Order trends
$order_trends = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $date  = date('Y-m-d', strtotime("-$i days"));
    $label = $days <= 7 ? date('M d', strtotime($date)) : date('M d', strtotime($date));
    $count = (int)countRowsWhere('orders_enhanced', "DATE(created_at) = '$date'");
    $rev_day = $conn->query("SELECT COALESCE(SUM(total_amount),0) as r FROM orders_enhanced WHERE DATE(created_at)='$date' AND payment_status='paid'")->fetch_assoc();
    $order_trends[] = ['date' => $label, 'count' => $count, 'revenue' => (float)$rev_day['r']];
}

// Recent orders
$recent_orders = [];
$ro = $conn->query("SELECT o.order_number, o.order_status, o.total_amount, o.created_at, o.special_instructions, CONCAT(COALESCE(c.first_name,''),' ',COALESCE(c.last_name,'')) as customer_name, COUNT(oi.id) as item_count FROM orders_enhanced o LEFT JOIN customers_enhanced c ON o.customer_id=c.id LEFT JOIN order_items_enhanced oi ON o.id=oi.order_id GROUP BY o.id ORDER BY o.created_at DESC LIMIT 10");
if ($ro) while ($r = $ro->fetch_assoc()) $recent_orders[] = $r;

// Low stock alerts
$low_stock_alerts = [];
$ls = $conn->query("SELECT p.product_name, p.stock_quantity as stock, b.brand_name FROM products_enhanced p JOIN vehicle_brands_enhanced b ON p.brand_id=b.id WHERE p.stock_quantity<=5 AND p.stock_quantity>0 ORDER BY p.stock_quantity ASC LIMIT 5");
if ($ls) while ($r = $ls->fetch_assoc()) $low_stock_alerts[] = $r;

// Real disk usage
$upload_dir = __DIR__ . '/../uploads/';
$disk_total = disk_total_space($upload_dir ?: __DIR__);
$disk_free  = disk_free_space($upload_dir ?: __DIR__);
$disk_used_pct = $disk_total > 0 ? round((($disk_total - $disk_free) / $disk_total) * 100) : 0;

echo json_encode([
    'success'          => true,
    'timestamp'        => time(),
    'stats'            => $stats,
    'order_trends'     => $order_trends,
    'recent_orders'    => $recent_orders,
    'low_stock_alerts' => $low_stock_alerts,
    'disk_used_pct'    => $disk_used_pct,
    'pending_tasks'    => [
        'pending_orders'    => $stats['pending_orders'],
        'low_stock_items'   => $stats['low_stock_products'],
        'unpaid_orders'     => $stats['unpaid_orders'],
        'on_demand_requests'=> $stats['on_demand_requests'],
    ],
]);
