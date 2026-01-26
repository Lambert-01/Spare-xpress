<?php
$page_title = 'Home';
include 'includes/header.php';
include 'includes/navigation.php';

// Critical CSS for above-the-fold content
$critical_css = "
<style>
/* Critical CSS for hero section */
.hero-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
    padding: 5rem 0;
}
.hero-content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: 700;
}
.hero-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
}
.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .hero-content h1 { font-size: 2rem; }
    .btn { min-height: 44px; padding: 12px 24px; }
    .brand-link, .category-link {
        min-height: 120px;
        touch-action: manipulation;
    }
}

/* Loading skeleton */
.skeleton-item {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    height: 120px;
    border-radius: 8px;
}
@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
";
?>

<!-- Hero Section Start -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-4">
                        <span class="text-primary">Spare Xpress</span><br>
                        <span class="text-dark">Your Auto Parts Store</span>
                    </h1>
                    <p class="lead mb-4 text-muted">
                        Find genuine auto parts for all vehicle brands. Fast delivery across Rwanda with expert support.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="pages/shop.php" class="btn btn-primary btn-lg px-4 py-3">
                            <i class="fas fa-shopping-cart me-2"></i>Browse Parts
                        </a>
                        <a href="pages/brands.php" class="btn btn-info btn-lg px-4 py-3">
                            <i class="fas fa-tags me-2"></i>Browse Brands
                        </a>
                        <a href="pages/order_request.php" class="btn btn-warning btn-lg px-4 py-3">
                            <i class="fas fa-star me-2"></i>Special Orders
                        </a>
                        <a href="#contact" class="btn btn-outline-primary btn-lg px-4 py-3">
                            <i class="fas fa-phone me-2"></i>Contact Us
                        </a>
                    </div>
                </div>
            </div> 
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="img/logo/logox.jpg" alt="Spare Xpress" class="img-fluid rounded shadow" style="max-width: 400px;">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero Section End -->

<!-- Brands Section Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Shop by Brand</h4>
            <h1 class="mb-0 display-3 wow fadeInUp" data-wow-delay="0.3s">Popular Brands in Rwanda</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $rowCount = 0;
            foreach ($brands as $index => $brand) {
                // Determine animation class based on row
                $animationClass = ($rowCount < 3) ? 'wow slideInRight' : 'wow slideInLeft';
                $delay = 0.5 + ($index % 3) * 0.2; // Stagger delays
                $brandClass = ($index < 3) ? 'upper-brand' : 'lower-brand';

                echo '<div class="col-lg-2 col-md-3 col-sm-4 col-6 ' . $animationClass . '" data-wow-delay="' . $delay . 's">';
                echo '<a href="pages/models.php?brand=' . strtolower($brand['slug']) . '" class="d-flex flex-column align-items-center justify-content-center p-4 bg-white rounded shadow-sm brand-link hover-lift ' . $brandClass . '" style="animation-delay: ' . ($index * 0.2) . 's;">';

                // Get brand logo
                $logoSrc = 'img/no-image.png'; // Default fallback
                if (!empty($brand['logo_image'])) {
                    // Clean up the path - remove any leading ../../ or similar
                    $cleanPath = str_replace(['../../', '../'], '', $brand['logo_image']);
                    $fullPath = 'uploads/brands/' . basename($cleanPath);
                    if (file_exists($fullPath)) {
                        $logoSrc = $fullPath;
                    } else {
                        // Fallback to simpleicons
                        $logoSrc = 'https://cdn.simpleicons.org/' . $brand['slug'] . '/007bff';
                    }
                } else {
                    // Special cases for brands not available in SimpleIcons
                    $specialLogos = [
                        'isuzu' => 'https://www.carlogos.org/car-logos/isuzu-logo.png',
                        'lexus' => 'https://www.carlogos.org/car-logos/lexus-logo.png',
                        'land-rover' => 'https://www.carlogos.org/car-logos/land-rover-logo.png',
                        'wuling' => 'https://www.carlogos.org/car-logos/wuling-logo.png'
                    ];

                    if (isset($specialLogos[$brand['slug']])) {
                        $logoSrc = $specialLogos[$brand['slug']];
                    } else {
                        // Try simpleicons for other brands
                        $logoSrc = 'https://cdn.simpleicons.org/' . $brand['slug'] . '/007bff';
                    }

                    if (isset($specialLogos[$brand['slug']])) {
                        $logoSrc = $specialLogos[$brand['slug']];
                    } else {
                        // Try simpleicons with the slug
                        $logoSrc = 'https://cdn.simpleicons.org/' . $brand['slug'] . '/007bff';
                    }
                }

                echo '<img src="' . $logoSrc . '" alt="' . $brand['name'] . ' logo" class="mb-2" style="width: 48px; height: 48px; object-fit: contain;" onerror="this.src=\'img/no-image.png\';">';
                echo '<span class="fw-bold text-primary text-center">' . $brand['name'] . '</span>';
                echo '</a>';
                echo '</div>';

                // Increment row count every 3 items
                if (($index + 1) % 3 == 0) {
                    $rowCount++;
                }
            }
            ?>
        </div>
    </div>
