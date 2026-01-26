<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include '../includes/config.php';

// Get filter parameters
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';
$country = $_GET['country'] ?? 'all';

// Build the query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($status !== 'all') {
    $where_conditions[] = "vb.is_active = ?";
    $params[] = $status === 'active' ? 1 : 0;
    $param_types .= 'i';
}

if ($country !== 'all') {
    $where_conditions[] = "vb.country = ?";
    $params[] = $country;
    $param_types .= 's';
}

if (!empty($search)) {
    $where_conditions[] = "(vb.brand_name LIKE ? OR vb.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ss';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Main query to get filtered brands
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

try {
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $brands = [];
    while ($brand = $result->fetch_assoc()) {
        $brands[] = $brand;
    }
    
    // Get statistics for filtered results
    $stats_query = "
        SELECT 
            COUNT(DISTINCT vb.id) as total_brands,
            COUNT(DISTINCT vm.id) as total_models,
            COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_products,
            COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
        FROM vehicle_brands_enhanced vb
        LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
        LEFT JOIN products_enhanced p ON vb.id = p.brand_id
        $where_clause
    ";
    
    $stats_stmt = $conn->prepare($stats_query);
    if (!empty($params)) {
        $stats_stmt->bind_param($param_types, ...$params);
    }
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result();
    $stats = $stats_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'brands' => $brands,
        'stats' => $stats,
        'total' => count($brands)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>