<?php
/**
 * SPARE XPRESS LTD - Inventory Excel Export
 * Exports all products with brands, models, categories, and prices to Excel
 */

session_start();
include_once '../includes/config.php';

// Check if user is admin (optional - remove if you want public access)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="SPARE_XPRESS_Inventory_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Query to get all products with related information
    $sql = "SELECT 
                p.id,
                p.product_name,
                p.description,
                p.sku,
                b.brand_name,
                m.model_name,
                c.category_name,
                p.regular_price,
                p.sale_price,
                p.stock_quantity,
                p.year_from,
                p.year_to,
                p.part_number,
                p.is_featured,
                p.created_at,
                CASE
                    WHEN p.stock_quantity > 5 THEN 'In Stock'
                    WHEN p.stock_quantity > 0 THEN 'Low Stock'
                    ELSE 'Out of Stock'
                END as stock_status
            FROM products_enhanced p
            LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
            LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
            LEFT JOIN categories_enhanced c ON p.category_id = c.id
            ORDER BY b.brand_name, m.model_name, p.product_name";

    $result = $conn->query($sql);

    if (!$result) {
        die("Database error: " . $conn->error);
    }

    // Start Excel output
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
    echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
    
    // Worksheet
    echo '<Worksheet ss:Name="Inventory">' . "\n";
    echo '<Table>' . "\n";

    // Header row with styling
    echo '<Row ss:StyleID="header">' . "\n";
    $headers = [
        'ID',
        'Product Name',
        'Brand',
        'Model', 
        'Category',
        'Part Number',
        'SKU',
        'Regular Price (RWF)',
        'Sale Price (RWF)',
        'Stock Quantity',
        'Stock Status',
        'Year From',
        'Year To',
        'Featured',
        'Description',
        'Date Added'
    ];

    foreach ($headers as $header) {
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
    }
    echo '</Row>' . "\n";

    // Data rows
    $row_count = 0;
    while ($row = $result->fetch_assoc()) {
        $row_count++;
        echo '<Row>' . "\n";
        
        // ID
        echo '<Cell><Data ss:Type="Number">' . $row['id'] . '</Data></Cell>' . "\n";
        
        // Product Name
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['product_name']) . '</Data></Cell>' . "\n";
        
        // Brand
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['brand_name'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // Model
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['model_name'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // Category
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['category_name'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // Part Number
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['part_number'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // SKU
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['sku'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // Regular Price
        echo '<Cell><Data ss:Type="Number">' . number_format($row['regular_price'], 2, '.', '') . '</Data></Cell>' . "\n";
        
        // Sale Price
        $sale_price = $row['sale_price'] ? number_format($row['sale_price'], 2, '.', '') : 'N/A';
        if ($sale_price === 'N/A') {
            echo '<Cell><Data ss:Type="String">N/A</Data></Cell>' . "\n";
        } else {
            echo '<Cell><Data ss:Type="Number">' . $sale_price . '</Data></Cell>' . "\n";
        }
        
        // Stock Quantity
        echo '<Cell><Data ss:Type="Number">' . $row['stock_quantity'] . '</Data></Cell>' . "\n";
        
        // Stock Status
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($row['stock_status']) . '</Data></Cell>' . "\n";
        
        // Year From
        echo '<Cell><Data ss:Type="String">' . ($row['year_from'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // Year To
        echo '<Cell><Data ss:Type="String">' . ($row['year_to'] ?? 'N/A') . '</Data></Cell>' . "\n";
        
        // Featured
        echo '<Cell><Data ss:Type="String">' . ($row['is_featured'] ? 'Yes' : 'No') . '</Data></Cell>' . "\n";
        
        // Description
        $description = strip_tags($row['description'] ?? '');
        $description = str_replace(["\r\n", "\r", "\n"], ' ', $description);
        echo '<Cell><Data ss:Type="String">' . htmlspecialchars($description) . '</Data></Cell>' . "\n";
        
        // Date Added
        echo '<Cell><Data ss:Type="String">' . date('Y-m-d H:i:s', strtotime($row['created_at'])) . '</Data></Cell>' . "\n";
        
        echo '</Row>' . "\n";
    }

    // Add summary row
    echo '<Row>' . "\n";
    echo '<Cell><Data ss:Type="String">TOTAL PRODUCTS:</Data></Cell>' . "\n";
    echo '<Cell><Data ss:Type="Number">' . $row_count . '</Data></Cell>' . "\n";
    echo '</Row>' . "\n";

    echo '</Table>' . "\n";
    echo '</Worksheet>' . "\n";
    echo '</Workbook>' . "\n";

    $conn->close();

} catch (Exception $e) {
    die("Error generating export: " . $e->getMessage());
}
?>
