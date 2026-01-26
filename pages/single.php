<?php
// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    $page_title = 'Invalid Product - SPARE XPRESS LTD';
    include '../includes/header.php';
    include '../includes/navigation.php';
    echo '<div class="container py-5"><div class="alert alert-danger"><h4>Invalid Product ID</h4><p>The product ID provided is invalid. Please go back to the <a href="/pages/shop.php">shop</a> and select a valid product.</p></div></div>';
    include '../includes/footer.php';
    exit;
}

// Include config for database connection
include '../includes/config.php';

// Fetch product details
try {
    $sql = "SELECT p.*, b.brand_name, m.model_name, c.category_name
            FROM products_enhanced p
            LEFT JOIN vehicle_brands_enhanced b ON p.brand_id = b.id
            LEFT JOIN vehicle_models_enhanced m ON p.model_id = m.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $page_title = 'Product Not Found - SPARE XPRESS LTD';
        include '../includes/header.php';
        include '../includes/navigation.php';
        echo '<div class="container py-5"><div class="alert alert-warning"><h4>Product Not Found</h4><p>The product you are looking for does not exist or has been removed. Please go back to the <a href="/pages/shop.php">shop</a> and browse our available products.</p></div></div>';
        include '../includes/footer.php';
        exit;
    }

    $product = $result->fetch_assoc();
    $stmt->close();

    // Format image path
    $image_path = $product['main_image'] ?? null;
    if (!empty($image_path)) {
        if (!str_starts_with($image_path, '/admin/')) {
            $image_path = '/admin/' . ltrim($image_path, '/');
        }
    }

    // Determine stock status
    if ($product['stock_quantity'] > 5) {
        $stock_status = 'In Stock';
        $stock_class = 'bg-success';
    } elseif ($product['stock_quantity'] > 0) {
        $stock_status = 'Low Stock';
        $stock_class = 'bg-warning text-dark';
    } else {
        $stock_status = 'Special Order (50% Deposit Required)';
        $stock_class = 'bg-info';
    }

} catch (Exception $e) {
    $page_title = 'Error - SPARE XPRESS LTD';
    include '../includes/header.php';
    include '../includes/navigation.php';
    echo '<div class="container py-5"><div class="alert alert-danger"><h4>Database Error</h4><p>There was an error loading the product. Please try again later or contact support. <a href="/pages/shop.php">Back to Shop</a></p></div></div>';
    include '../includes/footer.php';
    exit;
}

