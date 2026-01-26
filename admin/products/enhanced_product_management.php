<?php
// Enhanced Product Management System for SPARE XPRESS LTD
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Add new product
        $product_name = trim($_POST['product_name']);
        $slug = generateSlug($product_name);
        $sku = trim($_POST['sku']) ?: generateSKU();
        $brand_id = (int)$_POST['brand_id'];
        $model_id = !empty($_POST['model_id']) ? (int)$_POST['model_id'] : null;
        $category_id = (int)$_POST['category_id'];
        $description = trim($_POST['description']);
        $short_description = trim($_POST['short_description']);

        // Pricing
        $regular_price = !empty($_POST['regular_price']) ? (float)$_POST['regular_price'] : null;
        $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $wholesale_price = !empty($_POST['wholesale_price']) ? (float)$_POST['wholesale_price'] : null;

        // Stock
        $stock_quantity = (int)$_POST['stock_quantity'];
        $stock_status = $_POST['stock_status'];
        $low_stock_threshold = (int)$_POST['low_stock_threshold'];
        $manage_stock = isset($_POST['manage_stock']) ? 1 : 0;
        $backorders_allowed = isset($_POST['backorders_allowed']) ? 1 : 0;

        // Product details
        $product_condition = $_POST['product_condition'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $visibility = $_POST['visibility'];

        // Warranty
        $warranty_period = trim($_POST['warranty_period']);
        $warranty_type = $_POST['warranty_type'];
        $warranty_details = trim($_POST['warranty_details']);

        // Compatibility
        $compatible_models = isset($_POST['compatible_models']) ? json_encode($_POST['compatible_models']) : '[]';

        // Tags
        $tags = isset($_POST['tags']) ? json_encode($_POST['tags']) : '[]';

        // Specifications
        $specifications = trim($_POST['specifications']);

        // SEO
        $seo_title = trim($_POST['seo_title']);
        $seo_description = trim($_POST['seo_description']);
        $meta_keywords = trim($_POST['meta_keywords']);

        $stmt = $conn->prepare("INSERT INTO products_enhanced
            (product_name, slug, sku, brand_id, model_id, category_id, description, short_description,
             regular_price, sale_price, wholesale_price, stock_quantity, stock_status, low_stock_threshold,
             manage_stock, backorders_allowed, product_condition, is_featured, is_active, visibility,
             warranty_period, warranty_type, warranty_details, compatible_models, tags, specifications,
             seo_title, seo_description, meta_keywords)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssisssssssisiisssssssss",
            $product_name, $slug, $sku, $brand_id, $model_id, $category_id, $description, $short_description,
            $regular_price, $sale_price, $wholesale_price, $stock_quantity, $stock_status, $low_stock_threshold,
            $manage_stock, $backorders_allowed, $product_condition, $is_featured, $is_active, $visibility,
            $warranty_period, $warranty_type, $warranty_details, $compatible_models, $tags, $specifications,
            $seo_title, $seo_description, $meta_keywords);

        if ($stmt->execute()) {
            $product_id = $conn->insert_id;

            // Handle main image upload
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/products/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
                $file_name = $slug . '_main.' . $file_extension;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_path)) {
                    $conn->query("UPDATE products_enhanced SET main_image = '/uploads/products/$file_name' WHERE id = $product_id");
                }
            }

            // Handle gallery images
            $gallery_images = [];
            if (isset($_FILES['gallery_images'])) {
                for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
                    if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_extension = pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION);
                        $file_name = $slug . '_gallery_' . ($i + 1) . '.' . $file_extension;
                        $target_path = $upload_dir . $file_name;

                        if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $target_path)) {
                            $gallery_images[] = '/uploads/products/' . $file_name;
                        }
                    }
                }
            }

            if (!empty($gallery_images)) {
                $gallery_json = json_encode($gallery_images);
                $conn->query("UPDATE products_enhanced SET gallery_images = '$gallery_json' WHERE id = $product_id");
            }

            $_SESSION['success'] = 'Product added successfully!';
            header('Location: enhanced_product_management.php');
            exit;
        } else {
            $_SESSION['error'] = 'Failed to add product: ' . $conn->error;
        }
    }

    if (isset($_POST['update_product'])) {
        // Update existing product
        $product_id = (int)$_POST['product_id'];
        $product_name = trim($_POST['product_name']);
        $slug = generateSlug($product_name);
        $sku = trim($_POST['sku']) ?: generateSKU();
        $brand_id = (int)$_POST['brand_id'];
        $model_id = !empty($_POST['model_id']) ? (int)$_POST['model_id'] : null;
        $category_id = (int)$_POST['category_id'];
        $description = trim($_POST['description']);
        $short_description = trim($_POST['short_description']);

        // Pricing
        $regular_price = !empty($_POST['regular_price']) ? (float)$_POST['regular_price'] : null;
        $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
        $wholesale_price = !empty($_POST['wholesale_price']) ? (float)$_POST['wholesale_price'] : null;

        // Stock
        $stock_quantity = (int)$_POST['stock_quantity'];
        $stock_status = $_POST['stock_status'];
        $low_stock_threshold = (int)$_POST['low_stock_threshold'];
        $manage_stock = isset($_POST['manage_stock']) ? 1 : 0;
        $backorders_allowed = isset($_POST['backorders_allowed']) ? 1 : 0;

        // Product details
        $product_condition = $_POST['product_condition'];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $visibility = $_POST['visibility'];

        // Warranty
        $warranty_period = trim($_POST['warranty_period']);
        $warranty_type = $_POST['warranty_type'];
        $warranty_details = trim($_POST['warranty_details']);

        // Compatibility
        $compatible_models = isset($_POST['compatible_models']) ? json_encode($_POST['compatible_models']) : '[]';

        // Tags
        $tags = isset($_POST['tags']) ? json_encode($_POST['tags']) : '[]';

        // Specifications
        $specifications = trim($_POST['specifications']);

        // SEO
        $seo_title = trim($_POST['seo_title']);
        $seo_description = trim($_POST['seo_description']);
        $meta_keywords = trim($_POST['meta_keywords']);

        $stmt = $conn->prepare("UPDATE products_enhanced SET
            product_name = ?, slug = ?, sku = ?, brand_id = ?, model_id = ?, category_id = ?, description = ?, short_description = ?,
            regular_price = ?, sale_price = ?, wholesale_price = ?, stock_quantity = ?, stock_status = ?, low_stock_threshold = ?,
            manage_stock = ?, backorders_allowed = ?, product_condition = ?, is_featured = ?, is_active = ?, visibility = ?,
            warranty_period = ?, warranty_type = ?, warranty_details = ?, compatible_models = ?, tags = ?, specifications = ?,
            seo_title = ?, seo_description = ?, meta_keywords = ?
            WHERE id = ?");

        $stmt->bind_param("sssisssssssisiisssssssssssi",
            $product_name, $slug, $sku, $brand_id, $model_id, $category_id, $description, $short_description,
            $regular_price, $sale_price, $wholesale_price, $stock_quantity, $stock_status, $low_stock_threshold,
            $manage_stock, $backorders_allowed, $product_condition, $is_featured, $is_active, $visibility,
            $warranty_period, $warranty_type, $warranty_details, $compatible_models, $tags, $specifications,
            $seo_title, $seo_description, $meta_keywords, $product_id);

        if ($stmt->execute()) {
            // Handle main image upload
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/products/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

                $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
                $file_name = $slug . '_main.' . $file_extension;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_path)) {
                    $conn->query("UPDATE products_enhanced SET main_image = '/uploads/products/$file_name' WHERE id = $product_id");
                }
            }

            // Handle gallery images
            if (isset($_FILES['gallery_images'])) {
                $gallery_images = [];
                for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
                    if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_extension = pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION);
                        $file_name = $slug . '_gallery_' . ($i + 1) . '.' . $file_extension;
                        $target_path = $upload_dir . $file_name;

                        if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $target_path)) {
                            $gallery_images[] = '/uploads/products/' . $file_name;
                        }
                    }
                }

                if (!empty($gallery_images)) {
                    $gallery_json = json_encode($gallery_images);
                    $conn->query("UPDATE products_enhanced SET gallery_images = '$gallery_json' WHERE id = $product_id");
                }
            }

            $_SESSION['success'] = 'Product updated successfully!';
            header('Location: enhanced_product_management.php');
            exit;
        } else {
            $_SESSION['error'] = 'Failed to update product: ' . $conn->error;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if product has orders
    $check_orders = $conn->query("SELECT COUNT(*) as count FROM order_items_enhanced WHERE product_id = $id")->fetch_assoc()['count'];

    if ($check_orders > 0) {
        $_SESSION['error'] = 'Cannot delete product with associated orders. Please deactivate it instead.';
    } else {
        if ($conn->query("DELETE FROM products_enhanced WHERE id = $id")) {
            $_SESSION['success'] = 'Product deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete product.';
        }
    }

    header('Location: enhanced_product_management.php');
    exit;
}

