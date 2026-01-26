<?php
// API endpoint to get filter options for the shop
// SPARE XPRESS LTD - Dynamic Filters API

include_once '../includes/config.php';

// Prevent any HTML output for API responses
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    $filters = [];

    // Get brands
    $brand_sql = "SELECT DISTINCT b.brand_name, b.logo_image, COUNT(p.id) as product_count
                  FROM vehicle_brands_enhanced b
                  LEFT JOIN products_enhanced p ON b.id = p.brand_id
                  GROUP BY b.id, b.brand_name, b.logo_image
                  ORDER BY b.brand_name";
    $brand_result = $conn->query($brand_sql);
    $brands = [];
    while ($row = $brand_result->fetch_assoc()) {
        $brands[] = [
            'name' => $row['brand_name'],
            'icon' => $row['logo_image'],
            'count' => (int)$row['product_count']
        ];
    }
    $filters['brands'] = $brands;

    // Get categories
    $category_sql = "SELECT DISTINCT c.category_name, COUNT(p.id) as product_count
                     FROM categories_enhanced c
                     LEFT JOIN products_enhanced p ON c.id = p.category_id
                     GROUP BY c.id, c.category_name
                     ORDER BY c.category_name";
    $category_result = $conn->query($category_sql);
    $categories = [];
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = [
            'name' => $row['category_name'],
            'count' => (int)$row['product_count']
        ];
    }
    $filters['categories'] = $categories;

    // Get models (grouped by brand)
    $model_sql = "SELECT DISTINCT b.brand_name, m.model_name, COUNT(p.id) as product_count
                  FROM vehicle_models_enhanced m
                  JOIN vehicle_brands_enhanced b ON m.brand_id = b.id
                  LEFT JOIN products_enhanced p ON m.id = p.model_id
                  GROUP BY b.brand_name, m.model_name
                  ORDER BY b.brand_name, m.model_name";
    $model_result = $conn->query($model_sql);
    $models = [];
    while ($row = $model_result->fetch_assoc()) {
        $brand_name = $row['brand_name'];
        if (!isset($models[$brand_name])) {
            $models[$brand_name] = [];
        }
        $models[$brand_name][] = [
            'name' => $row['model_name'],
            'count' => (int)$row['product_count']
        ];
    }
    $filters['models'] = $models;

    // Get price range
    $price_sql = "SELECT MIN(regular_price) as min_price, MAX(regular_price) as max_price FROM products_enhanced WHERE regular_price > 0";
    $price_result = $conn->query($price_sql);
    $price_range = $price_result->fetch_assoc();
    $filters['price_range'] = [
        'min' => (float)($price_range['min_price'] ?: 0),
        'max' => (float)($price_range['max_price'] ?: 1000000)
    ];

    // Get year range - set defaults since data may not have year info
    $filters['year_range'] = [
        'min' => 1990,
        'max' => (int)date('Y') + 1
    ];

    // Static condition options (since not in current schema)
    $filters['conditions'] = [
        ['name' => 'OEM', 'value' => 'oem'],
        ['name' => 'Aftermarket', 'value' => 'aftermarket'],
        ['name' => 'New', 'value' => 'new'],
        ['name' => 'Used', 'value' => 'used'],
        ['name' => 'Reconditioned', 'value' => 'reconditioned']
    ];

    // Availability options
    $availability_sql = "SELECT
                            SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) as in_stock_count,
                            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as special_order_count
                         FROM products_enhanced";
    $availability_result = $conn->query($availability_sql);
    $availability = $availability_result->fetch_assoc();
    $filters['availability'] = [
        ['name' => 'In Stock', 'value' => 'in_stock', 'count' => (int)$availability['in_stock_count']],
        ['name' => 'Special Order', 'value' => 'special_order', 'count' => (int)$availability['special_order_count']]
    ];

    // Sorting options
    $filters['sorting'] = [
        ['name' => 'Newest First', 'value' => 'newest'],
        ['name' => 'Oldest First', 'value' => 'oldest'],
        ['name' => 'Price: Low to High', 'value' => 'price_low'],
        ['name' => 'Price: High to Low', 'value' => 'price_high'],
        ['name' => 'Name: A to Z', 'value' => 'name_az'],
        ['name' => 'Name: Z to A', 'value' => 'name_za']
    ];

    echo json_encode([
        'success' => true,
        'filters' => $filters
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>