</div>
<!-- Brands Section End -->

<!-- Categories Section Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Shop by Category</h4>
            <h1 class="mb-0 display-3 wow fadeInUp" data-wow-delay="0.3s">Our Stock Categories</h1>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            foreach ($categories as $index => $category) {
                $animationClass = 'wow fadeInUp';
                $delay = 0.1 + ($index * 0.1);

                echo '<div class="col-lg-2 col-md-3 col-sm-4 col-6 ' . $animationClass . '" data-wow-delay="' . $delay . 's">';
                echo '<a href="pages/shop.php?category=' . urlencode($category['slug']) . '" class="d-flex flex-column align-items-center justify-content-center p-4 bg-white rounded shadow-sm category-link hover-lift" style="height: 140px; transition: all 0.3s ease;">';
                echo '<i class="' . $category['icon'] . ' fa-3x text-primary mb-3"></i>';
                echo '<span class="fw-bold text-primary text-center small">' . $category['name'] . '</span>';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</div>
<!-- Categories Section End -->

<!-- Quick Search / Filter Bar Start -->
<div class="container-fluid py-5 bg-gradient-primary">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 text-center mb-4">
                <h2 class="mb-3 text-white">Find Your Perfect Auto Part</h2>
                <p class="text-light mb-4 lead">Search through our extensive catalog of genuine and quality auto parts</p>
            </div>
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4">
                        <form id="quickSearchForm" class="row g-3 align-items-end">
                            <!-- Brand Selection -->
                            <div class="col-lg-2 col-md-6">
                                <label for="brand" class="form-label fw-semibold text-primary">
                                    <i class="fas fa-car me-1"></i>Vehicle Brand
                                </label>
                                <select class="form-select form-select-lg border-primary" id="brand" name="brand">
                                    <option value="">All Brands</option>
                                    <!-- Brands will be loaded dynamically -->
                                </select>
                            </div>

                            <!-- Model Selection -->
                            <div class="col-lg-2 col-md-6">
                                <label for="model" class="form-label fw-semibold text-primary">
                                    <i class="fas fa-car-side me-1"></i>Model
                                </label>
                                <select class="form-select form-select-lg border-primary" id="model" name="model" disabled>
                                    <option value="">Select Brand First</option>
                                    <!-- Models will be loaded dynamically based on brand -->
                                </select>
                            </div>

                            <!-- Year Range -->
                            <div class="col-lg-2 col-md-6">
                                <label for="year_from" class="form-label fw-semibold text-primary">
                                    <i class="fas fa-calendar me-1"></i>Year From
                                </label>
                                <select class="form-select form-select-lg border-primary" id="year_from" name="year_from">
                                    <option value="">Any Year</option>
                                    <!-- Years will be loaded dynamically -->
                                </select>
                            </div>

                            <!-- Category Selection -->
                            <div class="col-lg-2 col-md-6">
                                <label for="category" class="form-label fw-semibold text-primary">
                                    <i class="fas fa-cogs me-1"></i>Part Category
                                </label>
                                <select class="form-select form-select-lg border-primary" id="category" name="category">
                                    <option value="">All Categories</option>
                                    <!-- Categories will be loaded dynamically -->
                                </select>
                            </div>

                            <!-- Search Input -->
                            <div class="col-lg-2 col-md-6">
                                <label for="search" class="form-label fw-semibold text-primary">
                                    <i class="fas fa-search me-1"></i>Part Name/SKU
                                </label>
                                <input type="text" class="form-control form-control-lg border-primary"
                                       id="search" name="search" placeholder="Enter part name or SKU">
                            </div>

                            <!-- Search Button -->
                            <div class="col-lg-2 col-md-6">
                                <button type="submit" class="btn btn-primary btn-lg w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-search me-2"></i>
                                    <span>Search Parts</span>
                                </button>
                            </div>
                        </form>

                        <!-- Advanced Filters Toggle -->
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <button class="btn btn-link text-primary p-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="false">
                                    <i class="fas fa-sliders-h me-1"></i>Advanced Filters
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Advanced Filters (Collapsible) -->
                        <div class="collapse mt-3" id="advancedFilters">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6">
                                    <label for="price_min" class="form-label fw-semibold text-primary">Min Price (RWF)</label>
                                    <input type="number" class="form-control border-primary" id="price_min" name="price_min" placeholder="0">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="price_max" class="form-label fw-semibold text-primary">Max Price (RWF)</label>
                                    <input type="number" class="form-control border-primary" id="price_max" name="price_max" placeholder="1000000">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="availability" class="form-label fw-semibold text-primary">Availability</label>
                                    <select class="form-select border-primary" id="availability" name="availability">
                                        <option value="">Any Availability</option>
                                        <option value="in_stock">In Stock</option>
                                        <option value="special_order">Special Order</option>
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="sort" class="form-label fw-semibold text-primary">Sort By</label>
                                    <select class="form-select border-primary" id="sort" name="sort">
                                        <option value="newest">Newest First</option>
                                        <option value="price_low">Price: Low to High</option>
                                        <option value="price_high">Price: High to Low</option>
                                        <option value="name_az">Name: A to Z</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Results Summary -->
                <div id="searchResults" class="mt-4" style="display: none;">
                    <div class="alert alert-info border-0 rounded-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="resultsText">Found <strong>0</strong> parts matching your criteria</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Quick Search / Filter Bar End -->



<!-- How It Works Section Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">How It Works</h4>
            <h1 class="mb-0 display-3 wow fadeInUp" data-wow-delay="0.3s">Simple Ordering Process</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-search fa-2x text-white"></i>
                    </div>
                    <h5 class="mb-3">Step 1: Search or Request a Part</h5>
                    <p class="mb-0">Use our filters or contact us for rare parts we don't stock</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-credit-card fa-2x text-white"></i>
                    </div>
                    <h5 class="mb-3">Step 2: Confirm & Pay 50%</h5>
                    <p class="mb-0">Pay 50% deposit via mobile money for special orders</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x text-white"></i>
                    </div>
                    <h5 class="mb-3">Step 3: Support Call & Sourcing</h5>
                    <p class="mb-0">Our team verifies fitment and sources your part</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 80px; height: 80px;">
                        <i class="fas fa-truck fa-2x text-white"></i>
                    </div>
                    <h5 class="mb-3">Step 4: Pickup or Delivery</h5>
                    <p class="mb-0">Pay remaining amount and receive your part</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- How It Works Section End -->

<style>
/* Brand Animation Styles */
@keyframes pan-move-right {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(15px); }
}