// Get products with analytics
$query = "
    SELECT p.*,
           b.brand_name,
           m.model_name,
           c.category_name,
           COALESCE(p.sales_count, 0) as sales_count,
           COALESCE(p.view_count, 0) as view_count,
           CASE
               WHEN p.stock_quantity <= p.low_stock_threshold THEN 'low_stock'
               WHEN p.stock_quantity = 0 THEN 'out_of_stock'
               ELSE 'in_stock'
           END as calculated_stock_status
    FROM products_enhanced p
    LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
    LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
    LEFT JOIN categories_enhanced c ON p.category_id = c.id
    ORDER BY p.created_at DESC
";

$result = $conn->query($query);

// Get filter parameters
$brand_filter = $_GET['brand'] ?? 'all';
$model_filter = $_GET['model'] ?? 'all';
$category_filter = $_GET['category'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';
$stock_filter = $_GET['stock'] ?? 'all';
$search = $_GET['search'] ?? '';

// Apply filters
$where_conditions = [];
if ($brand_filter !== 'all') {
    $where_conditions[] = "p.brand_id = " . (int)$brand_filter;
}
if ($model_filter !== 'all') {
    $where_conditions[] = "p.model_id = " . (int)$model_filter;
}
if ($category_filter !== 'all') {
    $where_conditions[] = "p.category_id = " . (int)$category_filter;
}
if ($status_filter !== 'all') {
    $where_conditions[] = "p.is_active = " . ($status_filter === 'active' ? 1 : 0);
}
if ($stock_filter !== 'all') {
    switch ($stock_filter) {
        case 'in_stock':
            $where_conditions[] = "p.stock_quantity > p.low_stock_threshold";
            break;
        case 'low_stock':
            $where_conditions[] = "p.stock_quantity <= p.low_stock_threshold AND p.stock_quantity > 0";
            break;
        case 'out_of_stock':
            $where_conditions[] = "p.stock_quantity = 0";
            break;
    }
}
if (!empty($search)) {
    $where_conditions[] = "(p.product_name LIKE '%" . $conn->real_escape_string($search) . "%' OR p.sku LIKE '%" . $conn->real_escape_string($search) . "%')";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "
    SELECT p.*,
           b.brand_name,
           m.model_name,
           c.category_name,
           COALESCE(p.sales_count, 0) as sales_count,
           COALESCE(p.view_count, 0) as view_count,
           CASE
               WHEN p.stock_quantity <= p.low_stock_threshold THEN 'low_stock'
               WHEN p.stock_quantity = 0 THEN 'out_of_stock'
               ELSE 'in_stock'
           END as calculated_stock_status
    FROM products_enhanced p
    LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
    LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
    LEFT JOIN categories_enhanced c ON p.category_id = c.id
    $where_clause
    ORDER BY p.created_at DESC
";

$result = $conn->query($query);

// Get brands for filter dropdown
$brands_query = $conn->query("SELECT id, brand_name FROM vehicle_brands_enhanced WHERE is_active = 1 ORDER BY brand_name");
$brands = [];
while ($brand = $brands_query->fetch_assoc()) {
    $brands[] = $brand;
}

// Get categories for filter dropdown
$categories_query = $conn->query("SELECT id, category_name FROM categories_enhanced WHERE is_active = 1 ORDER BY category_name");
$categories = [];
while ($category = $categories_query->fetch_assoc()) {
    $categories[] = $category;
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function generateSKU() {
    return 'SPX-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
}

function getStockStatusBadge($status) {
    $badges = [
        'in_stock' => '<span class="badge bg-success">In Stock</span>',
        'low_stock' => '<span class="badge bg-warning text-dark">Low Stock</span>',
        'out_of_stock' => '<span class="badge bg-danger">Out of Stock</span>',
        'on_backorder' => '<span class="badge bg-info">Backorder</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-box-seam-fill text-primary me-3"></i>
                Enhanced Product Management
            </h1>
            <p class="text-muted mb-0 fs-5">Professional product management with advanced features and analytics</p>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Product
        </button>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                    <h3 class="card-value text-primary mb-2" id="totalProducts"><?php echo $result->num_rows; ?></h3>
                    <p class="card-title mb-0">Total Products</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-check-circle fs-1"></i>
                    </div>
                    <h3 class="card-value text-success mb-2" id="activeProducts">
                        <?php
                        $active_count = $conn->query("SELECT COUNT(*) as count FROM products_enhanced WHERE is_active = 1")->fetch_assoc()['count'];
                        echo $active_count;
                        ?>
                    </h3>
                    <p class="card-title mb-0">Active Products</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="bi bi-tags fs-1"></i>
                    </div>
                    <h3 class="card-value text-warning mb-2" id="totalBrands">
                        <?php
                        $brand_count = $conn->query("SELECT COUNT(DISTINCT brand_id) as count FROM products_enhanced WHERE brand_id IS NOT NULL")->fetch_assoc()['count'];
                        echo $brand_count;
                        ?>
                    </h3>
                    <p class="card-title mb-0">Total Brands</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                        <i class="bi bi-graph-up fs-1"></i>
                    </div>
                    <h3 class="card-value text-info mb-2" id="totalSales">
                        <?php
                        $total_sales = $conn->query("SELECT COALESCE(SUM(sales_count), 0) as total FROM products_enhanced WHERE is_active = 1")->fetch_assoc()['total'];
                        echo number_format($total_sales);
                        ?>
                    </h3>
                    <p class="card-title mb-0">Total Sales</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="form-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label fw-semibold">Brand</label>
                <select class="form-select filter-select" id="brandFilter" data-filter="brand">
                    <option value="all">All Brands</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand['id']; ?>" <?php echo $brand_filter == $brand['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Model</label>
                <select class="form-select filter-select" id="modelFilter" data-filter="model">
                    <option value="all">All Models</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Category</label>
                <select class="form-select filter-select" id="categoryFilter" data-filter="category">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select filter-select" id="statusFilter" data-filter="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Stock</label>
                <select class="form-select filter-select" id="stockFilter" data-filter="stock">
                    <option value="all" <?php echo $stock_filter === 'all' ? 'selected' : ''; ?>>All Stock</option>
                    <option value="in_stock" <?php echo $stock_filter === 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                    <option value="low_stock" <?php echo $stock_filter === 'low_stock' ? 'selected' : ''; ?>>Low Stock</option>
                    <option value="out_of_stock" <?php echo $stock_filter === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success w-100" onclick="exportProducts()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4" id="products-container">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="product-card enhanced-card h-100">
                    <!-- Product Image Header -->
                    <div class="card-image-header">
                        <?php if ($product['main_image']): ?>
                            <img src="../<?php echo htmlspecialchars($product['main_image']); ?>"
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 class="product-hero-image">
                            <div class="image-overlay"></div>
                        <?php else: ?>
                            <div class="product-hero-placeholder">
                                <i class="bi bi-box-seam-fill display-4 text-white opacity-75"></i>
                                <div class="image-overlay"></div>
                            </div>
                        <?php endif; ?>

                        <!-- Product Badges -->
                        <div class="product-badges">
                            <?php if ($product['is_featured']): ?>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-star-fill me-1"></i>Featured
                                </span>
                            <?php endif; ?>
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['regular_price']): ?>
                                <span class="badge bg-danger">
                                    <i class="bi bi-tag-fill me-1"></i>Sale
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Controls -->
                        <div class="card-controls">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle opacity-75" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="view_product.php?id=<?php echo $product['id']; ?>">
                                        <i class="bi bi-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="edit_product.php?id=<?php echo $product['id']; ?>">
                                        <i class="bi bi-pencil me-2"></i>Edit Product</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="duplicateProduct(<?php echo $product['id']; ?>)">
                                        <i class="bi bi-copy me-2"></i>Duplicate</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')">
                                        <i class="bi bi-trash me-2"></i>Delete Product</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="card-content">
                        <!-- Product Info -->
                        <div class="product-info mb-3">
                            <h6 class="product-title mb-1"><?php echo htmlspecialchars($product['product_name']); ?></h6>
                            <div class="product-meta">
                                <div class="brand-model-info">
                                    <small class="text-muted">
                                        <i class="bi bi-tags me-1"></i>
                                        <?php echo htmlspecialchars($product['brand_name'] ?: 'No Brand'); ?>
                                        <?php if ($product['model_name']): ?>
                                            / <?php echo htmlspecialchars($product['model_name']); ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="category-info">
                                    <small class="text-muted">
                                        <i class="bi bi-grid me-1"></i>
                                        <?php echo htmlspecialchars($product['category_name'] ?: 'No Category'); ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="product-pricing mb-3">
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['regular_price']): ?>
                                <div class="price-sale">
                                    <span class="original-price text-decoration-line-through text-muted small">
                                        RWF <?php echo number_format($product['regular_price'], 0); ?>
                                    </span>
                                    <div class="sale-price text-danger fw-bold">
                                        RWF <?php echo number_format($product['sale_price'], 0); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="regular-price text-primary fw-bold">
                                    RWF <?php echo number_format($product['regular_price'], 0); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Stock & Analytics Grid -->
                        <div class="analytics-grid mb-3">
                            <div class="analytics-item">
                                <div class="analytics-value fw-bold <?php
                                    echo match($product['calculated_stock_status']) {
                                        'in_stock' => 'text-success',
                                        'low_stock' => 'text-warning',
                                        'out_of_stock' => 'text-danger',
                                        default => 'text-muted'
                                    };
                                ?>">
                                    <?php echo $product['stock_quantity']; ?>
                                </div>
                                <div class="analytics-label small text-muted">Stock</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-info fw-bold"><?php echo number_format($product['sales_count']); ?></div>
                                <div class="analytics-label small text-muted">Sales</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-secondary fw-bold"><?php echo number_format($product['view_count']); ?></div>
                                <div class="analytics-label small text-muted">Views</div>
                            </div>
                        </div>

                        <!-- Status and Actions -->
                        <div class="card-footer-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-badge <?php echo $product['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-danger btn-sm action-btn" onclick="deleteProduct(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['product_name']); ?>')">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Loading State -->
    <div class="text-center py-5 d-none" id="loading-state">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted mt-2">Loading products...</p>
    </div>

    <!-- Empty State -->
    <div class="empty-state text-center py-5 d-none" id="empty-state">
        <i class="bi bi-box-seam display-1 text-muted mb-3"></i>
        <h4>No Products Found</h4>
        <p class="text-muted">No products match your current filters.</p>
        <button type="button" class="btn btn-primary" onclick="clearFilters()">
            <i class="bi bi-arrow-counterclockwise me-2"></i>Clear Filters
        </button>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add New Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Basic Information -->
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Product Name *</label>
                            <input type="text" class="form-control" name="product_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SKU</label>
                            <input type="text" class="form-control" name="sku" placeholder="Auto-generated if empty">
                        </div>

                        <!-- Brand, Model, Category -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Brand *</label>
                            <select class="form-select" name="brand_id" required onchange="loadModels(this.value)">
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Model</label>
                            <select class="form-select" name="model_id" id="modelSelect">
                                <option value="">Select Model (Optional)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Category *</label>
                            <select class="form-select" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Descriptions -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Short Description</label>
                            <input type="text" class="form-control" name="short_description" placeholder="Brief product description...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Detailed product description..."></textarea>
                        </div>

                        <!-- Pricing -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Regular Price (RWF)</label>
                            <input type="number" class="form-control" name="regular_price" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sale Price (RWF)</label>
                            <input type="number" class="form-control" name="sale_price" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Wholesale Price (RWF)</label>
                            <input type="number" class="form-control" name="wholesale_price" step="0.01" min="0">
                        </div>

                        <!-- Stock Management -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" class="form-control" name="stock_quantity" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Low Stock Threshold</label>
                            <input type="number" class="form-control" name="low_stock_threshold" value="5" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Stock Status</label>
                            <select class="form-select" name="stock_status">
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                                <option value="on_backorder">On Backorder</option>
                            </select>
<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editProductForm">
                <input type="hidden" name="product_id" id="editProductId">
                <div class="modal-body" id="editProductContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Product Condition</label>
                            <select class="form-select" name="product_condition">
                                <option value="new">New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                            </select>
                        </div>

                        <!-- Images -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Main Product Image</label>
                            <input type="file" class="form-control" name="main_image" accept="image/*">
                            <small class="text-muted">Primary product image</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gallery Images</label>
                            <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                            <small class="text-muted">Additional product images (max 5)</small>
                        </div>

                        <!-- Warranty -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warranty Period</label>
                            <input type="text" class="form-control" name="warranty_period" placeholder="e.g., 2 years, 50000 km">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Warranty Type</label>
                            <select class="form-select" name="warranty_type">
                                <option value="manufacturer">Manufacturer</option>
                                <option value="dealer">Dealer</option>
                                <option value="none">No Warranty</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Visibility</label>
                            <select class="form-select" name="visibility">
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="password_protected">Password Protected</option>
                            </select>
                        </div>

                        <!-- Checkboxes -->
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="manage_stock" id="manageStock" checked>
                                        <label class="form-check-label" for="manageStock">
                                            Manage Stock
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="backorders_allowed" id="backordersAllowed">
                                        <label class="form-check-label" for="backordersAllowed">
                                            Allow Backorders
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured">
                                        <label class="form-check-label" for="isFeatured">
                                            Featured Product
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                        <label class="form-check-label" for="isActive">
                                            Product is Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Technical Specifications (JSON)</label>
                            <textarea class="form-control" name="specifications" rows="4" placeholder='{"engine": "2.0L Turbo", "power": "180hp", "transmission": "6-speed manual"}'></textarea>
                            <small class="text-muted">Enter specifications in JSON format</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.product-thumb {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.product-thumb-placeholder {
    width: 60px;
    height: 45px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
}

.stats-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.card-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.card-title {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
}
</style>

<script>
// Automatic filtering functionality
let filterTimeout;
const currentFilters = {
    brand: '<?php echo $brand_filter; ?>',
    model: '<?php echo $model_filter; ?>',
    category: '<?php echo $category_filter; ?>',
    status: '<?php echo $status_filter; ?>',
    stock: '<?php echo $stock_filter; ?>'
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter selects
    initializeFilters();

    // Load initial models if brand is selected
    if (currentFilters.brand && currentFilters.brand !== 'all') {
        loadFilterModels(currentFilters.brand);
    }
});

function initializeFilters() {
    // Add change event listeners to all filter selects
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', function() {
            const filterType = this.dataset.filter;
            const filterValue = this.value;

            // Update current filters
            currentFilters[filterType] = filterValue;

            // Special handling for brand change - load models
            if (filterType === 'brand') {
                loadFilterModels(filterValue);
                // Reset model filter when brand changes
                currentFilters.model = 'all';
                document.getElementById('modelFilter').value = 'all';
            }

            // Debounce the filter request
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                applyFilters();
            }, 300);
        });
    });
}

