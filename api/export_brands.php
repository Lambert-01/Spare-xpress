<?php
include '../includes/config.php';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="brands_export_' . date('Y-m-d_H-i-s') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'ID',
    'Brand Name',
    'Slug',
    'Country',
    'Founded Year',
    'Description',
    'Website',
    'Contact Email',
    'Contact Phone',
    'Models Count',
    'Products Count',
    'Active Products',
    'Total Sales',
    'Status',
    'Created At'
]);

// Get all brands with analytics
$query = "
    SELECT vb.*,
           COUNT(DISTINCT vm.id) as model_count,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_brands_enhanced vb
    LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
    LEFT JOIN products_enhanced p ON vb.id = p.brand_id
    GROUP BY vb.id
    ORDER BY vb.brand_name
";

$result = $conn->query($query);

while ($brand = $result->fetch_assoc()) {
    fputcsv($output, [
        $brand['id'],
        $brand['brand_name'],
        $brand['slug'],
        $brand['country'] ?? '',
        $brand['founded_year'] ?? '',
        $brand['description'] ?? '',
        $brand['website'] ?? '',
        $brand['contact_email'] ?? '',
        $brand['contact_phone'] ?? '',
        $brand['model_count'],
        $brand['product_count'],
        $brand['active_product_count'],
        $brand['total_sales'],
        $brand['is_active'] ? 'Active' : 'Inactive',
        $brand['created_at'] ?? ''
    ]);
}

fclose($output);
exit();
?>