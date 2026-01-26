<?php
// API endpoint for filtered products
include '../../includes/config.php';

// Get filter parameters
$brand_filter = $_GET['brand'] ?? 'all';
$model_filter = $_GET['model'] ?? 'all';
$category_filter = $_GET['category'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';
$stock_filter = $_GET['stock'] ?? 'all';

// Build WHERE conditions
$where_conditions = [];
if ($brand_filter !== 'all') {
    $where_conditions[] = "p.brand_id = " . (int)$brand_filter;
}
if ($model_filter !== 'all') {
    $where_conditions[] = "p.model_id = " . (int)$model_filter;
}
if ($category_filter !== 'all') {
    $where_conditions[] = "p.category_id = " . (int)$category_filter;
}
if ($status_filter !== 'all') {
    $where_conditions[] = "p.is_active = " . ($status_filter === 'active' ? 1 : 0);
}
if ($stock_filter !== 'all') {
    switch ($stock_filter) {
        case 'in_stock':
            $where_conditions[] = "p.stock_quantity > p.low_stock_threshold";
            break;
        case 'low_stock':
            $where_conditions[] = "p.stock_quantity <= p.low_stock_threshold AND p.stock_quantity > 0";
            break;
        case 'out_of_stock':
            $where_conditions[] = "p.stock_quantity = 0";
            break;
    }
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get filtered products
$query = "
    SELECT p.*,
           b.brand_name,
           m.model_name,
           c.category_name,
           COALESCE(p.sales_count, 0) as sales_count,
           COALESCE(p.view_count, 0) as view_count
    FROM products_enhanced p
    LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
    LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
    LEFT JOIN categories_enhanced c ON p.category_id = c.id
    $where_clause
    ORDER BY p.created_at DESC
";

$result = $conn->query($query);
$products = [];

if ($result) {
    while ($product = $result->fetch_assoc()) {
        $products[] = $product;
    }
}

// Get statistics for filtered results
$stats_query = "
    SELECT
        COUNT(*) as total_products,
        COUNT(CASE WHEN p.is_active = 1 THEN 1 END) as active_products,
        COUNT(CASE WHEN p.stock_quantity <= p.low_stock_threshold AND p.stock_quantity > 0 THEN 1 END) as low_stock_items,
        COUNT(CASE WHEN p.stock_quantity = 0 THEN 1 END) as out_of_stock_items,
        COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales,
        COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.view_count ELSE 0 END), 0) as total_views,
        COUNT(DISTINCT p.brand_id) as total_brands,
        COUNT(DISTINCT p.category_id) as total_categories
    FROM products_enhanced p
    $where_clause
";

$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : null;

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'products' => $products,
    'stats' => $stats,
    'filters' => [
        'brand' => $brand_filter,
        'model' => $model_filter,
        'category' => $category_filter,
        'status' => $status_filter,
        'stock' => $stock_filter
    ]
]);
?>