<?php
// API endpoint for filtered models
include '../../includes/config.php';

// Get filter parameters
$brand_filter = $_GET['brand'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';
$year_filter = $_GET['year'] ?? 'all';

// Build WHERE conditions
$where_conditions = [];
if ($brand_filter !== 'all') {
    $where_conditions[] = "vm.brand_id = " . (int)$brand_filter;
}
if ($status_filter !== 'all') {
    $where_conditions[] = "vm.is_active = " . ($status_filter === 'active' ? 1 : 0);
}
if ($year_filter !== 'all') {
    if ($year_filter === 'current') {
        $current_year = date('Y');
        $where_conditions[] = "((vm.year_from <= $current_year AND vm.year_to >= $current_year) OR vm.year_to IS NULL)";
    } else {
        $year = (int)$year_filter;
        $where_conditions[] = "(vm.year_from <= $year AND (vm.year_to >= $year OR vm.year_to IS NULL))";
    }
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get filtered models
$query = "
    SELECT vm.*,
           vb.brand_name,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_models_enhanced vm
    LEFT JOIN vehicle_brands_enhanced vb ON vm.brand_id = vb.id
    LEFT JOIN products_enhanced p ON vm.id = p.model_id
    $where_clause
    GROUP BY vm.id
    ORDER BY vb.brand_name, vm.model_name
";

$result = $conn->query($query);
$models = [];

if ($result) {
    while ($model = $result->fetch_assoc()) {
        $models[] = $model;
    }
}

// Get statistics for filtered results
$stats_query = "
    SELECT
        COUNT(*) as total_models,
        COUNT(CASE WHEN vm.is_active = 1 THEN 1 END) as active_models,
        COUNT(DISTINCT vm.brand_id) as total_brands,
        COUNT(DISTINCT p.id) as total_products,
        COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_products,
        COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_models_enhanced vm
    LEFT JOIN products_enhanced p ON vm.id = p.model_id
    $where_clause
";

$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : null;

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'models' => $models,
    'stats' => $stats,
    'filters' => [
        'brand' => $brand_filter,
        'status' => $status_filter,
        'year' => $year_filter
    ]
]);
?>