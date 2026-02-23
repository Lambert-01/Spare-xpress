<?php
/**
 * SPARE XPRESS LTD - Public Inventory Excel Export
 * Simplified version - no login required
 * Exports all products with brands, models, categories, and prices to Excel
 */

include_once 'includes/config.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="SPARE_XPRESS_Inventory_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Query to get all products with related information
    $sql = "SELECT 
                p.id,
                p.product_name,
                p.description,
                p.short_description,
                p.sku,
                b.brand_name,
                m.model_name,
                c.category_name,
                p.price,
                p.sale_price,
                p.stock_quantity,
                p.`condition`,
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
            WHERE p.is_active = 1
            ORDER BY b.brand_name, m.model_name, p.product_name";

    $result = $conn->query($sql);

    if (!$result) {
        die("Database error: " . $conn->error);
    }

    // Start Excel XML output
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
 
<Styles>
 <Style ss:ID="header">
  <Font ss:Bold="1" ss:Size="12" ss:Color="#FFFFFF"/>
  <Interior ss:Color="#4472C4" ss:Pattern="Solid"/>
  <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
 </Style>
 <Style ss:ID="currency">
  <NumberFormat ss:Format="#,##0.00"/>
 </Style>
</Styles>

<Worksheet ss:Name="SPARE XPRESS Inventory">
<Table>

<!-- Header Row -->
<Row ss:StyleID="header" ss:Height="25">
 <Cell><Data ss:Type="String">ID</Data></Cell>
 <Cell><Data ss:Type="String">Product Name</Data></Cell>
 <Cell><Data ss:Type="String">Brand</Data></Cell>
 <Cell><Data ss:Type="String">Model</Data></Cell>
 <Cell><Data ss:Type="String">Category</Data></Cell>
 <Cell><Data ss:Type="String">SKU</Data></Cell>
 <Cell><Data ss:Type="String">Condition</Data></Cell>
 <Cell><Data ss:Type="String">Price (RWF)</Data></Cell>
 <Cell><Data ss:Type="String">Sale Price (RWF)</Data></Cell>
 <Cell><Data ss:Type="String">Stock Qty</Data></Cell>
 <Cell><Data ss:Type="String">Stock Status</Data></Cell>
 <Cell><Data ss:Type="String">Featured</Data></Cell>
 <Cell><Data ss:Type="String">Short Description</Data></Cell>
 <Cell><Data ss:Type="String">Description</Data></Cell>
 <Cell><Data ss:Type="String">Date Added</Data></Cell>
</Row>

<?php
    // Data rows
    $row_count = 0;
    $total_value = 0;
    
    while ($row = $result->fetch_assoc()) {
        $row_count++;
        $total_value += $row['price'] * $row['stock_quantity'];
        
        // Clean descriptions
        $description = strip_tags($row['description'] ?? '');
        $description = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $description);
        $description = preg_replace('/\s+/', ' ', $description);
        $description = trim($description);
        
        $short_description = strip_tags($row['short_description'] ?? '');
        $short_description = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $short_description);
        
        echo '<Row>' . "\n";
        echo ' <Cell><Data ss:Type="Number">' . $row['id'] . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars($row['product_name'], ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars($row['brand_name'] ?? 'N/A', ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars($row['model_name'] ?? 'N/A', ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars($row['category_name'] ?? 'N/A', ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars($row['sku'] ?? 'N/A', ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . ucfirst($row['condition'] ?? 'new') . '</Data></Cell>' . "\n";
        echo ' <Cell ss:StyleID="currency"><Data ss:Type="Number">' . number_format($row['price'], 2, '.', '') . '</Data></Cell>' . "\n";
        
        if ($row['sale_price']) {
            echo ' <Cell ss:StyleID="currency"><Data ss:Type="Number">' . number_format($row['sale_price'], 2, '.', '') . '</Data></Cell>' . "\n";
        } else {
            echo ' <Cell><Data ss:Type="String">-</Data></Cell>' . "\n";
        }
        
        echo ' <Cell><Data ss:Type="Number">' . $row['stock_quantity'] . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars($row['stock_status'], ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . ($row['is_featured'] ? 'Yes' : 'No') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars(substr($short_description, 0, 200), ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . htmlspecialchars(substr($description, 0, 500), ENT_XML1, 'UTF-8') . '</Data></Cell>' . "\n";
        echo ' <Cell><Data ss:Type="String">' . date('Y-m-d', strtotime($row['created_at'])) . '</Data></Cell>' . "\n";
        echo '</Row>' . "\n";
    }
?>

<!-- Summary Row -->
<Row>
 <Cell></Cell>
 <Cell><Data ss:Type="String">TOTAL PRODUCTS:</Data></Cell>
 <Cell><Data ss:Type="Number"><?php echo $row_count; ?></Data></Cell>
 <Cell></Cell>
 <Cell></Cell>
 <Cell></Cell>
 <Cell><Data ss:Type="String">Total Inventory Value:</Data></Cell>
 <Cell ss:StyleID="currency"><Data ss:Type="Number"><?php echo number_format($total_value, 2, '.', ''); ?></Data></Cell>
</Row>

</Table>
</Worksheet>
</Workbook>
<?php

    $conn->close();

} catch (Exception $e) {
    die("Error generating export: " . $e->getMessage());
}
?>