$page_title = 'Product Details - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';
include '../includes/wishlist.php';
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="display-4 text-dark fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h1>
                <nav aria-label="breadcrumb" class="wow fadeInUp" data-wow-delay="0.3s">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/pages/shop.php">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Product Details Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Product Images -->
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="product-images">
                    <div class="main-image-container mb-3">
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                              class="img-fluid rounded shadow main-product-image" id="mainProductImage" onerror="this.src='/img/no-image.png'">
                        <div class="image-overlay">
                            <button class="btn btn-light btn-zoom" onclick="zoomImage('<?php echo addslashes($image_path); ?>')" title="Zoom Image">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Image Gallery Thumbnails -->
                    <div class="image-gallery">
                        <div class="gallery-thumbnails">
                            <div class="thumbnail-item active" data-image="<?php echo $image_path; ?>">
                                <img src="<?php echo $image_path; ?>" alt="Main image" class="thumbnail-img" onerror="this.src='/img/no-image.png'">
                            </div>
                            <?php
                            $gallery_images = json_decode($product['gallery_images'] ?? '[]', true) ?: [];
                            foreach ($gallery_images as $gallery_image) {
                                if (!empty($gallery_image)) {
                                    $gallery_path = $gallery_image;
    if (!str_starts_with($gallery_path, '/admin/')) {
        $gallery_path = '/admin/' . ltrim($gallery_path, '/');
    }
                                    echo '<div class="thumbnail-item" data-image="' . htmlspecialchars($gallery_path) . '">';
                                    echo '<img src="' . htmlspecialchars($gallery_path) . '" alt="Gallery image" class="thumbnail-img" onerror="this.src=\'/img/no-image.png\'">';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="product-info">
                    <h2 class="mb-3"><?php echo htmlspecialchars($product['product_name']); ?></h2>

                    <!-- Product Meta -->
                    <div class="product-meta mb-3">
                        <?php if ($product['brand_name']): ?>
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($product['brand_name']); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($product['model_name']): ?>
                            <span class="badge bg-secondary me-2">
                                <i class="fas fa-car me-1"></i><?php echo htmlspecialchars($product['model_name']); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($product['category_name']): ?>
                            <span class="badge bg-info">
                                <i class="fas fa-cogs me-1"></i><?php echo htmlspecialchars($product['category_name']); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Price -->
                    <div class="product-price mb-3">
                        <h3 class="text-primary fw-bold">RWF <?php echo number_format($product['regular_price'], 0, '.', ','); ?></h3>
                    </div>

                    <!-- Stock Status -->
                    <div class="stock-status mb-3">
                        <span class="badge <?php echo $stock_class; ?> fs-6 px-3 py-2">
                            <i class="fas fa-warehouse me-1"></i><?php echo $stock_status; ?>
                        </span>
                        <?php if ($product['stock_quantity'] > 0): ?>
                            <small class="text-muted ms-2">(<?php echo $product['stock_quantity']; ?> available)</small>
                        <?php endif; ?>
                    </div>

                    <!-- Year Compatibility -->
                    <?php if (!empty($product['year_from']) && !empty($product['year_to'])): ?>
                        <div class="compatibility mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                Compatible Years: <?php echo htmlspecialchars($product['year_from']); ?> - <?php echo htmlspecialchars($product['year_to']); ?>
                            </h6>
                        </div>
                    <?php endif; ?>

                    <!-- Quantity Selector -->
                    <div class="quantity-selector mb-4">
                        <label class="form-label fw-bold">Quantity:</label>
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" value="1" min="1"
                                    max="<?php echo $product['stock_quantity'] > 0 ? $product['stock_quantity'] : 99; ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="changeQuantity(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons mb-4">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <button class="btn btn-primary btn-lg w-100" onclick="addToCart(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-outline-danger btn-lg w-100 btn-wishlist"
                                        data-product-id="<?php echo $product['id']; ?>"
                                        onclick="addToWishlist(<?php echo $product['id']; ?>, {
                                            name: '<?php echo addslashes($product['product_name']); ?>',
                                            price: <?php echo $product['regular_price']; ?>,
                                            image: '<?php echo addslashes($image_path); ?>',
                                            brand: '<?php echo addslashes($product['brand_name'] ?? ''); ?>',
                                            model: '<?php echo addslashes($product['model_name'] ?? ''); ?>'
                                        })">
                                    <i class="far fa-heart me-1"></i>
                                    <span class="wishlist-text">Wishlist</span>
                                </button>
                            </div>
                        </div>

                        <!-- Additional Quick Actions -->
                        <div class="quick-actions mt-3">
                            <button class="btn btn-outline-info btn-sm me-2" onclick="shareProduct()">
                                <i class="fas fa-share me-1"></i>Share
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" onclick="compareProduct(<?php echo $product['id']; ?>)">
                                <i class="fas fa-balance-scale me-1"></i>Compare
                            </button>
                        </div>
                    </div>

                    <!-- Key Features -->
                    <div class="key-features mb-4">
                        <h5 class="mb-3">Key Features:</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Genuine quality parts
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Fast delivery across Rwanda
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Professional installation support
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>Warranty included
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description & Specifications -->
        <div class="row mt-5">
            <div class="col-12 wow fadeInUp" data-wow-delay="0.5s">
                <div class="product-details-tabs">
                    <nav>
                        <div class="nav nav-tabs" id="productTabs" role="tablist">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab">
                                <i class="fas fa-info-circle me-1"></i>Description
                            </button>
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab"
                                    data-bs-target="#specifications" type="button" role="tab">
                                <i class="fas fa-list me-1"></i>Specifications
                            </button>
                            <button class="nav-link" id="compatibility-tab" data-bs-toggle="tab"
                                    data-bs-target="#compatibility" type="button" role="tab">
                                <i class="fas fa-car me-1"></i>Compatibility
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                        <!-- Description Tab -->
                        <div class="tab-pane fade show active" id="description" role="tabpanel">
                            <h5>Product Description</h5>
                            <p><?php echo nl2br(htmlspecialchars($product['description'] ?: 'No description available.')); ?></p>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6>What's Included:</h6>
                                    <ul>
                                        <li>Main product component</li>
                                        <li>Installation hardware (if applicable)</li>
                                        <li>Basic installation guide</li>
                                        <li>Warranty information</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Quality Assurance:</h6>
                                    <ul>
                                        <li>Genuine manufacturer parts</li>
                                        <li>Quality tested before shipping</li>
                                        <li>Professional packaging</li>
                                        <li>Safe delivery guarantee</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Tab -->
                        <div class="tab-pane fade" id="specifications" role="tabpanel">
                            <h5>Technical Specifications</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <td><strong>Product Name</strong></td>
                                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                        </tr>
                                        <?php if ($product['brand_name']): ?>
                                        <tr>
                                            <td><strong>Brand</strong></td>
                                            <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($product['model_name']): ?>
                                        <tr>
                                            <td><strong>Model</strong></td>
                                            <td><?php echo htmlspecialchars($product['model_name']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($product['category_name']): ?>
                                        <tr>
                                            <td><strong>Category</strong></td>
                                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong>Price</strong></td>
                                            <td>RWF <?php echo number_format($product['regular_price'], 0, '.', ','); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Stock Status</strong></td>
                                            <td><?php echo $stock_status; ?></td>
                                        </tr>
                                        <?php if (!empty($product['year_from']) && !empty($product['year_to'])): ?>
                                        <tr>
                                            <td><strong>Year Range</strong></td>
                                            <td><?php echo htmlspecialchars($product['year_from']); ?> - <?php echo htmlspecialchars($product['year_to']); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <td><strong>SKU</strong></td>
                                            <td>SPX-<?php echo str_pad($product['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Compatibility Tab -->
                        <div class="tab-pane fade" id="compatibility" role="tabpanel">
                            <h5>Vehicle Compatibility</h5>
                            <div class="compatibility-info">
                                <?php if ($product['brand_name'] || $product['model_name'] || (!empty($product['year_from']) && !empty($product['year_to']))): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        This part is compatible with the following vehicles:
                                    </div>

                                    <div class="row">
                                        <?php if ($product['brand_name']): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-tag fa-2x text-primary mb-2"></i>
                                                    <h6 class="card-title">Brand</h6>
                                                    <p class="card-text"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($product['model_name']): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-car fa-2x text-primary mb-2"></i>
                                                    <h6 class="card-title">Model</h6>
                                                    <p class="card-text"><?php echo htmlspecialchars($product['model_name']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($product['year_from']) && !empty($product['year_to'])): ?>
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-calendar fa-2x text-primary mb-2"></i>
                                                    <h6 class="card-title">Year Range</h6>
                                                    <p class="card-text"><?php echo htmlspecialchars($product['year_from']); ?> - <?php echo htmlspecialchars($product['year_to']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Note:</strong> Please verify your vehicle's exact specifications before purchasing.
                                        Contact our support team if you need assistance with compatibility.
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary">
                                        <i class="fas fa-question-circle me-2"></i>
                                        Compatibility information is being updated. Please contact our support team for assistance.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details End -->

<!-- Related Products Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Related Products</h4>
            <h1 class="mb-0 display-5 wow fadeInUp" data-wow-delay="0.3s">You May Also Like</h1>
        </div>
        <div id="relatedProducts" class="row g-4">
            <!-- Related products will be loaded here -->
        </div>
    </div>
</div>
<!-- Related Products End -->

<style>
/* Enhanced Product Image Gallery */
.product-images {
    position: relative;
}

.main-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.main-product-image {
    width: 100%;
    height: 450px;
    object-fit: contain;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transition: transform 0.3s ease;
    cursor: zoom-in;
}

.main-product-image:hover {
    transform: scale(1.02);
}

.image-overlay {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.main-image-container:hover .image-overlay {
    opacity: 1;
}

.btn-zoom {
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
}

.btn-zoom:hover {
    background: white;
    transform: scale(1.1);
}

/* Image Gallery */
.image-gallery {
    margin-top: 1rem;
}

.gallery-thumbnails {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.thumbnail-item {
    flex: 0 0 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
}

.thumbnail-item.active {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.thumbnail-item:hover {
    border-color: #007bff;
    transform: scale(1.05);
}

.thumbnail-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Enhanced Product Meta */
.product-meta .badge {
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
}

/* Enhanced Price Display */
.product-price h3 {
    font-size: 2.2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

/* Enhanced Stock Status */
.stock-status .badge {
    font-size: 0.9rem;
    padding: 0.6rem 1.2rem;
    border-radius: 25px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Enhanced Quantity Selector */
.quantity-selector {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.quantity-selector .input-group {
    max-width: 180px;
    margin: 0 auto;
}

.quantity-selector .btn {
    border: 2px solid #dee2e6;
    background: white;
    font-weight: 600;
}

.quantity-selector .form-control {
    border-left: none;
    border-right: none;
    font-weight: 600;
    text-align: center;
}

/* Enhanced Action Buttons */
.action-buttons .btn {
    font-weight: 600;
    border-radius: 12px;
    padding: 0.8rem 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.btn-wishlist.active {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.btn-wishlist.active .fa-heart {
    color: white !important;
}

/* Quick Actions */
.quick-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.quick-actions .btn {
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

/* Enhanced Key Features */
.key-features {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #dee2e6;
}

.key-features h5 {
    color: #2d3748;
    font-weight: 700;
    margin-bottom: 1rem;
}

.key-features ul li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
}

.key-features ul li:last-child {
    border-bottom: none;
}

.key-features ul li i {
    margin-right: 0.75rem;
    color: #28a745;
    font-size: 1.1rem;
}

/* Enhanced Tabs */
.product-details-tabs .nav-tabs {
    border: none;
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 12px;
}

.product-details-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    margin-right: 0.25rem;
    transition: all 0.3s ease;
}

.product-details-tabs .nav-link:hover {
    background: rgba(0,123,255,0.1);
    color: #007bff;
}

.product-details-tabs .nav-link.active {
    background: #007bff;
    color: white;
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.tab-content {
    background: white;
    border-radius: 0 0 12px 12px;
    padding: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

/* Enhanced Compatibility Cards */
.compatibility-info .card {
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
}

.compatibility-info .card:hover {
    transform: translateY(-5px);
}

.compatibility-info .card-body {
    text-align: center;
    padding: 2rem 1rem;
}

.compatibility-info .card i {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

/* Enhanced Related Products */
.related-product-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
}

.related-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.related-product-card img {
    transition: transform 0.3s ease;
}

.related-product-card:hover img {
    transform: scale(1.05);
}

/* Image Zoom Modal */
.image-zoom-modal .modal-dialog {
    max-width: 90vw;
    margin: 2rem auto;
}

.image-zoom-modal .modal-body {
    padding: 0;
    text-align: center;
    background: #000;
}

.image-zoom-modal img {
    max-width: 100%;
    max-height: 80vh;
    object-fit: contain;
}

/* Mobile Responsive Enhancements */
@media (max-width: 768px) {
    .main-product-image {
        height: 350px;
    }

    .action-buttons .row > div {
        margin-bottom: 0.5rem;
    }

    .product-price h3 {
        font-size: 1.8rem;
    }

    .quantity-selector {
        padding: 1rem;
    }

    .gallery-thumbnails {
        justify-content: center;
    }

    .thumbnail-item {
        flex: 0 0 70px;
        height: 70px;
    }

    .quick-actions {
        justify-content: center;
    }

    .tab-content {
        padding: 1.5rem 1rem;
    }
}

@media (max-width: 576px) {
    .main-product-image {
        height: 280px;
    }

    .product-details-tabs .nav-link {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    .action-buttons .btn {
        padding: 0.7rem 1rem;
        font-size: 0.95rem;
    }
}

/* Loading States */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

/* Success Animations */
@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.btn-success.added {
    animation: successPulse 0.6s ease;
}
</style>

<script>
// Enhanced Product Page JavaScript
let currentImageIndex = 0;
let productImages = [];

// Initialize product images array
document.addEventListener('DOMContentLoaded', function() {
    // Initialize main image
    const mainImage = document.getElementById('mainProductImage');
    if (mainImage) {
        productImages = [mainImage.src];
    }

    // Load related products
    loadRelatedProducts();

    // Load cart count
    loadCartCount();

    // Initialize image gallery
    initializeImageGallery();

    // Add loading states to buttons
    initializeButtonStates();
});

// Image Gallery Functions
function initializeImageGallery() {
    // Add click handlers to thumbnails
    document.querySelectorAll('.thumbnail-item').forEach((thumb, index) => {
        thumb.addEventListener('click', () => switchImage(index, thumb.dataset.image));
    });
}

function switchImage(index, imageSrc) {
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = document.querySelectorAll('.thumbnail-item');

    if (!mainImage) return;

    // Update active thumbnail
    thumbnails.forEach(thumb => thumb.classList.remove('active'));
    thumbnails[index]?.classList.add('active');

    // Add loading overlay
    showImageLoading();

    // Switch main image
    const newImage = new Image();
    newImage.onload = function() {
        mainImage.src = imageSrc;
        hideImageLoading();
        currentImageIndex = index;
    };
    newImage.onerror = function() {
        mainImage.src = '/img/no-image.png';
        hideImageLoading();
        showErrorToast('Failed to load image');
    };
    newImage.src = imageSrc;
}

function showImageLoading() {
    const container = document.querySelector('.main-image-container');
    if (!container) return;

    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>';
    container.appendChild(overlay);
}

function hideImageLoading() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function zoomImage(imageSrc) {
    // Create zoom modal
    const modalHtml = `
        <div class="modal fade image-zoom-modal" id="imageZoomModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="${imageSrc}" alt="Product Zoom" class="img-fluid" onerror="this.src='/img/no-image.png'">
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('imageZoomModal'));
    modal.show();

    // Remove modal from DOM after hiding
    document.getElementById('imageZoomModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Quantity Selector
function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    const currentValue = parseInt(quantityInput.value);
    const newValue = currentValue + delta;
    const maxValue = parseInt(quantityInput.getAttribute('max'));

    if (newValue >= 1 && newValue <= maxValue) {
        quantityInput.value = newValue;
        updateQuantityDisplay();
    }
}

function updateQuantityDisplay() {
    // Add visual feedback for quantity changes
    const quantityInput = document.getElementById('quantity');
    quantityInput.style.transform = 'scale(1.1)';
    setTimeout(() => {
        quantityInput.style.transform = 'scale(1)';
    }, 150);
}

// Enhanced Add to Cart
function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    const addToCartBtn = document.querySelector('.action-buttons .btn-primary');

    // Show loading state
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
    addToCartBtn.disabled = true;

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('/api/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showSuccessToast(`Added ${quantity} item${quantity > 1 ? 's' : ''} to cart!`, 'Added to Cart');

            // Add success animation
            addToCartBtn.classList.add('added');
            addToCartBtn.innerHTML = '<i class="fas fa-check me-2"></i>Added to Cart!';

            setTimeout(() => {
                addToCartBtn.classList.remove('added');
                addToCartBtn.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
                addToCartBtn.disabled = false;
            }, 2000);

        } else {
            showErrorToast(data.message || 'Failed to add item to cart');
            addToCartBtn.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
            addToCartBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Failed to add item to cart. Please try again.');
        addToCartBtn.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
        addToCartBtn.disabled = false;
    });
}

// Enhanced Wishlist Functions
function addToWishlist(productId, productData = null) {
    if (typeof wishlistManager !== 'undefined') {
        wishlistManager.toggleItem(productId, productData);
    } else {
        // Fallback if wishlist system not loaded
        showInfoToast('Wishlist feature loading...', 'Please wait');
    }
}

// Share Product Function
function shareProduct() {
    const productUrl = window.location.href;
    const productName = '<?php echo addslashes($product['product_name']); ?>';

    if (navigator.share) {
        // Use Web Share API if available
        navigator.share({
            title: productName,
            text: `Check out this product: ${productName}`,
            url: productUrl
        }).catch(console.error);
    } else {
        // Fallback to clipboard
        navigator.clipboard.writeText(productUrl).then(() => {
            showSuccessToast('Product link copied to clipboard!', 'Link Copied');
        }).catch(() => {
            // Final fallback - show share dialog
            const textArea = document.createElement('textarea');
            textArea.value = shareText;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showSuccessToast('Product link copied to clipboard!', 'Link Copied');
        });
    }
}

// Compare Product Function
function compareProduct(productId) {
    // This would integrate with a comparison system
    showInfoToast('Product comparison feature coming soon!', 'Compare Products');
}

// Load Related Products with Enhanced UI
function loadRelatedProducts() {
    const category = '<?php echo $product['category_name']; ?>';
    if (!category) return;

    // Show loading state
    const container = document.getElementById('relatedProducts');
    if (container) {
        container.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading related products...</span></div></div>';
    }

    fetch(`/api/get_products.php?category=${encodeURIComponent(category)}&limit=4`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                renderRelatedProducts(data.products.filter(p => p.id != <?php echo $product['id']; ?>));
            } else {
                renderRelatedProducts([]);
            }
        })
        .catch(error => {
            console.error('Error loading related products:', error);
            renderRelatedProducts([]);
        });
}

// Enhanced Related Products Rendering
function renderRelatedProducts(products) {
    const container = document.getElementById('relatedProducts');

    if (products.length === 0) {
        container.innerHTML = '<div class="col-12 text-center"><div class="text-muted py-4"><i class="fas fa-info-circle fa-2x mb-2"></i><br>No related products found.</div></div>';
        return;
    }

    container.innerHTML = products.slice(0, 4).map(product => `
        <div class="col-lg-3 col-md-6">
            <div class="related-product-card h-100">
                <div class="position-relative overflow-hidden">
                    <img src="${product.image}" alt="${product.name}" class="img-fluid w-100"
                          style="height: 200px; object-fit: cover;" onerror="this.src='/img/no-image.png'">
                    <div class="position-absolute top-0 end-0 p-2">
                        <span class="badge ${getStockBadgeClass(product.stock_status)}">
                            ${product.stock_status}
                        </span>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0 p-3"
                         style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                        <button class="btn btn-light btn-sm me-1" onclick="quickViewProduct(${product.id})" title="Quick View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="addToCartFromRelated(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price})" title="Add to Cart">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="p-3">
                    <h6 class="mb-2 text-truncate" style="font-size: 0.9rem; cursor: pointer;" onclick="viewProduct(${product.id})">
                        ${product.name}
                    </h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-primary fw-bold">RWF ${product.price.toLocaleString()}</span>
                        <button class="btn btn-outline-primary btn-sm" onclick="viewProduct(${product.id})">
                            View
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Quick View for Related Products
function quickViewProduct(productId) {
    // This would open a quick view modal
    viewProduct(productId); // For now, just navigate to product page
}

// Add to Cart from Related Products
function addToCartFromRelated(productId, productName, price) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 1);

    fetch('/api/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showSuccessToast(`${productName} added to cart!`, 'Added to Cart');
        } else {
            showErrorToast(data.message || 'Failed to add item to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorToast('Failed to add item to cart');
    });
}

// Utility Functions
function getStockBadgeClass(status) {
    switch (status) {
        case 'In Stock': return 'bg-success';
        case 'Low Stock': return 'bg-warning text-dark';
        case 'Special Order': return 'bg-info';
        default: return 'bg-secondary';
    }
}

function viewProduct(productId) {
    window.location.href = `/pages/single.php?id=${productId}`;
}

function loadCartCount() {
    fetch('/api/get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
            }
        })
        .catch(error => console.error('Error loading cart:', error));
}

function updateCartCount(count) {
    const cartElements = document.querySelectorAll('.cart-count');
    cartElements.forEach(element => {
        element.textContent = count;
    });
}

function initializeButtonStates() {
    // Add loading states to form submissions
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
            }
        });
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to add to cart
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        const addToCartBtn = document.querySelector('.action-buttons .btn-primary');
        if (addToCartBtn && !addToCartBtn.disabled) {
            addToCart(<?php echo $product['id']; ?>);
        }
    }

    // Escape key to close modals
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            const bsModal = bootstrap.Modal.getInstance(openModal);
            if (bsModal) bsModal.hide();
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>