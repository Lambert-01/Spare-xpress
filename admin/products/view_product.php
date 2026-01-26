<?php
// Product View Page for SPARE XPRESS LTD
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

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
                         c.category_name,
                         CASE
                             WHEN p.stock_quantity <= p.low_stock_threshold THEN 'low_stock'
                             WHEN p.stock_quantity = 0 THEN 'out_of_stock'
                             ELSE 'in_stock'
                         END as calculated_stock_status
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

// Parse JSON fields
$gallery_images = json_decode($product['gallery_images'] ?? '[]', true) ?: [];
$compatible_models = json_decode($product['compatible_models'] ?? '[]', true) ?: [];
$tags = json_decode($product['tags'] ?? '[]', true) ?: [];
$specifications = json_decode($product['specifications'] ?? '{}', true) ?: [];

// Get related products (same brand/category)
$related_query = "SELECT p.id, p.product_name, p.main_image, p.regular_price, p.sale_price
                  FROM products_enhanced p
                  WHERE p.id != ? AND p.is_active = 1 AND
                        (p.brand_id = ? OR p.category_id = ?) AND p.stock_quantity > 0
                  ORDER BY p.created_at DESC LIMIT 4";
$related_stmt = $conn->prepare($related_query);
$related_stmt->bind_param("iii", $product_id, $product['brand_id'], $product['category_id']);
$related_stmt->execute();
$related_products = $related_stmt->get_result();
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-eye-fill text-primary me-3"></i>
                Product Details
            </h2>
            <p class="text-muted mb-0">Complete product information and specifications</p>
        </div>
        <div class="d-flex gap-2">
            <a href="edit_product.php?id=<?php echo $product_id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil-fill me-1"></i>Edit Product
            </a>
            <a href="enhanced_product_management.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Products
            </a>
        </div>
    </div>

    <!-- Product Status Banner -->
    <div class="alert alert-<?php
        echo match($product['is_active']) {
            1 => 'success',
            default => 'warning'
        };
    ?> mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2 fs-5"></i>
            <div>
                <strong>Product Status:</strong> <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                <?php if ($product['is_featured']): ?>
                    <span class="badge bg-warning text-dark ms-2">
                        <i class="bi bi-star-fill me-1"></i>Featured Product
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Product Images -->
        <div class="col-lg-5">
            <div class="form-card">
                <div class="card-header bg-light border-bottom-0">
                    <h5 class="mb-0">
                        <i class="bi bi-images text-primary me-2"></i>
                        Product Images
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Main Image -->
                    <div class="main-image-container mb-3">
                        <?php if ($product['main_image']): ?>
                            <img src="../<?php echo htmlspecialchars($product['main_image']); ?>"
                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                 class="img-fluid rounded" style="width: 100%; max-height: 400px; object-fit: cover;">
                        <?php else: ?>
                            <div class="text-center p-5 bg-light rounded">
                                <i class="bi bi-image display-1 text-muted"></i>
                                <p class="text-muted mt-2">No main image</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Gallery Images -->
                    <?php if (!empty($gallery_images)): ?>
                        <div class="gallery-images">
                            <h6 class="mb-3">Gallery Images</h6>
                            <div class="row g-2">
                                <?php foreach ($gallery_images as $image): ?>
                                    <div class="col-6 col-md-4">
                                        <img src="../<?php echo htmlspecialchars($image); ?>"
                                             alt="Gallery image"
                                             class="img-fluid rounded cursor-pointer"
                                             style="width: 100%; height: 80px; object-fit: cover;"
                                             onclick="showImageModal('../<?php echo htmlspecialchars($image); ?>')">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        <div class="col-lg-7">
            <div class="form-card">
                <div class="card-header bg-light border-bottom-0">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Product Information
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Basic Info -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold small text-muted">Product Name</label>
                            <h4 class="mb-0"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                            <small class="text-muted">SKU: <?php echo htmlspecialchars($product['sku']); ?></small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Product ID</label>
                            <h5 class="text-primary mb-0">#<?php echo $product['id']; ?></h5>
                        </div>
                    </div>

                    <!-- Classification -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Brand</label>
                            <p class="mb-0"><?php echo htmlspecialchars($product['brand_name'] ?: 'Not specified'); ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Model</label>
                            <p class="mb-0"><?php echo htmlspecialchars($product['model_name'] ?: 'Not specified'); ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted">Category</label>
                            <p class="mb-0"><?php echo htmlspecialchars($product['category_name'] ?: 'Not specified'); ?></p>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="pricing-section mb-4">
                        <label class="form-label fw-semibold small text-muted">Pricing</label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="pricing-card">
                                    <small class="text-muted">Regular Price</small>
                                    <div class="price-amount text-primary fw-bold">
                                        RWF <?php echo number_format($product['regular_price'] ?? 0, 0); ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($product['sale_price'] && $product['sale_price'] < $product['regular_price']): ?>
                                <div class="col-md-4">
                                    <div class="pricing-card">
                                        <small class="text-muted">Sale Price</small>
                                        <div class="price-amount text-danger fw-bold">
                                            RWF <?php echo number_format($product['sale_price'], 0); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="pricing-card">
                                        <small class="text-muted">Savings</small>
                                        <div class="price-amount text-success fw-bold">
                                            RWF <?php echo number_format(($product['regular_price'] - $product['sale_price']), 0); ?>
                                            (<?php echo round((($product['regular_price'] - $product['sale_price']) / $product['regular_price']) * 100, 1); ?>%)
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['wholesale_price']): ?>
                                <div class="col-md-4">
                                    <div class="pricing-card">
                                        <small class="text-muted">Wholesale Price</small>
                                        <div class="price-amount text-info fw-bold">
                                            RWF <?php echo number_format($product['wholesale_price'], 0); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="stock-section mb-4">
                        <label class="form-label fw-semibold small text-muted">Stock & Availability</label>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="stock-card">
                                    <small class="text-muted">Stock Quantity</small>
                                    <div class="stock-amount fw-bold <?php
                                        echo match($product['calculated_stock_status']) {
                                            'in_stock' => 'text-success',
                                            'low_stock' => 'text-warning',
                                            'out_of_stock' => 'text-danger',
                                            default => 'text-muted'
                                        };
                                    ?>">
                                        <?php echo $product['stock_quantity']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stock-card">
                                    <small class="text-muted">Low Stock Threshold</small>
                                    <div class="stock-amount fw-bold text-info">
                                        <?php echo $product['low_stock_threshold']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stock-card">
                                    <small class="text-muted">Stock Status</small>
                                    <div class="stock-amount">
                                        <span class="badge bg-<?php
                                            echo match($product['calculated_stock_status']) {
                                                'in_stock' => 'success',
                                                'low_stock' => 'warning',
                                                'out_of_stock' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $product['calculated_stock_status'])); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stock-card">
                                    <small class="text-muted">Condition</small>
                                    <div class="stock-amount">
                                        <span class="badge bg-primary">
                                            <?php echo ucfirst($product['product_condition']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Settings -->
                    <div class="settings-section">
                        <label class="form-label fw-semibold small text-muted">Product Settings</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <i class="bi bi-<?php echo $product['manage_stock'] ? 'check-circle text-success' : 'x-circle text-danger'; ?> me-2"></i>
                                    <span>Stock Management: <?php echo $product['manage_stock'] ? 'Enabled' : 'Disabled'; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <i class="bi bi-<?php echo $product['backorders_allowed'] ? 'check-circle text-success' : 'x-circle text-danger'; ?> me-2"></i>
                                    <span>Backorders: <?php echo $product['backorders_allowed'] ? 'Allowed' : 'Not Allowed'; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <i class="bi bi-<?php echo $product['is_featured'] ? 'star-fill text-warning' : 'star text-muted'; ?> me-2"></i>
                                    <span>Featured Product: <?php echo $product['is_featured'] ? 'Yes' : 'No'; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <i class="bi bi-eye<?php echo $product['visibility'] === 'public' ? ' text-success' : ' text-muted'; ?> me-2"></i>
                                    <span>Visibility: <?php echo ucfirst(str_replace('_', ' ', $product['visibility'])); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information Tabs -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="form-card">
                <div class="card-header bg-light border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                                <i class="bi bi-file-text me-1"></i>Description
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab">
                                <i class="bi bi-gear me-1"></i>Specifications
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="warranty-tab" data-bs-toggle="tab" data-bs-target="#warranty" type="button" role="tab">
                                <i class="bi bi-shield-check me-1"></i>Warranty
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">
                                <i class="bi bi-graph-up me-1"></i>Analytics
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="mb-3">Product Description</h6>
                                    <?php if ($product['description']): ?>
                                        <div class="product-description">
                                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted">No detailed description available.</p>
                                    <?php endif; ?>

                                    <?php if ($product['short_description']): ?>
                                        <h6 class="mt-4 mb-2">Short Description</h6>
                                        <div class="product-short-description">
                                            <?php echo nl2br(htmlspecialchars($product['short_description'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-3">Product Tags</h6>
                                    <?php if (!empty($tags)): ?>
                                        <div class="tags-container">
                                            <?php foreach ($tags as $tag): ?>
                                                <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted small">No tags assigned.</p>
                                    <?php endif; ?>

                                    <h6 class="mt-4 mb-3">Compatible Models</h6>
                                    <?php if (!empty($compatible_models)): ?>
                                        <div class="compatible-models">
                                            <small class="text-muted">This product is compatible with:</small>
                                            <ul class="list-unstyled mt-2">
                                                <?php foreach ($compatible_models as $model): ?>
                                                    <li><i class="bi bi-check-circle text-success me-1"></i><?php echo htmlspecialchars($model); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted small">No specific model compatibility defined.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specifications" role="tabpanel">
                            <?php if (!empty($specifications)): ?>
                                <div class="specifications-grid">
                                    <?php foreach ($specifications as $key => $value): ?>
                                        <div class="spec-item">
                                            <div class="spec-label"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?>:</div>
                                            <div class="spec-value"><?php echo htmlspecialchars($value); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-gear text-muted fs-1 mb-2"></i>
                                    <p class="text-muted">No technical specifications available.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Warranty Tab -->
                        <div class="tab-pane fade" id="warranty" role="tabpanel">
                            <div class="warranty-info">
                                <?php if ($product['warranty_period'] || $product['warranty_type'] || $product['warranty_details']): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="warranty-item mb-3">
                                                <label class="form-label fw-semibold small text-muted">Warranty Period</label>
                                                <p class="mb-0"><?php echo htmlspecialchars($product['warranty_period'] ?: 'Not specified'); ?></p>
                                            </div>
                                            <div class="warranty-item mb-3">
                                                <label class="form-label fw-semibold small text-muted">Warranty Type</label>
                                                <p class="mb-0"><?php echo htmlspecialchars(ucfirst($product['warranty_type'] ?: 'Not specified')); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="warranty-item">
                                                <label class="form-label fw-semibold small text-muted">Warranty Details</label>
                                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($product['warranty_details'] ?: 'No additional warranty details provided.')); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-shield-x text-muted fs-1 mb-2"></i>
                                        <p class="text-muted">No warranty information available.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Analytics Tab -->
                        <div class="tab-pane fade" id="analytics" role="tabpanel">
                            <div class="analytics-section">
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <div class="analytics-card text-center">
                                            <div class="analytics-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                                                <i class="bi bi-eye fs-2"></i>
                                            </div>
                                            <div class="analytics-value"><?php echo number_format($product['view_count'] ?? 0); ?></div>
                                            <div class="analytics-label">Total Views</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="analytics-card text-center">
                                            <div class="analytics-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                                                <i class="bi bi-cart-check fs-2"></i>
                                            </div>
                                            <div class="analytics-value"><?php echo number_format($product['sales_count'] ?? 0); ?></div>
                                            <div class="analytics-label">Total Sales</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="analytics-card text-center">
                                            <div class="analytics-icon bg-info bg-opacity-10 text-info mx-auto mb-2">
                                                <i class="bi bi-graph-up fs-2"></i>
                                            </div>
                                            <div class="analytics-value">
                                                <?php
                                                $conversion_rate = ($product['view_count'] ?? 0) > 0 ?
                                                    round((($product['sales_count'] ?? 0) / ($product['view_count'] ?? 1)) * 100, 1) : 0;
                                                echo $conversion_rate . '%';
                                                ?>
                                            </div>
                                            <div class="analytics-label">Conversion Rate</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="analytics-card text-center">
                                            <div class="analytics-icon bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                                                <i class="bi bi-calendar-event fs-2"></i>
                                            </div>
                                            <div class="analytics-value">
                                                <?php
                                                $days_since_created = round((time() - strtotime($product['created_at'])) / (60 * 60 * 24));
                                                echo $days_since_created;
                                                ?>
                                            </div>
                                            <div class="analytics-label">Days Listed</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if ($related_products->num_rows > 0): ?>
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="form-card">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="mb-0">
                            <i class="bi bi-diagram-3 text-success me-2"></i>
                            Related Products
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php while ($related = $related_products->fetch_assoc()): ?>
                                <div class="col-md-3">
                                    <div class="related-product-card">
                                        <?php if ($related['main_image']): ?>
                                            <img src="../<?php echo htmlspecialchars($related['main_image']); ?>"
                                                 alt="<?php echo htmlspecialchars($related['product_name']); ?>"
                                                 class="img-fluid rounded mb-2"
                                                 style="width: 100%; height: 120px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded mb-2 d-flex align-items-center justify-content-center"
                                                 style="width: 100%; height: 120px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <h6 class="mb-1 small"><?php echo htmlspecialchars(substr($related['product_name'], 0, 40)); ?>...</h6>
                                        <div class="price text-primary fw-bold small">
                                            <?php if ($related['sale_price'] && $related['sale_price'] < $related['regular_price']): ?>
                                                RWF <?php echo number_format($related['sale_price'], 0); ?>
                                            <?php else: ?>
                                                RWF <?php echo number_format($related['regular_price'] ?? 0, 0); ?>
                                            <?php endif; ?>
                                        </div>
                                        <a href="view_product.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img id="modalImage" src="" alt="Product Image" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<style>
.pricing-card, .stock-card, .setting-item, .analytics-card {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
}

.price-amount {
    font-size: 1.1rem;
}

.stock-amount {
    font-size: 1.1rem;
}

.setting-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
}

.specifications-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.spec-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    background: #f8f9fa;
}

.spec-label {
    font-weight: 600;
    color: #495057;
}

.spec-value {
    color: #007bff;
    font-weight: 500;
}

.analytics-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.analytics-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #495057;
}

.analytics-label {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.related-product-card {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #fff;
    transition: box-shadow 0.3s ease;
}

.related-product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.cursor-pointer {
    cursor: pointer;
}
</style>

<script>
function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

<?php include '../footer.php'; ?>