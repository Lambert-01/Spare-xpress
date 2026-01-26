<?php
// Product Edit Page for SPARE XPRESS LTD
ob_start();
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';
require_once '../logs/error_log.php';

// Get product ID
$product_id = (int)($_GET['id'] ?? 0);
if (!$product_id) {
    header('Location: enhanced_product_management.php');
    exit;
}

// Fetch product details
$product_query = "SELECT p.*,
                         b.brand_name,
                         m.model_name,
                         c.category_name
                  FROM products_enhanced p
                  LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
                  LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
                  LEFT JOIN categories_enhanced c ON p.category_id = c.id
                  WHERE p.id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    header('Location: enhanced_product_management.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_name = trim($_POST['product_name']);
    $slug = generateSlug($product_name);
    $sku = trim($_POST['sku']) ?: generateSKU();
    $brand_id = (int)$_POST['brand_id'];
    $model_id = !empty($_POST['model_id']) ? (int)$_POST['model_id'] : null;
    $category_id = (int)$_POST['category_id'];
    $description = !empty($_POST['description']) ? trim($_POST['description']) : '';
    $short_description = !empty($_POST['short_description']) ? trim($_POST['short_description']) : '';

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
    $warranty_period = !empty($_POST['warranty_period']) ? trim($_POST['warranty_period']) : '';
    $warranty_type = $_POST['warranty_type'];
    $warranty_details = !empty($_POST['warranty_details']) ? trim($_POST['warranty_details']) : '';

    // Compatibility and tags
    $compatible_models = isset($_POST['compatible_models']) ? json_encode(array_map('trim', explode(',', $_POST['compatible_models']))) : json_encode([]);
    $tags = isset($_POST['tags']) ? json_encode(array_map('trim', explode(',', $_POST['tags']))) : json_encode([]);
    $specifications = !empty($_POST['specifications']) ? trim($_POST['specifications']) : '';

    // SEO
    $seo_title = !empty($_POST['seo_title']) ? trim($_POST['seo_title']) : '';
    $seo_description = !empty($_POST['seo_description']) ? trim($_POST['seo_description']) : '';
    $meta_keywords = !empty($_POST['meta_keywords']) ? trim($_POST['meta_keywords']) : '';

    // Handle main image upload
    $main_image = $product['main_image']; // Keep existing
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/products/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $file_name = $slug . '_main.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $target_path)) {
            $main_image = 'uploads/products/' . $file_name;
        }
    }

    // Handle gallery images
    $gallery_images = json_decode($product['gallery_images'] ?? '[]', true) ?: [];
    if (isset($_FILES['gallery_images'])) {
        $new_gallery_images = [];
        for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
            if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION);
                $file_name = $slug . '_gallery_' . ($i + 1) . '.' . $file_extension;
                $target_path = $upload_dir . $file_name;

                if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $target_path)) {
                    $new_gallery_images[] = 'uploads/products/' . $file_name;
                }
            }
        }
        if (!empty($new_gallery_images)) {
            $gallery_images = $new_gallery_images; // Replace with new images
        }
    }
    $gallery_json = json_encode($gallery_images);

    // Update product
    $update_query = "UPDATE products_enhanced SET
        product_name = ?, slug = ?, sku = ?, brand_id = ?, model_id = ?, category_id = ?,
        description = ?, short_description = ?, regular_price = ?, sale_price = ?, wholesale_price = ?,
        stock_quantity = ?, stock_status = ?, low_stock_threshold = ?, manage_stock = ?, backorders_allowed = ?,
        product_condition = ?, is_featured = ?, is_active = ?, visibility = ?,
        warranty_period = ?, warranty_type = ?, warranty_details = ?, main_image = ?, gallery_images = ?,
        compatible_models = ?, tags = ?, specifications = ?,
        seo_title = ?, seo_description = ?, meta_keywords = ?, updated_at = NOW()
        WHERE id = ?";

    // Prepare the update statement
    $stmt = $conn->prepare($update_query);
    
    // Build type string dynamically to handle NULL values
    $types = [];
    $params = [
        &$product_name, &$slug, &$sku, &$brand_id, &$model_id, &$category_id,
        &$description, &$short_description, &$regular_price, &$sale_price, &$wholesale_price,
        &$stock_quantity, &$stock_status, &$low_stock_threshold, &$manage_stock, &$backorders_allowed,
        &$product_condition, &$is_featured, &$is_active, &$visibility,
        &$warranty_period, &$warranty_type, &$warranty_details, &$main_image, &$gallery_json,
        &$compatible_models, &$tags, &$specifications,
        &$seo_title, &$seo_description, &$meta_keywords, &$product_id
    ];
    
    // Define types for each parameter
    $type_definitions = [
        's', 's', 's', 'i',
        $model_id === null ? 's' : 'i', // model_id can be NULL
        'i', 's', 'd', 'd', 'd', 'i', 's', 'i', 'i', 'i', 's', 'i', 'i', 's', 's',
        's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 's', 'i'
    ];
    
    $type_string = implode('', $type_definitions);
     
    // Bind parameters with correct types
    $stmt->bind_param($type_string, ...$params);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Product updated successfully!';
        header("Location: view_product.php?id=$product_id");
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update product: ' . $conn->error;
    }
}