@keyframes pan-move-left {
    0%, 100% { transform: translateX(0); }
    50% { transform: translateX(-15px); }
}

.upper-brand {
    animation: pan-move-right 4s ease-in-out infinite;
}

.lower-brand {
    animation: pan-move-left 4s ease-in-out infinite;
}

/* Category and Brand Link Styles */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.brand-link, .category-link {
    text-decoration: none;
    border: 1px solid #e9ecef;
}

.brand-link:hover, .category-link:hover {
    border-color: #007bff;
    background-color: #f8f9fa !important;
}

/* Quick Search Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
}

.bg-gradient-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.03"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.02"/><circle cx="10" cy="50" r="0.5" fill="%23ffffff" opacity="0.02"/><circle cx="90" cy="30" r="0.5" fill="%23ffffff" opacity="0.02"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.form-select-lg {
    font-size: 0.95rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.form-select-lg:focus,
.form-control-lg:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
}

.card {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border: 1px solid rgba(0, 123, 255, 0.2);
    color: #0c5460;
}

.btn-link {
    color: #007bff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-link:hover {
    color: #0056b3;
    text-decoration: none;
}

.btn-link:focus {
    box-shadow: none;
}

.collapse-icon {
    transition: transform 0.3s ease;
}

[data-bs-toggle="collapse"].collapsed .collapse-icon {
    transform: rotate(0deg);
}

[data-bs-toggle="collapse"]:not(.collapsed) .collapse-icon {
    transform: rotate(180deg);
}

/* Loading animation */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .form-select-lg,
    .form-control-lg {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }

    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>