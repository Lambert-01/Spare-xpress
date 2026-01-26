<?php
// API endpoint for filtered brands
include '../../includes/config.php';

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$country_filter = $_GET['country'] ?? 'all';

// Build WHERE conditions
$where_conditions = [];
if ($status_filter !== 'all') {
    $where_conditions[] = "vb.is_active = " . ($status_filter === 'active' ? 1 : 0);
}
if ($country_filter !== 'all') {
    $where_conditions[] = "vb.country = '" . $conn->real_escape_string($country_filter) . "'";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get filtered brands
$query = "
    SELECT vb.*,
           COUNT(DISTINCT vm.id) as model_count,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_brands_enhanced vb
    LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
    LEFT JOIN products_enhanced p ON vb.id = p.brand_id
    $where_clause
    GROUP BY vb.id
    ORDER BY vb.brand_name
";

$result = $conn->query($query);
$brands = [];

if ($result) {
    while ($brand = $result->fetch_assoc()) {
        $brands[] = $brand;
    }
}

// Get statistics for filtered results
$stats_query = "
    SELECT
        COUNT(*) as total_brands,
        COUNT(CASE WHEN vb.is_active = 1 THEN 1 END) as active_brands,
        COUNT(DISTINCT vm.id) as total_models,
        COUNT(DISTINCT p.id) as total_products,
        COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_products,
        COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_brands_enhanced vb
    LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
    LEFT JOIN products_enhanced p ON vb.id = p.brand_id
    $where_clause
";

$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : null;

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'brands' => $brands,
    'stats' => $stats,
    'filters' => [
        'status' => $status_filter,
        'country' => $country_filter
    ]
]);
?>