function loadFilterModels(brandId) {
    const modelSelect = document.getElementById('modelFilter');
    modelSelect.innerHTML = '<option value="all">All Models</option>';

    if (!brandId || brandId === 'all') {
        return;
    }

    fetch(`../api/get_models_by_brand.php?brand_id=${brandId}`)
        .then(response => response.json())
        .then(data => {
            if (data.models && data.models.length > 0) {
                data.models.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model.id;
                    option.textContent = model.model_name;
                    if (model.id == currentFilters.model) {
                        option.selected = true;
                    }
                    modelSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading models:', error);
        });
}

function applyFilters() {
    // Show loading state
    showLoadingState();

    // Build query string
    const params = new URLSearchParams();
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key] !== 'all') {
            params.append(key, currentFilters[key]);
        }
    });

    // Make AJAX request
    fetch(`../api/get_filtered_products.php?${params}`)
        .then(response => response.json())
        .then(data => {
            hideLoadingState();
            updateProductsGrid(data.products);
            updateStatistics(data.stats);
        })
        .catch(error => {
            console.error('Error filtering products:', error);
            hideLoadingState();
            showToast('Error loading products. Please try again.', 'danger');
        });
}

function updateProductsGrid(products) {
    const container = document.getElementById('products-container');
    const emptyState = document.getElementById('empty-state');

    if (!products || products.length === 0) {
        container.innerHTML = '';
        emptyState.classList.remove('d-none');
        return;
    }

    emptyState.classList.add('d-none');

    let html = '';
    products.forEach(product => {
        const stockStatus = getStockStatus(product.stock_quantity, product.low_stock_threshold);
        const stockBadgeClass = getStockBadgeClass(stockStatus);

        html += `
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="product-card enhanced-card h-100">
                    <div class="card-image-header">
                        ${product.main_image ?
                            `<img src="../${product.main_image}" alt="${product.product_name}" class="product-hero-image">
                             <div class="image-overlay"></div>` :
                            `<div class="product-hero-placeholder">
                                 <i class="bi bi-box-seam-fill display-4 text-white opacity-75"></i>
                                 <div class="image-overlay"></div>
                             </div>`
                        }

                        <div class="product-badges">
                            ${product.is_featured ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Featured</span>' : ''}
                            ${product.sale_price && product.sale_price < product.regular_price ? '<span class="badge bg-danger"><i class="bi bi-tag-fill me-1"></i>Sale</span>' : ''}
                        </div>

                        <div class="card-controls">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle opacity-75" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="view_product.php?id=${product.id}">
                                        <i class="bi bi-eye me-2"></i>View Details</a></li>
                                    <li><a class="dropdown-item" href="edit_product.php?id=${product.id}">
                                        <i class="bi bi-pencil me-2"></i>Edit Product</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="duplicateProduct(${product.id})">
                                        <i class="bi bi-copy me-2"></i>Duplicate</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteProduct(${product.id}, '${product.product_name.replace(/'/g, "\\'")}')">
                                        <i class="bi bi-trash me-2"></i>Delete Product</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="product-info mb-3">
                            <h6 class="product-title mb-1">${product.product_name}</h6>
                            <div class="product-meta">
                                <div class="brand-model-info">
                                    <small class="text-muted">
                                        <i class="bi bi-tags me-1"></i>
                                        ${product.brand_name || 'No Brand'}
                                        ${product.model_name ? ' / ' + product.model_name : ''}
                                    </small>
                                </div>
                                <div class="category-info">
                                    <small class="text-muted">
                                        <i class="bi bi-grid me-1"></i>
                                        ${product.category_name || 'No Category'}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="product-pricing mb-3">
                            ${product.sale_price && product.sale_price < product.regular_price ?
                                `<div class="price-sale">
                                    <span class="original-price text-decoration-line-through text-muted small">
                                        RWF ${Number(product.regular_price).toLocaleString()}
                                    </span>
                                    <div class="sale-price text-danger fw-bold">
                                        RWF ${Number(product.sale_price).toLocaleString()}
                                    </div>
                                </div>` :
                                `<div class="regular-price text-primary fw-bold">
                                    RWF ${Number(product.regular_price).toLocaleString()}
                                </div>`
                            }
                        </div>

                        <div class="analytics-grid mb-3">
                            <div class="analytics-item">
                                <div class="analytics-value fw-bold ${stockBadgeClass}">${product.stock_quantity}</div>
                                <div class="analytics-label small text-muted">Stock</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-info fw-bold">${Number(product.sales_count || 0).toLocaleString()}</div>
                                <div class="analytics-label small text-muted">Sales</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-secondary fw-bold">${Number(product.view_count || 0).toLocaleString()}</div>
                                <div class="analytics-label small text-muted">Views</div>
                            </div>
                        </div>

                        <div class="card-footer-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-badge ${product.is_active ? 'status-active' : 'status-inactive'}">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    ${product.is_active ? 'Active' : 'Inactive'}
                                </span>
                                <div class="action-buttons">
                                    <a href="#" class="btn btn-danger btn-sm action-btn" onclick="deleteProduct(${product.id}, '${product.product_name.replace(/'/g, "\\'")}')">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function updateStatistics(stats) {
    // Update statistics cards with filtered data
    if (stats) {
        // Update Total Products card
        const totalProductsCard = document.querySelector('.stats-card:nth-child(1)');
        if (totalProductsCard) {
            const valueElement = totalProductsCard.querySelector('.card-value');
            if (valueElement) {
                valueElement.textContent = Number(stats.total_products || 0).toLocaleString();
            }
        }

        // Update Active Products card
        const activeProductsCard = document.querySelector('.stats-card:nth-child(2)');
        if (activeProductsCard) {
            const valueElement = activeProductsCard.querySelector('.card-value');
            if (valueElement) {
                valueElement.textContent = Number(stats.active_products || 0).toLocaleString();
            }
        }

        // Update Total Brands card
        const totalBrandsCard = document.querySelector('.stats-card:nth-child(3)');
        if (totalBrandsCard) {
            const valueElement = totalBrandsCard.querySelector('.card-value');
            if (valueElement) {
                valueElement.textContent = Number(stats.total_brands || 0).toLocaleString();
            }
        }

        // Update Total Sales card
        const totalSalesCard = document.querySelector('.stats-card:nth-child(4)');
        if (totalSalesCard) {
            const valueElement = totalSalesCard.querySelector('.card-value');
            if (valueElement) {
                valueElement.textContent = Number(stats.total_sales || 0).toLocaleString();
            }
        }
    }
}

function getStockStatus(quantity, threshold) {
    if (quantity <= threshold && quantity > 0) return 'low_stock';
    if (quantity === 0) return 'out_of_stock';
    return 'in_stock';
}

function getStockBadgeClass(status) {
    switch (status) {
        case 'in_stock': return 'text-success';
        case 'low_stock': return 'text-warning';
        case 'out_of_stock': return 'text-danger';
        default: return 'text-muted';
    }
}

function showLoadingState() {
    document.getElementById('loading-state').classList.remove('d-none');
    document.getElementById('products-container').classList.add('d-none');
    document.getElementById('empty-state').classList.add('d-none');
}

function hideLoadingState() {
    document.getElementById('loading-state').classList.add('d-none');
    document.getElementById('products-container').classList.remove('d-none');
}

function clearFilters() {
    document.querySelectorAll('.filter-select').forEach(select => {
        select.value = 'all';
        const filterType = select.dataset.filter;
        currentFilters[filterType] = 'all';
    });

    // Reload all products
    applyFilters();
}

// Product management functions
function viewProduct(productId) {
    // TODO: Implement product view modal
    showToast('Product view coming soon!', 'info');
}

function editProduct(productId) {
    // TODO: Implement product edit modal
    showToast('Product edit coming soon!', 'info');
}

function duplicateProduct(productId) {
    // TODO: Implement product duplication
    showToast('Product duplication coming soon!', 'info');
}

function deleteProduct(productId, productName) {
    if (confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
        // Show loading
        const btn = event.target.closest('a');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        btn.style.pointerEvents = 'none';

        fetch(`?delete=${productId}`, {
            method: 'GET'
        })
        .then(response => {
            if (response.ok) {
                showToast('Product deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error('Delete failed');
            }
        })
        .catch(error => {
            console.error('Error deleting product:', error);
            showToast('Error deleting product. Please try again.', 'danger');
            btn.innerHTML = originalHTML;
            btn.style.pointerEvents = 'auto';
        });
    }
}

function exportProducts() {
    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = '../api/export_products.php';
    link.download = 'products_export_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast('Export started! File will download automatically.', 'success');
}

// Load models based on selected brand (for add product modal)
function loadModels(brandId) {
    const modelSelect = document.getElementById('modelSelect');
    modelSelect.innerHTML = '<option value="">Loading...</option>';

    if (!brandId) {
        modelSelect.innerHTML = '<option value="">Select Model (Optional)</option>';
        return;
    }

    fetch(`../api/get_models_by_brand.php?brand_id=${brandId}`)
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Select Model (Optional)</option>';
            if (data.models && data.models.length > 0) {
                data.models.forEach(model => {
                    options += `<option value="${model.id}">${model.model_name}</option>`;
                });
            }
            modelSelect.innerHTML = options;
        })
        .catch(error => {
            console.error('Error loading models:', error);
            modelSelect.innerHTML = '<option value="">Error loading models</option>';
        });
}
</script>

<?php include '../footer.php'; ?>
