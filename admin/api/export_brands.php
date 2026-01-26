<?php
// Export Brands API for SPARE XPRESS LTD Admin Panel
include '../../includes/config.php';
include '../includes/auth.php';

// Set headers for file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="brands_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, [
    'ID',
    'Brand Name',
    'Slug',
    'Description',
    'Country',
    'Founded Year',
    'Manufacturer Details',
    'Website',
    'Contact Email',
    'Contact Phone',
    'SEO Title',
    'SEO Description',
    'Logo Image',
    'Brand Image',
    'Is Active',
    'Created At',
    'Updated At',
    'Model Count',
    'Product Count',
    'Total Sales'
]);

// Get brands with analytics
$query = "
    SELECT vb.*,
           COUNT(DISTINCT vm.id) as model_count,
           COUNT(DISTINCT p.id) as product_count,
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
        $brand['description'],
        $brand['country'],
        $brand['founded_year'],
        $brand['manufacturer_details'],
        $brand['website'],
        $brand['contact_email'],
        $brand['contact_phone'],
        $brand['seo_title'],
        $brand['seo_description'],
        $brand['logo_image'],
        $brand['brand_image'],
        $brand['is_active'] ? 'Yes' : 'No',
        $brand['created_at'],
        $brand['updated_at'],
        $brand['model_count'],
        $brand['product_count'],
        $brand['total_sales']
    ]);
}

fclose($output);
exit;
?>