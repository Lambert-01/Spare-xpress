<?php
/**
 * SPARE XPRESS LTD - CSV Export (Alternative format)
 * Exports all products to CSV format for easy Excel import
 */

include_once 'includes/config.php';

// Set headers for CSV download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="SPARE_XPRESS_Inventory_' . date('Y-m-d_H-i-s') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";

try {
    // Query to get all products
    $sql = "SELECT 
                p.id,
                p.product_name,
                b.brand_name,
                m.model_name,
                c.category_name,
                p.sku,
                p.`condition`,
                p.price,
                p.sale_price,
                p.stock_quantity,
                CASE
                    WHEN p.stock_quantity > 5 THEN 'In Stock'
                    WHEN p.stock_quantity > 0 THEN 'Low Stock'
                    ELSE 'Out of Stock'
                END as stock_status,
                p.is_featured,
                p.short_description,
                p.description,
                p.created_at
            FROM products_enhanced p
            LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
            LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
            LEFT JOIN categories_enhanced c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY b.brand_name, m.model_name, p.product_name";

    $result = $conn->query($sql);

    if (!$result) {
        die("Database error: " . $conn->error);
    }

    // Open output stream
    $output = fopen('php://output', 'w');

    // Write header row
    fputcsv($output, [
        'ID',
        'Product Name',
        'Brand',
        'Model',
        'Category',
        'SKU',
        'Condition',
        'Price (RWF)',
        'Sale Price (RWF)',
        'Stock Quantity',
        'Stock Status',
        'Featured',
        'Short Description',
        'Description',
        'Date Added'
    ]);

    // Write data rows
    while ($row = $result->fetch_assoc()) {
        // Clean descriptions
        $description = strip_tags($row['description'] ?? '');
        $description = str_replace(["\r\n", "\r", "\n"], ' ', $description);
        
        $short_description = strip_tags($row['short_description'] ?? '');
        $short_description = str_replace(["\r\n", "\r", "\n"], ' ', $short_description);
        
        fputcsv($output, [
            $row['id'],
            $row['product_name'],
            $row['brand_name'] ?? 'N/A',
            $row['model_name'] ?? 'N/A',
            $row['category_name'] ?? 'N/A',
            $row['sku'] ?? 'N/A',
            ucfirst($row['condition'] ?? 'new'),
            number_format($row['price'], 2, '.', ''),
            $row['sale_price'] ? number_format($row['sale_price'], 2, '.', '') : 'N/A',
            $row['stock_quantity'],
            $row['stock_status'],
            $row['is_featured'] ? 'Yes' : 'No',
            $short_description,
            $description,
            date('Y-m-d H:i:s', strtotime($row['created_at']))
        ]);
    }

    fclose($output);
    $conn->close();

} catch (Exception $e) {
    die("Error generating CSV: " . $e->getMessage());
}
?>
