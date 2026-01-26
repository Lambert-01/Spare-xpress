    <?php
// API endpoint to get products with filtering, sorting, and pagination
// SPARE XPRESS LTD - Dynamic Shop API

include_once '../includes/config.php';

// Prevent any HTML output for API responses
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Get parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$model = isset($_GET['model']) ? trim($_GET['model']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$year = isset($_GET['year']) ? trim($_GET['year']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$condition = isset($_GET['condition']) ? trim($_GET['condition']) : '';
$availability = isset($_GET['availability']) ? trim($_GET['availability']) : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$ids = isset($_GET['ids']) ? trim($_GET['ids']) : '';

try {
    // Build WHERE clause
    $where_conditions = [];
    $params = [];
    $param_types = '';

    // Search
    if (!empty($search)) {
        $where_conditions[] = "(p.product_name LIKE ? OR p.description LIKE ? OR b.brand_name LIKE ? OR c.category_name LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $param_types .= 'ssss';
    }

    // Brand filter
    if (!empty($brand)) {
        $where_conditions[] = "b.brand_name = ?";
        $params[] = $brand;
        $param_types .= 's';
    }

    // Model filter
    if (!empty($model)) {
        $where_conditions[] = "m.model_name = ?";
        $params[] = $model;
        $param_types .= 's';
    }

    // Category filter
    if (!empty($category)) {
        $where_conditions[] = "c.category_name = ?";
        $params[] = $category;
        $param_types .= 's';
    }

    // Year filter
    if (!empty($year)) {
        $year_conditions = [];

        // Split by comma to handle multiple year selections
        $year_values = explode(',', $year);

        foreach ($year_values as $year_value) {
            $year_value = trim($year_value);

            // Check if it's a range (e.g., "2020-2024")
            if (strpos($year_value, '-') !== false) {
                list($start_year, $end_year) = explode('-', $year_value);
                $start_year = (int)trim($start_year);
                $end_year = (int)trim($end_year);

                // Products compatible with any year in this range
                $year_conditions[] = "(p.year_from <= ? AND p.year_to >= ?)";
                $params[] = $end_year;
                $params[] = $start_year;
                $param_types .= 'ii';
            } else {
                // Single year
                $single_year = (int)$year_value;
                $year_conditions[] = "(p.year_from <= ? AND p.year_to >= ?)";
                $params[] = $single_year;
                $params[] = $single_year;
                $param_types .= 'ii';
            }
        }

        if (!empty($year_conditions)) {
            $where_conditions[] = "(" . implode(" OR ", $year_conditions) . ")";
        }
    }

    // Price range
    if ($min_price > 0) {
        $where_conditions[] = "p.regular_price >= ?";
        $params[] = $min_price;
        $param_types .= 'd';
    }
    if ($max_price > 0) {
        $where_conditions[] = "p.regular_price <= ?";
        $params[] = $max_price;
        $param_types .= 'd';
    }

    // Condition filter (we'll use a simple mapping for now)
    if (!empty($condition)) {
        // This would need to be adjusted based on how you store condition in DB
        // For now, we'll skip this as the schema doesn't have a condition field
    }

    // Availability filter
    if ($availability === 'in_stock') {
        $where_conditions[] = "p.stock_quantity > 0";
    } elseif ($availability === 'special_order') {
        $where_conditions[] = "p.stock_quantity = 0";
    }

    // Single product ID filter (for quick view)
    if ($id > 0) {
        $where_conditions[] = "p.id = ?";
        $params[] = $id;
        $param_types .= 'i';
    }

    // Multiple product IDs filter (for recently viewed, comparison)
    if (!empty($ids)) {
        $id_array = array_map('intval', explode(',', $ids));
        if (!empty($id_array)) {
            $placeholders = str_repeat('?,', count($id_array) - 1) . '?';
            $where_conditions[] = "p.id IN ($placeholders)";
            $params = array_merge($params, $id_array);
            $param_types .= str_repeat('i', count($id_array));
        }
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Build ORDER BY clause
    $order_by = 'ORDER BY p.created_at DESC'; // default: newest first

    switch ($sort) {
        case 'price_low':
            $order_by = 'ORDER BY p.price ASC';
            break;
        case 'price_high':
            $order_by = 'ORDER BY p.price DESC';
            break;
        case 'name_az':
            $order_by = 'ORDER BY p.product_name ASC';
            break;
        case 'name_za':
            $order_by = 'ORDER BY p.product_name DESC';
            break;
        case 'oldest':
            $order_by = 'ORDER BY p.created_at ASC';
            break;
        case 'newest':
        default:
            $order_by = 'ORDER BY p.created_at DESC';
            break;
    }

    // Calculate offset
    $offset = ($page - 1) * $limit;

    // Get total count
    $count_sql = "SELECT COUNT(DISTINCT p.id) as total FROM products_enhanced p
                  LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
                  LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
                  LEFT JOIN categories_enhanced c ON p.category_id = c.id
                  $where_clause";

    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $count_stmt->bind_param($param_types, ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_count = $count_result->fetch_assoc()['total'];
    $count_stmt->close();

    // Get products
    $sql = "SELECT p.*, b.brand_name, m.model_name, c.category_name,
                    CASE
                        WHEN p.stock_quantity > 5 THEN 'In Stock'
                        WHEN p.stock_quantity > 0 THEN 'Low Stock'
                        ELSE 'Special Order'
                    END as stock_status
             FROM products_enhanced p
             LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
             LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
             LEFT JOIN categories_enhanced c ON p.category_id = c.id
             $where_clause
             $order_by
             LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $all_params = array_merge($params, [$limit, $offset]);
    $all_types = $param_types . 'ii';
    $stmt->bind_param($all_types, ...$all_params);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Format image path - ensure correct path (uploads are in admin/uploads/)
        $image_path = $row['main_image'];
        if (!empty($image_path)) {
            // Always prefix with /admin/ and ensure no double slashes
            $image_path = '/admin/' . ltrim($image_path, '/');
        }

        // Format gallery images - ensure correct path (uploads are in admin/uploads/)
        $gallery_images = json_decode($row['gallery_images'], true) ?: [];
        $formatted_gallery = [];
        foreach ($gallery_images as $gallery_image) {
            if (!empty($gallery_image)) {
                // Always prefix with /admin/ and ensure no double slashes
                $formatted_gallery[] = '/admin/' . ltrim($gallery_image, '/');
            }
        }

        $products[] = [
            'id' => $row['id'],
            'name' => $row['product_name'],
            'description' => $row['description'],
            'price' => (float)$row['regular_price'],
            'sale_price' => $row['sale_price'] ? (float)$row['sale_price'] : null,
            'brand' => $row['brand_name'],
            'model' => $row['model_name'],
            'category' => $row['category_name'],
            'stock' => (int)$row['stock_quantity'],
            'stock_status' => $row['stock_status'],
            'image' => $image_path ?: '/img/no-image.png',
            'gallery_images' => $formatted_gallery,
            'year_from' => $row['year_from'] ?? null,
            'year_to' => $row['year_to'] ?? null,
            'is_featured' => (bool)$row['is_featured'],
            'on_sale' => $row['sale_price'] && $row['sale_price'] < $row['regular_price']
        ];
    }

    $stmt->close();

    // Calculate pagination info
    $total_pages = ceil($total_count / $limit);

    echo json_encode([
        'success' => true,
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_products' => $total_count,
            'per_page' => $limit
        ],
        'filters' => [
            'search' => $search,
            'brand' => $brand,
            'model' => $model,
            'category' => $category,
            'year' => $year,
            'min_price' => $min_price,
            'max_price' => $max_price,
            'condition' => $condition,
            'availability' => $availability,
            'sort' => $sort,
            'id' => $id,
            'ids' => $ids
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>