// Get brands and categories for dropdowns
$brands_query = $conn->query("SELECT id, brand_name FROM vehicle_brands_enhanced WHERE is_active = 1 ORDER BY brand_name");
$brands = [];
while ($brand = $brands_query->fetch_assoc()) {
    $brands[] = $brand;
}

$categories_query = $conn->query("SELECT id, category_name FROM categories_enhanced WHERE is_active = 1 ORDER BY category_name");
$categories = [];
while ($category = $categories_query->fetch_assoc()) {
    $categories[] = $category;
}

// Parse JSON fields for form
$gallery_images = json_decode($product['gallery_images'] ?? '[]', true) ?: [];
$compatible_models = json_decode($product['compatible_models'] ?? '[]', true) ?: [];
$tags = json_decode($product['tags'] ?? '[]', true) ?: [];

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function generateSKU() {
    return 'SPX-' . date('Y') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-pencil-fill text-warning me-3"></i>
                Edit Product
            </h2>
            <p class="text-muted mb-0">Modify product information and settings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="view_product.php?id=<?php echo $product_id; ?>" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i>View Product
            </a>
            <a href="enhanced_product_management.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Products
            </a>
        </div>
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

    <form method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            Basic Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Product Name *</label>
                                <input type="text" class="form-control" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" class="form-control" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" placeholder="Auto-generated if empty">
                            </div>

                            <!-- Brand, Model, Category -->
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Brand *</label>
                                <select class="form-select" name="brand_id" required onchange="loadModels(this.value)">
                                    <option value="">Select Brand</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?php echo $brand['id']; ?>" <?php echo $brand['id'] == $product['brand_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Model</label>
                                <select class="form-select" name="model_id" id="modelSelect">
                                    <option value="">Select Model (Optional)</option>
                                    <!-- Models will be loaded via AJAX -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Category *</label>
                                <select class="form-select" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Descriptions -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Short Description</label>
                                <input type="text" class="form-control" name="short_description" value="<?php echo htmlspecialchars($product['short_description'] ?? ''); ?>" placeholder="Brief product description...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Description</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Detailed product description..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-cash text-success me-2"></i>
                            Pricing
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Regular Price (RWF)</label>
                                <input type="number" class="form-control" name="regular_price" step="0.01" min="0" value="<?php echo $product['regular_price'] ?? ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sale Price (RWF)</label>
                                <input type="number" class="form-control" name="sale_price" step="0.01" min="0" value="<?php echo $product['sale_price'] ?? ''; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Wholesale Price (RWF)</label>
                                <input type="number" class="form-control" name="wholesale_price" step="0.01" min="0" value="<?php echo $product['wholesale_price'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Management -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-box-seam text-warning me-2"></i>
                            Stock Management
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Stock Quantity</label>
                                <input type="number" class="form-control" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Low Stock Threshold</label>
                                <input type="number" class="form-control" name="low_stock_threshold" value="<?php echo $product['low_stock_threshold']; ?>" min="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Stock Status</label>
                                <select class="form-select" name="stock_status">
                                    <option value="in_stock" <?php echo $product['stock_status'] === 'in_stock' ? 'selected' : ''; ?>>In Stock</option>
                                    <option value="out_of_stock" <?php echo $product['stock_status'] === 'out_of_stock' ? 'selected' : ''; ?>>Out of Stock</option>
                                    <option value="on_backorder" <?php echo $product['stock_status'] === 'on_backorder' ? 'selected' : ''; ?>>On Backorder</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Product Condition</label>
                                <select class="form-select" name="product_condition">
                                    <option value="new" <?php echo $product['product_condition'] === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="used" <?php echo $product['product_condition'] === 'used' ? 'selected' : ''; ?>>Used</option>
                                    <option value="refurbished" <?php echo $product['product_condition'] === 'refurbished' ? 'selected' : ''; ?>>Refurbished</option>
                                </select>
                            </div>

                            <!-- Stock Settings -->
                            <div class="col-12">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="manage_stock" id="manageStock" <?php echo $product['manage_stock'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="manageStock">
                                                Manage Stock
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="backorders_allowed" id="backordersAllowed" <?php echo $product['backorders_allowed'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="backordersAllowed">
                                                Allow Backorders
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-images text-info me-2"></i>
                            Product Images
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Main Product Image</label>
                                <input type="file" class="form-control" name="main_image" accept="image/*">
                                <?php if ($product['main_image']): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Current: <?php echo basename($product['main_image']); ?></small>
                                        <br>
                                        <img src="../<?php echo htmlspecialchars($product['main_image']); ?>" alt="Current main image" class="img-thumbnail mt-1" style="max-width: 100px; max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gallery Images</label>
                                <input type="file" class="form-control" name="gallery_images[]" accept="image/*" multiple>
                                <small class="text-muted">Select multiple images to replace current gallery</small>
                                <?php if (!empty($gallery_images)): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Current gallery: <?php echo count($gallery_images); ?> images</small>
                                        <div class="d-flex gap-1 mt-1 flex-wrap">
                                            <?php foreach ($gallery_images as $image): ?>
                                                <img src="../<?php echo htmlspecialchars($image); ?>" alt="Gallery image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specifications -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-gear text-secondary me-2"></i>
                            Technical Specifications
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Technical Specifications (JSON)</label>
                                <textarea class="form-control" name="specifications" rows="6" placeholder='{"engine": "2.0L Turbo", "power": "180hp", "transmission": "6-speed manual"}'><?php echo htmlspecialchars($product['specifications'] ?? ''); ?></textarea>
                                <small class="text-muted">Enter specifications in JSON format</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-search text-primary me-2"></i>
                            SEO Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">SEO Title</label>
                                <input type="text" class="form-control" name="seo_title" value="<?php echo htmlspecialchars($product['seo_title'] ?? ''); ?>" maxlength="60">
                                <small class="text-muted">Recommended: 50-60 characters</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">SEO Description</label>
                                <textarea class="form-control" name="seo_description" rows="3" maxlength="160"><?php echo htmlspecialchars($product['seo_description'] ?? ''); ?></textarea>
                                <small class="text-muted">Recommended: 150-160 characters</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Meta Keywords</label>
                                <input type="text" class="form-control" name="meta_keywords" value="<?php echo htmlspecialchars($product['meta_keywords'] ?? ''); ?>" placeholder="keyword1, keyword2, keyword3">
                                <small class="text-muted">Comma-separated keywords</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Product Settings -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-sliders text-info me-2"></i>
                            Product Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Visibility</label>
                            <select class="form-select" name="visibility">
                                <option value="public" <?php echo $product['visibility'] === 'public' ? 'selected' : ''; ?>>Public</option>
                                <option value="private" <?php echo $product['visibility'] === 'private' ? 'selected' : ''; ?>>Private</option>
                                <option value="password_protected" <?php echo $product['visibility'] === 'password_protected' ? 'selected' : ''; ?>>Password Protected</option>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="isFeatured">
                                        Featured Product
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="isActive">
                                        Product is Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warranty -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check text-success me-2"></i>
                            Warranty Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warranty Period</label>
                            <input type="text" class="form-control" name="warranty_period" value="<?php echo htmlspecialchars($product['warranty_period'] ?? ''); ?>" placeholder="e.g., 2 years, 50000 km">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warranty Type</label>
                            <select class="form-select" name="warranty_type">
                                <option value="manufacturer" <?php echo $product['warranty_type'] === 'manufacturer' ? 'selected' : ''; ?>>Manufacturer</option>
                                <option value="dealer" <?php echo $product['warranty_type'] === 'dealer' ? 'selected' : ''; ?>>Dealer</option>
                                <option value="none" <?php echo $product['warranty_type'] === 'none' ? 'selected' : ''; ?>>No Warranty</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Warranty Details</label>
                            <textarea class="form-control" name="warranty_details" rows="3" placeholder="Additional warranty information..."><?php echo htmlspecialchars($product['warranty_details'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Tags and Compatibility -->
                <div class="form-card mb-4">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-tags text-warning me-2"></i>
                            Tags & Compatibility
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Tags</label>
                            <input type="text" class="form-control" name="tags" value="<?php echo htmlspecialchars(implode(', ', $tags)); ?>" placeholder="Enter tags separated by commas">
                            <small class="text-muted">Tags help with product organization and search</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Compatible Models</label>
                            <input type="text" class="form-control" name="compatible_models" value="<?php echo htmlspecialchars(implode(', ', $compatible_models)); ?>" placeholder="Enter compatible models separated by commas">
                            <small class="text-muted">Specific vehicle models this part fits</small>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle me-2"></i>Update Product
                            </button>
                            <a href="view_product.php?id=<?php echo $product_id; ?>" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>View Product
                            </a>
                            <a href="enhanced_product_management.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Load models when brand is selected
document.addEventListener('DOMContentLoaded', function() {
    // Load initial models if brand is selected
    const brandSelect = document.querySelector('select[name="brand_id"]');
    if (brandSelect.value) {
        loadModels(brandSelect.value);
    }
});

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
                    const selected = model.id == <?php echo $product['model_id'] ?? 'null'; ?> ? 'selected' : '';
                    options += `<option value="${model.id}" ${selected}>${model.model_name}</option>`;
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

<?php
ob_end_flush();
include '../footer.php';
?>