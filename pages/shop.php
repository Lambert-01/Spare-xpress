<?php
$page_title = 'Shop Automotive Parts - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';
?>

<!-- Page Header Start - Premium Design -->
<div class="container-fluid bg-mesh-gradient bg-particles py-5 position-relative overflow-hidden" style="min-height: 400px;">
    <!-- Animated background -->
    <div class="position-absolute w-100 h-100 top-0 start-0" style="z-index: 0;">
        <div class="position-absolute" style="top: 20%; right: 10%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); border-radius: 50%; animation: float 7s ease-in-out infinite;"></div>
    </div>

    <div class="container py-4 position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-7 fade-in">
                <!-- Badge -->
                <div class="mb-3">
                    <span class="badge-modern badge-bestseller">
                        <i class="fas fa-shopping-bag me-2"></i>Auto Parts Shopping
                    </span>
                </div>

                <!-- Main Heading -->
                <h1 class="display-4 fw-bold mb-3 text-white">
                    <span class="text-gradient-accent">Premium Auto Parts</span>
                </h1>
                
                <!-- Subheading -->
                <p class="lead text-white mb-4" style="opacity: 0.95; max-width: 600px;">
                    Discover genuine automotive parts for all vehicle brands. Quality parts with expert support and fast delivery across Rwanda.
                </p>
                
                <!-- Trust Indicators - Vibrant Design -->
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="p-3 text-center rounded-xl hover-lift transition h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 mb-2 d-inline-flex">
                                <i class="fas fa-shield-alt fa-lg text-white"></i>
                            </div>
                            <p class="text-white fw-bold mb-1 small">Genuine Parts</p>
                            <small class="text-white d-block" style="opacity: 0.9; font-size: 0.7rem;">OEM & Aftermarket</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 text-center rounded-xl hover-lift transition h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 mb-2 d-inline-flex">
                                <i class="fas fa-truck fa-lg text-white"></i>
                            </div>
                            <p class="text-white fw-bold mb-1 small">Fast Delivery</p>
                            <small class="text-white d-block" style="opacity: 0.9; font-size: 0.7rem;">24-72 Hours</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 text-center rounded-xl hover-lift transition h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 mb-2 d-inline-flex">
                                <i class="fas fa-tools fa-lg text-white"></i>
                            </div>
                            <p class="text-white fw-bold mb-1 small">Expert Support</p>
                            <small class="text-white d-block" style="opacity: 0.9; font-size: 0.7rem;">Professional Advice</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="p-3 text-center rounded-xl hover-lift transition h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);">
                            <div class="bg-white bg-opacity-25 rounded-circle p-2 mb-2 d-inline-flex">
                                <i class="fas fa-certificate fa-lg text-white"></i>
                            </div>
                            <p class="text-white fw-bold mb-1 small">Warranty</p>
                            <small class="text-white d-block" style="opacity: 0.9; font-size: 0.7rem;">Quality Guaranteed</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Image/Logo Column -->
            <div class="col-lg-5 text-center mt-4 mt-lg-0 fade-in">
                <div class="position-relative" data-parallax="0.2">
                    <div class="glass-card p-4 rounded-2xl">
                        <img src="/img/logo/logox.jpg" alt="SPARE XPRESS" class="img-fluid rounded-xl shadow-xl" style="max-width: 280px; filter: brightness(1.1);">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Pattern -->
    <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 200px; z-index: 0;">
        <i class="fas fa-cogs text-white"></i>
    </div>
</div>
<!-- Page Header End -->

<!-- Breadcrumb Navigation -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 shadow-sm">
        <li class="breadcrumb-item">
            <a href="/" class="text-decoration-none">
                <i class="fas fa-home me-1"></i>Home
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-shopping-cart me-1"></i>Shop Auto Parts
        </li>
    </ol>
</nav>

<!-- Shop Section Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <!-- Advanced Search Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 rounded-3 shadow-sm">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-search me-1 text-primary"></i>Search Parts
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg border-end-0"
                                       id="advancedSearchInput" placeholder="Enter part name, SKU, or description...">
                                <button class="btn btn-primary px-4" onclick="applyFilters()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-car me-1 text-primary"></i>Quick Brand Filter
                            </label>
                            <select class="form-select form-select-lg" id="quickBrandFilter" onchange="applyFilters()">
                                <option value="">All Brands</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <button class="btn btn-outline-primary btn-lg w-100" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#advancedFiltersPanel">
                                <i class="fas fa-sliders-h me-2"></i>Advanced Filters
                                <i class="fas fa-chevron-down ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Filters Panel -->
                    <div class="collapse mt-4" id="advancedFiltersPanel">
                        <div class="border-top pt-4">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-dark">Category</label>
                                    <select class="form-select" id="quickCategoryFilter" onchange="applyFilters()">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-dark">Vehicle Model</label>
                                    <select class="form-select" id="quickModelFilter" onchange="applyFilters()">
                                        <option value="">All Models</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label fw-semibold text-dark">Max Price (RWF)</label>
                                    <input type="number" class="form-control" id="quickMaxPrice" placeholder="No limit" onchange="applyFilters()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Products Section -->
            <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                <!-- Sort and View Controls -->
                <div class="bg-white p-3 rounded-3 shadow-sm mb-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <span class="text-muted me-2 small fw-semibold">Sort by:</span>
                                <select class="form-select form-select-sm flex-grow-1" id="sortSelect" onchange="applyFilters()">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="price_low">Price: Low to High</option>
                                    <option value="price_high">Price: High to Low</option>
                                    <option value="name_az">Name: A to Z</option>
                                    <option value="name_za">Name: Z to A</option>
                                    <option value="popular">Most Popular</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div id="resultsInfo" class="text-muted fw-semibold small">
                                <i class="fas fa-spinner fa-spin me-2"></i>Loading products...
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <span class="text-muted small fw-semibold me-2">View:</span>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="gridView" title="Grid View">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="listView" title="List View">
                                        <i class="fas fa-list"></i>
                                    </button>
                                </div>
                                <div class="vr mx-2"></div>
                                <select class="form-select form-select-sm" id="pageSizeSelect" onchange="changePageSize()" style="width: auto;">
                                    <option value="12">12 per page</option>
                                    <option value="24">24 per page</option>
                                    <option value="48">48 per page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recently Viewed Products -->
                <div id="recentlyViewed" class="bg-white p-3 rounded-3 shadow-sm mb-4" style="display: none;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-history me-2 text-primary"></i>Recently Viewed
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearRecentlyViewed()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div id="recentlyViewedContainer" class="d-flex gap-3 overflow-auto pb-2" style="scrollbar-width: thin;"></div>
                </div>

                <!-- Results Info -->
                <div class="mb-4">
                    <div id="resultsInfo" class="results-info text-dark fw-semibold">
                        <i class="fas fa-spinner fa-spin me-2"></i>Loading products...
                    </div>
                </div>

                <!-- Products Grid -->
                <div id="productsContainer" class="row g-4">
                    <!-- Products will be loaded here dynamically -->
                </div>

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="mt-3">
                        <h5 class="text-muted">Loading Products...</h5>
                        <p class="text-muted small">Please wait while we fetch the best parts for you</p>
                    </div>
                </div>

                <!-- No Results -->
                <div id="noResults" class="text-center py-5 bg-white rounded-3 shadow-sm" style="display: none;">
                    <div class="mb-4">
                        <i class="fas fa-search fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">No products found</h4>
                    <p class="text-muted mb-4">Try adjusting your filters or search terms to find what you're looking for.</p>
                    <button class="btn btn-primary px-4" onclick="clearAllFilters()">
                        <i class="fas fa-refresh me-2"></i>Clear All Filters
                    </button>
                </div>

                <!-- Pagination -->
                <div id="paginationContainer" class="d-flex justify-content-center mt-5">
                    <!-- Pagination will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Shop Section End -->

<!-- Featured Products Section Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 fade-in">
            <div class="mb-3">
                <span class="badge-modern badge-new">
                    <i class="fas fa-fire me-2"></i>Featured Parts
                </span>
            </div>
            <h2 class="mb-3 fw-bold">
                <span class="text-gradient">Latest & Popular Parts</span>
            </h2>
            <p class="lead text-muted mb-0" style="max-width: 700px; margin: 0 auto;">
                Discover our most popular and recently added automotive parts - handpicked for quality and reliability
            </p>
        </div>

        <!-- Featured Products Container -->
        <div id="featuredProductsContainer" class="row g-4 wow fadeInUp" data-wow-delay="0.5s">
            <!-- Featured products will be loaded here dynamically -->
        </div>

        <!-- Loading State -->
        <div id="featuredLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading featured products...</span>
            </div>
            <p class="mt-3 text-muted">Loading featured products...</p>
        </div>

        <!-- View All Products Button -->
        <div class="text-center mt-5">
            <a href="#productsContainer" class="btn btn-primary btn-lg px-5 py-3">
                <i class="fas fa-th-large me-2"></i>View All Products
            </a>
        </div>
    </div>
</div>
<!-- Featured Products Section End -->

<!-- Trust Signals Section - Premium -->
<div class="container-fluid py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-2 col-md-4 col-6 fade-in">
                <div class="glass-card p-4 text-center hover-lift transition rounded-xl h-100">
                    <div class="mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex">
                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Genuine Parts</h6>
                    <small class="text-muted">100% Authentic</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 fade-in">
                <div class="glass-card p-4 text-center hover-lift transition rounded-xl h-100">
                    <div class="mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex">
                            <i class="fas fa-truck fa-2x text-success"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Fast Delivery</h6>
                    <small class="text-muted">24-72 Hours</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 fade-in">
                <div class="glass-card p-4 text-center hover-lift transition rounded-xl h-100">
                    <div class="mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex">
                            <i class="fas fa-tools fa-2x text-info"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Expert Support</h6>
                    <small class="text-muted">Professional Team</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 fade-in">
                <div class="glass-card p-4 text-center hover-lift transition rounded-xl h-100">
                    <div class="mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex">
                            <i class="fas fa-certificate fa-2x text-warning"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">Warranty</h6>
                    <small class="text-muted">Quality Guaranteed</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6 fade-in">
                <div class="glass-card p-4 text-center hover-lift transition rounded-xl h-100">
                    <div class="mb-3">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-flex">
                            <i class="fas fa-headset fa-2x text-danger"></i>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">24/7 Support</h6>
                    <small class="text-muted">Always Available</small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Trust Signals End -->

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="quickViewModalLabel">
                    <i class="fas fa-eye me-2"></i>Product Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="quickViewContent">
                    <!-- Full product details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-primary" id="quickViewAddToCart">
                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product Comparison Modal -->
<div class="modal fade" id="comparisonModal" tabindex="-1" aria-labelledby="comparisonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="comparisonModalLabel">
                    <i class="fas fa-balance-scale me-2"></i>Compare Products
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="comparisonContent">
                    <div class="text-center py-5">
                        <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No products to compare</h5>
                        <p class="text-muted">Add products to comparison by clicking the compare button on product cards.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-info" onclick="clearComparison()">
                    <i class="fas fa-trash me-1"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Comparison Button -->
<div id="floatingComparisonBtn" class="position-fixed bottom-0 end-0 m-4" style="display: none; z-index: 1050;">
    <button class="btn btn-info btn-lg rounded-circle shadow-lg" onclick="showComparisonModal()" title="Compare Products">
        <i class="fas fa-balance-scale"></i>
        <span id="comparisonBadge" class="badge bg-danger rounded-circle position-absolute top-0 start-100 translate-middle" style="display: none;">0</span>
    </button>
</div>

<style>
/* Enhanced Styling */
.page-header {
    min-height: 400px;
}


/* Enhanced Product Card Styles */
.product-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e9ecef;
    border-radius: 1rem;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    position: relative;
}

.product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.product-image-container {
    position: relative;
    height: 240px;
    overflow: hidden;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 1.5rem;
    transition: transform 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.08);
}

.product-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.product-badge {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.badge-new { background: linear-gradient(135deg, #ff6b6b, #ee5a24); color: white; }
.badge-bestseller { background: linear-gradient(135deg, #ffd93d, #ff8c42); color: #333; }
.badge-sale { background: linear-gradient(135deg, #ff4757, #ff3838); color: white; }
.badge-low-stock { background: linear-gradient(135deg, #ffa726, #fb8c00); color: white; }

.stock-badge {
    font-size: 0.7rem;
    padding: 0.4rem 0.8rem;
    border-radius: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0, 123, 255, 0.95) 0%, rgba(102, 126, 234, 0.95) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 2;
    backdrop-filter: blur(2px);
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.quick-actions {
    display: flex;
    gap: 0.75rem;
    flex-direction: column;
}

.quick-actions .btn {
    border: 2px solid rgba(255,255,255,0.3);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.quick-actions .btn:hover {
    border-color: white;
    transform: scale(1.05);
}

.product-info {
    padding: 1.75rem;
}

.product-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    min-height: 2.8rem;
}

.product-meta {
    font-size: 0.85rem;
    color: #4a5568;
    margin-bottom: 1.25rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.product-meta-item {
    display: inline-flex;
    align-items: center;
    background: #f7fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.product-meta-item i {
    margin-right: 0.25rem;
    color: #007bff;
}

.product-price {
    font-size: 1.4rem;
    font-weight: 800;
    color: #007bff;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.price-currency {
    font-size: 0.9rem;
    font-weight: 600;
    color: #718096;
}

.delivery-info {
    font-size: 0.8rem;
    color: #38a169;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.delivery-info i {
    margin-right: 0.375rem;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: auto;
}

.btn-quick-view, .btn-add-cart {
    flex: 1;
    font-weight: 600;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-quick-view {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.btn-quick-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.btn-add-cart {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    color: white;
}

.btn-add-cart:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
}

.btn-wishlist {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.btn-wishlist:hover {
    background: #e9ecef;
    border-color: #dc3545;
    color: #dc3545;
    transform: scale(1.1);
}

.btn-compare {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    color: #6c757d;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.btn-compare:hover {
    background: #e9ecef;
    border-color: #007bff;
    color: #007bff;
    transform: scale(1.1);
}

.btn-compare.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

/* Recently Viewed Styles */
#recentlyViewedContainer .recently-viewed-item {
    flex: 0 0 120px;
    background: white;
    border-radius: 0.5rem;
    padding: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #e9ecef;
}

#recentlyViewedContainer .recently-viewed-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

#recentlyViewedContainer .recently-viewed-item img {
    width: 100%;
    height: 80px;
    object-fit: contain;
    margin-bottom: 0.5rem;
}

#recentlyViewedContainer .recently-viewed-item .item-title {
    font-size: 0.75rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.2;
    height: 1.8rem;
}

#recentlyViewedContainer .recently-viewed-item .item-price {
    font-size: 0.8rem;
    font-weight: 700;
    color: #007bff;
}

/* Floating Comparison Button */
#floatingComparisonBtn {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Advanced Search Enhancements */
.input-group .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.input-group .btn {
    border-left: none;
}

.input-group .btn:focus {
    box-shadow: none;
}

/* Loading States */
.loading-skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Custom Pagination Styles */
.pagination-wrapper {
    display: flex;
    align-items: center;
    flex-wrap: nowrap;
    gap: 0.25rem;
}

.pagination-wrapper .btn {
    min-width: 35px;
    height: 35px;
    padding: 0.25rem 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
}

.pagination-wrapper .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination-wrapper .text-muted {
    font-size: 0.875rem;
    user-select: none;
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .product-card {
        margin-bottom: 1.5rem;
    }

    #floatingComparisonBtn {
        margin: 1rem;
    }

    .quick-actions {
        flex-direction: row;
        gap: 0.25rem;
    }

    .quick-actions .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }

    .product-title {
        font-size: 0.9rem;
        min-height: 2.4rem;
    }

    .product-price {
        font-size: 1.2rem;
    }
}

/* Trust Signals */
.trust-badges {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.trust-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    font-weight: 600;
    color: #2d3748;
}

.trust-badge i {
    color: #007bff;
    font-size: 1.25rem;
}

/* Enhanced Results Info */
.results-info {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid #007bff;
}

/* Comparison Modal Enhancements */
#comparisonContent .table th {
    vertical-align: middle;
    font-weight: 600;
}

#comparisonContent .table td {
    vertical-align: middle;
}

/* Recently Viewed Enhancements */
#recentlyViewed {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-radius: 0.75rem;
    padding: 1.25rem;
}

/* Search Suggestions (for future enhancement) */
.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
}

.search-suggestion-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-suggestion-item:hover {
    background-color: #f8f9fa;
}

.search-suggestion-item:last-child {
    border-bottom: none;
}

.btn-quick-view {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-quick-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-add-cart {
    flex: 1;
    background: #28a745;
    border: none;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-add-cart:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
}

.pagination .page-link {
    color: #007bff;
    border-color: #dee2e6;
    font-weight: 500;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Quick View Modal Enhancements */
.modal-content {
    border: none;
    border-radius: 1rem;
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.quick-view-image {
    width: 100%;
    height: 300px;
    object-fit: contain;
    background: #f8f9fa;
    padding: 2rem;
}

.quick-view-details {
    padding: 2rem;
}

.quick-view-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.quick-view-meta {
    margin-bottom: 1.5rem;
}

.quick-view-meta .badge {
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

.quick-view-price {
    font-size: 2rem;
    font-weight: 700;
    color: #007bff;
    margin-bottom: 1.5rem;
}

.quick-view-description {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 2rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .page-header {
        min-height: 300px;
        padding: 2rem 0;
    }

    .filter-options {
        max-height: 200px;
    }

    .product-image-container {
        height: 180px;
    }

    .product-info {
        padding: 1rem;
    }

    .product-title {
        font-size: 0.9rem;
    }

    .product-price {
        font-size: 1.1rem;
    }

    .product-actions {
        flex-direction: column;
    }

    .btn-quick-view,
    .btn-add-cart {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .product-card {
        margin-bottom: 1rem;
    }

}

/* Price Styling */
.price-sale {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.original-price {
    font-size: 0.875rem;
}

.sale-price {
    font-size: 1.1rem;
}

/* Carousel in Product Cards */
.product-image-container .carousel {
    height: 240px;
}

.product-image-container .carousel-item img {
    height: 240px;
    object-fit: contain;
    padding: 1.5rem;
}

/* Loading Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.wow {
    animation-duration: 0.6s;
    animation-fill-mode: both;
}

.fadeInUp {
    animation-name: fadeInUp;
}

</style>

<script>
// Global variables
let currentPage = 1;
let currentFilters = {};
let currentSort = 'newest';
let totalPages = 1;
let isLoading = false;
let currentQuickViewProduct = null;
let currentPageSize = 12;
let recentlyViewed = [];
let comparisonList = [];

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadSavedPageSize();
    loadFilters();
    loadProducts();
    loadFeaturedProducts();
    loadRecentlyViewed();
    loadComparisonList();

    // Check for URL parameters and apply filters
    checkUrlParameters();

    // View toggle
    document.getElementById('gridView').addEventListener('click', function() {
        setViewMode('grid');
    });

    document.getElementById('listView').addEventListener('click', function() {
        setViewMode('list');
    });

    // Advanced search with autocomplete
    const searchInput = document.getElementById('advancedSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(handleSearchInput, 300));
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }

    // Quick filter listeners
    ['quickCategoryFilter', 'quickModelFilter', 'quickMaxPrice'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', applyFilters);
        }
    });

    // Special handling for brand filter to update models
    const brandFilter = document.getElementById('quickBrandFilter');
    if (brandFilter) {
        brandFilter.addEventListener('change', function() {
            updateModelsForBrand();
            applyFilters();
        });
    }
});

// Global filter data
let allBrands = [];
let allModels = [];
let allCategories = [];

// Load filter options
function loadFilters() {
    fetch('/api/get_filters.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allBrands = data.filters.brands || [];
                allModels = data.filters.models || [];
                allCategories = data.filters.categories || [];

                // Populate quick filter dropdowns
                populateQuickFilters(data.filters);
                // Update models based on current brand selection (initially none)
                updateModelsForBrand();
            }
        })
        .catch(error => console.error('Error loading filters:', error));
}

// Check URL parameters and apply initial filters
function checkUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);

    // Check for brand parameter
    const brandParam = urlParams.get('brand');
    if (brandParam) {
        // Wait a bit for filters to load, then set brand filter
        setTimeout(() => {
            const brandSelect = document.getElementById('quickBrandFilter');
            if (brandSelect) {
                // Find option with matching text
                const options = brandSelect.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].text.toLowerCase().includes(brandParam.toLowerCase()) ||
                        options[i].value.toLowerCase() === brandParam.toLowerCase()) {
                        brandSelect.value = options[i].value;
                        updateModelsForBrand();
                        break;
                    }
                }
            }

            // Check for model parameter
            const modelParam = urlParams.get('model');
            if (modelParam) {
                // Wait for models to load, then set model filter
                setTimeout(() => {
                    const modelSelect = document.getElementById('quickModelFilter');
                    if (modelSelect) {
                        const options = modelSelect.options;
                        for (let i = 0; i < options.length; i++) {
                            if (options[i].text.toLowerCase().includes(modelParam.toLowerCase()) ||
                                options[i].value.toLowerCase() === modelParam.toLowerCase()) {
                                modelSelect.value = options[i].value;
                                break;
                            }
                        }
                    }

                    // Apply filters after setting both brand and model
                    applyFilters();
                }, 500);
            } else {
                // Apply filters for brand only
                applyFilters();
            }
        }, 1000);
    }
}

// Populate quick filter dropdowns
function populateQuickFilters(filters) {
    // Quick brand filter
    const brandSelect = document.getElementById('quickBrandFilter');
    if (brandSelect) {
        let options = '<option value="">All Brands</option>';
        (filters.brands || []).forEach(brand => {
            options += `<option value="${brand.name}">${brand.name} (${brand.count})</option>`;
        });
        brandSelect.innerHTML = options;
    }

    // Quick category filter
    const categorySelect = document.getElementById('quickCategoryFilter');
    if (categorySelect) {
        let options = '<option value="">All Categories</option>';
        (filters.categories || []).forEach(category => {
            options += `<option value="${category.name}">${category.name} (${category.count})</option>`;
        });
        categorySelect.innerHTML = options;
    }

    // Quick model filter - initially just "All Models"
    const modelSelect = document.getElementById('quickModelFilter');
    if (modelSelect) {
        modelSelect.innerHTML = '<option value="">All Models</option>';
    }

    // Year filtering removed as requested
}

// Function to update models when brand is selected
function updateModelsForBrand() {
    const brandSelect = document.getElementById('quickBrandFilter');
    const modelSelect = document.getElementById('quickModelFilter');

    if (!brandSelect || !modelSelect) return;

    const selectedBrand = brandSelect.value;

    if (!selectedBrand) {
        // No brand selected - show only "All Models"
        modelSelect.innerHTML = '<option value="">All Models</option>';
    } else {
        // Brand selected - fetch models for this brand
        modelSelect.innerHTML = '<option value="">Loading models...</option>';

        fetch(`/api/get_models_by_brand.php?brand=${encodeURIComponent(selectedBrand)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let options = '<option value="">All Models</option>';
                    (data.models || []).forEach(model => {
                        options += `<option value="${model.name}">${model.name} (${model.count})</option>`;
                    });
                    modelSelect.innerHTML = options;
                } else {
                    modelSelect.innerHTML = '<option value="">All Models</option>';
                    console.error('Error loading models:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching models:', error);
                modelSelect.innerHTML = '<option value="">All Models</option>';
            });
    }
}

// Load featured products
function loadFeaturedProducts() {
    fetch('/api/get_products.php?limit=8&sort=newest')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderFeaturedProducts(data.products);
            } else {
                showFeaturedError();
            }
        })
        .catch(error => {
            console.error('Error loading featured products:', error);
            showFeaturedError();
        })
        .finally(() => {
            document.getElementById('featuredLoading').style.display = 'none';
        });
}


// Render featured products
function renderFeaturedProducts(products) {
    const container = document.getElementById('featuredProductsContainer');

    if (products.length === 0) {
        showFeaturedError();
        return;
    }

    container.innerHTML = products.map(product => {
        // Prepare all images (main + gallery)
        const allImages = [product.image];
        if (product.gallery_images && product.gallery_images.length > 0) {
            allImages.push(...product.gallery_images);
        }

        // Create image carousel if multiple images
        let imageHtml = '';
        if (allImages.length > 1) {
            const carouselId = `featured-carousel-${product.id}`;
            imageHtml = `
                <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        ${allImages.map((img, index) => `
                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <img src="${img}" alt="${product.name}" class="product-image d-block w-100" onerror="this.src='/img/no-image.png'">
                            </div>
                        `).join('')}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            `;
        } else {
            imageHtml = `<img src="${product.image}" alt="${product.name}" class="product-image" onerror="this.src='/img/no-image.png'">`;
        }

        return `
        <div class="col-lg-3 col-md-6">
            <div class="product-card h-100">
                <div class="product-image-container position-relative">
                    ${imageHtml}
                    <div class="product-badges">
                        ${getProductBadges(product)}
                        <span class="badge ${getStockBadgeClass(product.stock_status)} stock-badge">
                            ${product.stock_status}
                        </span>
                    </div>
                    <div class="product-overlay">
                        <div class="text-center text-white">
                            <i class="fas fa-eye fa-2x mb-2"></i>
                            <p class="mb-0 small fw-bold">Click to Quick View</p>
                        </div>
                    </div>
                </div>
                <div class="product-info">
                    <h6 class="product-title">${product.name}</h6>
                    <div class="product-meta">
                        ${product.brand ? `<span><i class="fas fa-tag me-1"></i>${product.brand}</span>` : ''}
                        ${product.model ? `<span><i class="fas fa-car me-1"></i>${product.model}</span>` : ''}
                        ${product.category ? `<span><i class="fas fa-cogs me-1"></i>${product.category}</span>` : ''}
                    </div>
                    <div class="product-price">
                        ${product.on_sale && product.sale_price ?
                            `<div class="price-sale">
                                <span class="original-price text-decoration-line-through text-muted small">
                                    RWF ${product.price.toLocaleString()}
                                </span>
                                <div class="sale-price text-danger fw-bold">
                                    RWF ${product.sale_price.toLocaleString()}
                                </div>
                            </div>` :
                            `RWF ${product.price.toLocaleString()}`
                        }
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-sm btn-quick-view" onclick="quickViewProduct(${product.id})">
                            <i class="fas fa-eye me-1"></i>Quick View
                        </button>
                        <button class="btn btn-sm btn-add-cart" onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price})">
                            <i class="fas fa-cart-plus me-1"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

// Show featured products error
function showFeaturedError() {
    const container = document.getElementById('featuredProductsContainer');
    container.innerHTML = `
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Featured Products Available</h5>
                <p class="text-muted">Featured products will appear here once added to the system.</p>
            </div>
        </div>
    `;
}

// Apply filters function - CRITICAL: This was missing and causing filters not to work!
function applyFilters() {
    // Reset current filters
    currentFilters = {};

    // Get search value
    const searchInput = document.getElementById('advancedSearchInput');
    if (searchInput && searchInput.value.trim()) {
        currentFilters.search = searchInput.value.trim();
    }

    // Get brand filter
    const brandFilter = document.getElementById('quickBrandFilter');
    if (brandFilter && brandFilter.value) {
        currentFilters.brand = brandFilter.value;
    }

    // Get model filter
    const modelFilter = document.getElementById('quickModelFilter');
    if (modelFilter && modelFilter.value) {
        currentFilters.model = modelFilter.value;
    }

    // Get category filter
    const categoryFilter = document.getElementById('quickCategoryFilter');
    if (categoryFilter && categoryFilter.value) {
        currentFilters.category = categoryFilter.value;
    }

    // Get price filters
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const quickMaxPrice = document.getElementById('quickMaxPrice');

    if (minPrice && minPrice.value) {
        currentFilters.min_price = parseFloat(minPrice.value);
    }
    if (maxPrice && maxPrice.value) {
        currentFilters.max_price = parseFloat(maxPrice.value);
    }
    if (quickMaxPrice && quickMaxPrice.value) {
        currentFilters.max_price = parseFloat(quickMaxPrice.value);
    }

    // Get availability filter if it exists
    const availabilityFilter = document.querySelector('input[name="availability"]:checked');
    if (availabilityFilter) {
        currentFilters.availability = availabilityFilter.value;
    }

    // Update active filters display
    updateActiveFiltersDisplay();

    // Reload products with filters
    loadProducts(1);
}

// Load products
function loadProducts(page = 1) {
    if (isLoading) return;

    isLoading = true;
    currentPage = page;

    // Show loading
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('productsContainer').style.opacity = '0.5';

    // Build query parameters
    const params = new URLSearchParams({
        page: page,
        limit: currentPageSize,
        sort: currentSort,
        ...currentFilters
    });

    fetch(`/api/get_products.php?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderProducts(data.products);
                renderPagination(data.pagination);
                updateResultsInfo(data.pagination);
                totalPages = data.pagination.total_pages;
            } else {
                showNoResults();
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            showNoResults();
        })
        .finally(() => {
            isLoading = false;
            document.getElementById('loadingSpinner').style.display = 'none';
            document.getElementById('productsContainer').style.opacity = '1';
        });
}

// Render products
function renderProducts(products) {
    const container = document.getElementById('productsContainer');

    if (products.length === 0) {
        showNoResults();
        return;
    }

    container.innerHTML = products.map(product => {
        // Prepare all images (main + gallery)
        const allImages = [product.image];
        if (product.gallery_images && product.gallery_images.length > 0) {
            allImages.push(...product.gallery_images);
        }

        // Create image carousel if multiple images
        let imageHtml = '';
        if (allImages.length > 1) {
            const carouselId = `carousel-${product.id}`;
            imageHtml = `
                <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        ${allImages.map((img, index) => `
                            <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                <img src="${img}" alt="${product.name}" class="product-image d-block w-100" onerror="this.src='/img/no-image.png'">
                            </div>
                        `).join('')}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <div class="carousel-indicators">
                        ${allImages.map((_, index) => `
                            <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${index}" ${index === 0 ? 'class="active" aria-current="true"' : ''} aria-label="Slide ${index + 1}"></button>
                        `).join('')}
                    </div>
                </div>
            `;
        } else {
            imageHtml = `<img src="${product.image}" alt="${product.name}" class="product-image" onerror="this.src='/img/no-image.png'">`;
        }

        return `
        <div class="col-lg-4 col-md-6">
            <div class="product-card h-100" onclick="addToRecentlyViewed(${product.id})">
                <div class="product-image-container position-relative">
                    ${imageHtml}
                    <div class="product-badges">
                        ${getProductBadges(product)}
                        <span class="badge ${getStockBadgeClass(product.stock_status)} stock-badge">
                            ${product.stock_status}
                        </span>
                    </div>
                    <div class="product-overlay">
                        <div class="quick-actions">
                            <button class="btn btn-light btn-sm" onclick="event.stopPropagation(); quickViewProduct(${product.id})" title="Quick View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-light btn-sm" onclick="event.stopPropagation(); toggleWishlist(${product.id})" title="Add to Wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                            <button class="btn btn-light btn-sm ${isInComparison(product.id) ? 'active' : ''}"
                                    onclick="event.stopPropagation(); toggleComparison(${product.id})" title="Compare">
                                <i class="fas fa-balance-scale"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="product-info d-flex flex-column">
                    <h6 class="product-title">${product.name}</h6>
                    <div class="product-meta">
                        ${product.brand ? `<span class="product-meta-item"><i class="fas fa-tag"></i>${product.brand}</span>` : ''}
                        ${product.model ? `<span class="product-meta-item"><i class="fas fa-car"></i>${product.model}</span>` : ''}
                        ${product.category ? `<span class="product-meta-item"><i class="fas fa-cogs"></i>${product.category}</span>` : ''}
                    </div>
                    <div class="product-price">
                        ${product.on_sale && product.sale_price ?
                            `<div class="price-sale">
                                <span class="original-price text-decoration-line-through text-muted small">
                                    RWF ${product.price.toLocaleString()}
                                </span>
                                <div class="sale-price text-danger fw-bold">
                                    RWF ${product.sale_price.toLocaleString()}
                                </div>
                            </div>` :
                            `<div class="regular-price text-primary fw-bold">
                                <span class="price-currency">RWF</span>
                                <span>${product.price.toLocaleString()}</span>
                            </div>`
                        }
                    </div>
                    <div class="delivery-info">
                        <i class="fas fa-truck"></i>
                        ${getDeliveryInfo(product.stock_status)}
                    </div>
                    <div class="product-actions mt-auto">
                        <button class="btn btn-quick-view" onclick="event.stopPropagation(); quickViewProduct(${product.id})">
                            <i class="fas fa-eye me-1"></i>Quick View
                        </button>
                        <button class="btn btn-add-cart" onclick="event.stopPropagation(); addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price})">
                            <i class="fas fa-cart-plus me-1"></i>Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');

    document.getElementById('noResults').style.display = 'none';
}

// Get stock badge class
function getStockBadgeClass(status) {
    switch (status) {
        case 'In Stock': return 'bg-success';
        case 'Low Stock': return 'bg-warning text-dark';
        case 'Special Order': return 'bg-info';
        default: return 'bg-secondary';
    }
}

// Get product badges (New, Bestseller, Sale, etc.)
function getProductBadges(product) {
    let badges = '';

    // New badge (products added in last 30 days)
    if (product.is_new) {
        badges += '<span class="badge product-badge badge-new">New</span>';
    }

    // Bestseller badge (based on sales/popularity)
    if (product.is_bestseller) {
        badges += '<span class="badge product-badge badge-bestseller">Bestseller</span>';
    }

    // Sale badge (if there's a discount)
    if (product.on_sale) {
        badges += '<span class="badge product-badge badge-sale">Sale</span>';
    }

    // Low stock warning
    if (product.stock_status === 'Low Stock') {
        badges += '<span class="badge product-badge badge-low-stock">Low Stock</span>';
    }

    return badges;
}

// Get delivery information
function getDeliveryInfo(stockStatus) {
    switch (stockStatus) {
        case 'In Stock':
            return 'Ships in 24-48 hours';
        case 'Low Stock':
            return 'Ships in 2-3 days';
        case 'Special Order':
            return '7-14 days delivery';
        default:
            return 'Contact for availability';
    }
}

// Recently viewed products
function addToRecentlyViewed(productId) {
    // Remove if already exists
    recentlyViewed = recentlyViewed.filter(id => id !== productId);

    // Add to beginning
    recentlyViewed.unshift(productId);

    // Keep only last 5
    recentlyViewed = recentlyViewed.slice(0, 5);

    // Save to localStorage
    localStorage.setItem('recentlyViewed', JSON.stringify(recentlyViewed));

    // Update display
    updateRecentlyViewedDisplay();
}

function loadRecentlyViewed() {
    const stored = localStorage.getItem('recentlyViewed');
    if (stored) {
        recentlyViewed = JSON.parse(stored);
        updateRecentlyViewedDisplay();
    }
}

function updateRecentlyViewedDisplay() {
    const container = document.getElementById('recentlyViewedContainer');
    const section = document.getElementById('recentlyViewed');

    if (recentlyViewed.length === 0) {
        section.style.display = 'none';
        return;
    }

    section.style.display = 'block';

    // Load product details for recently viewed items
    const ids = recentlyViewed.join(',');
    fetch(`/api/get_products.php?ids=${ids}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                container.innerHTML = data.products.map(product => `
                    <div class="recently-viewed-item" onclick="quickViewProduct(${product.id})">
                        <img src="${product.image}" alt="${product.name}" onerror="this.src='/img/no-image.png'">
                        <div class="item-title">${product.name}</div>
                        <div class="item-price">RWF ${product.price.toLocaleString()}</div>
                    </div>
                `).join('');
            }
        })
        .catch(error => console.error('Error loading recently viewed:', error));
}

function clearRecentlyViewed() {
    recentlyViewed = [];
    localStorage.removeItem('recentlyViewed');
    document.getElementById('recentlyViewed').style.display = 'none';
}

// Product comparison
function toggleComparison(productId) {
    const index = comparisonList.indexOf(productId);
    const btn = event.target.closest('button');

    if (index > -1) {
        comparisonList.splice(index, 1);
        btn.classList.remove('active');
        showToast('Removed from comparison', '', 'info');
    } else {
        if (comparisonList.length >= 4) {
            showToast('Maximum 4 products can be compared', '', 'warning');
            return;
        }
        comparisonList.push(productId);
        btn.classList.add('active');
        showToast('Added to comparison', '', 'success');
    }

    localStorage.setItem('comparisonList', JSON.stringify(comparisonList));
    updateComparisonBadge();
}

function isInComparison(productId) {
    return comparisonList.includes(productId);
}

function loadComparisonList() {
    const stored = localStorage.getItem('comparisonList');
    if (stored) {
        comparisonList = JSON.parse(stored);
        updateComparisonBadge();
    }
}

function updateComparisonBadge() {
    // Update comparison badge in navbar if exists
    const badge = document.getElementById('comparisonBadge');
    if (badge) {
        badge.textContent = comparisonList.length;
        badge.style.display = comparisonList.length > 0 ? 'inline' : 'none';
    }
}

// Wishlist functionality
function toggleWishlist(productId) {
    // Placeholder for wishlist functionality
    showToast('Wishlist feature coming soon!', '', 'info');
}

// Page size functionality
function changePageSize() {
    const select = document.getElementById('pageSizeSelect');
    currentPageSize = parseInt(select.value);
    localStorage.setItem('pageSize', currentPageSize);
    loadProducts(1);
}

// Load saved page size
function loadSavedPageSize() {
    const saved = localStorage.getItem('pageSize');
    if (saved) {
        currentPageSize = parseInt(saved);
        document.getElementById('pageSizeSelect').value = currentPageSize;
    }
}

// Advanced search with autocomplete
function handleSearchInput() {
    const query = this.value.trim();
    if (query.length < 2) return;

    // Show loading state
    showSearchSuggestions(query);
}

function showSearchSuggestions(query) {
    // Placeholder for search suggestions
    // This would typically fetch from an API
    console.log('Search query:', query);
}

// Debounce function for search input
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Render pagination
function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');

    if (pagination.total_pages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<div class="d-flex justify-content-center"><div class="pagination-wrapper">';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<button class="btn btn-outline-primary btn-sm me-1" onclick="loadProducts(${pagination.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    } else {
        html += `<button class="btn btn-outline-secondary btn-sm me-1" disabled>
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }

    // Page numbers - simplified to show max 7 pages
    const totalPages = pagination.total_pages;
    const currentPage = pagination.current_page;

    // Calculate range
    let startPage = Math.max(1, currentPage - 3);
    let endPage = Math.min(totalPages, currentPage + 3);

    // Adjust range to always show 7 pages when possible
    if (endPage - startPage < 6) {
        if (startPage === 1) {
            endPage = Math.min(totalPages, startPage + 6);
        } else if (endPage === totalPages) {
            startPage = Math.max(1, endPage - 6);
        }
    }

    // First page if not in range
    if (startPage > 1) {
        html += `<button class="btn btn-outline-primary btn-sm me-1" onclick="loadProducts(1)">1</button>`;
        if (startPage > 2) {
            html += `<span class="me-1 text-muted">...</span>`;
        }
    }

    // Page range
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `<button class="btn btn-primary btn-sm me-1 active" disabled>${i}</button>`;
        } else {
            html += `<button class="btn btn-outline-primary btn-sm me-1" onclick="loadProducts(${i})">${i}</button>`;
        }
    }

    // Last page if not in range
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="me-1 text-muted">...</span>`;
        }
        html += `<button class="btn btn-outline-primary btn-sm me-1" onclick="loadProducts(${totalPages})">${totalPages}</button>`;
    }

    // Next button
    if (currentPage < totalPages) {
        html += `<button class="btn btn-outline-primary btn-sm" onclick="loadProducts(${currentPage + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    } else {
        html += `<button class="btn btn-outline-secondary btn-sm" disabled>
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }

    html += '</div></div>';
    container.innerHTML = html;
}

// Update results info
function updateResultsInfo(pagination) {
    const info = document.getElementById('resultsInfo');
    const start = (pagination.current_page - 1) * pagination.per_page + 1;
    const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_products);

    info.innerHTML = `
        <i class="fas fa-list me-2 text-primary"></i>
        <strong>${pagination.total_products}</strong> products found
        ${pagination.total_products > 0 ? `(showing ${start}-${end})` : ''}
    `;
}

// Apply filters
function applyFilters() {
    // Collect filter values
    currentFilters = {};

    // Advanced search
    const advancedSearchValue = document.getElementById('advancedSearchInput')?.value.trim();
    if (advancedSearchValue) {
        currentFilters.search = advancedSearchValue;
    }

    // Quick filters
    const quickBrand = document.getElementById('quickBrandFilter')?.value;
    if (quickBrand) {
        currentFilters.brand = quickBrand;
    }

    const quickCategory = document.getElementById('quickCategoryFilter')?.value;
    if (quickCategory) {
        currentFilters.category = quickCategory;
    }

    const quickModel = document.getElementById('quickModelFilter')?.value;
    if (quickModel) {
        currentFilters.model = quickModel;
    }

    // Year filtering removed as requested

    const quickMaxPrice = document.getElementById('quickMaxPrice')?.value;
    if (quickMaxPrice) {
        currentFilters.max_price = quickMaxPrice;
    }

    // Advanced filters (only if advanced panel is open)
    const advancedPanel = document.getElementById('advancedFiltersPanel');
    if (advancedPanel && advancedPanel.classList.contains('show')) {
        // Price range (detailed)
        const minPrice = document.getElementById('minPrice')?.value;
        const maxPrice = document.getElementById('maxPrice')?.value;
        if (minPrice) currentFilters.min_price = minPrice;
        if (maxPrice) currentFilters.max_price = maxPrice;
    }

    // Sort
    currentSort = document.getElementById('sortSelect').value;

    // Reset to page 1 and load
    loadProducts(1);
}

// Update active filters display (simplified since sidebar is removed)
function updateActiveFiltersDisplay() {
    // Since sidebar is removed, we don't need to display active filters
    // The quick filters in the advanced panel will handle filtering
}

// Remove individual filter
function removeFilter(type, value) {
    switch (type) {
        case 'search':
            document.getElementById('advancedSearchInput').value = '';
            delete currentFilters.search;
            break;
        case 'brand':
            if (currentFilters.brand) {
                const brands = currentFilters.brand.split(',').filter(b => b !== value);
                if (brands.length > 0) {
                    currentFilters.brand = brands.join(',');
                } else {
                    delete currentFilters.brand;
                }
                const quickBrand = document.getElementById('quickBrandFilter');
                if (quickBrand && quickBrand.value === value) quickBrand.value = '';
                updateModelsForBrand();
            }
            break;
        case 'category':
            if (currentFilters.category) {
                const categories = currentFilters.category.split(',').filter(c => c !== value);
                if (categories.length > 0) {
                    currentFilters.category = categories.join(',');
                } else {
                    delete currentFilters.category;
                }
                const quickCategory = document.getElementById('quickCategoryFilter');
                if (quickCategory && quickCategory.value === value) quickCategory.value = '';
            }
            break;
        case 'model':
            delete currentFilters.model;
            document.getElementById('quickModelFilter').value = '';
            break;
        case 'price':
            delete currentFilters.min_price;
            delete currentFilters.max_price;
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            document.getElementById('quickMaxPrice').value = '';
            break;
        case 'availability':
            delete currentFilters.availability;
            break;
    }

    applyFilters();
}

// Clear all filters
function clearAllFilters() {
    // Reset all form elements
    const elements = [
        'advancedSearchInput', 'minPrice', 'maxPrice',
        'quickBrandFilter', 'quickCategoryFilter', 'quickModelFilter', 'quickMaxPrice'
    ];

    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) element.value = '';
    });

    document.getElementById('sortSelect').value = 'newest';

    currentFilters = {};
    currentSort = 'newest';

    // Update models since brand was cleared
    updateModelsForBrand();

    loadProducts(1);
}

// Add to cart
function addToCart(productId, productName, price) {
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
            // Update cart display in navbar
            if (typeof updateCartDisplay === 'function') {
                updateCartDisplay();
            }

            // Show success message
            showToast('Success', `${productName} added to cart!`, 'success');
        } else {
            showToast('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error', 'Failed to add item to cart', 'error');
    });
}

// Quick view product
function quickViewProduct(productId) {
    // Show loading in modal
    const modal = new bootstrap.Modal(document.getElementById('quickViewModal'));
    const content = document.getElementById('quickViewContent');

    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading product details...</p>
        </div>
    `;

    modal.show();

    // Fetch product details
    fetch(`/api/get_products.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                // Get the specific product
                const product = data.products[0];
                renderQuickView(product);
                currentQuickViewProduct = product;
            } else {
                content.innerHTML = '<div class="text-center py-5"><p class="text-muted">Product not found.</p></div>';
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            content.innerHTML = '<div class="text-center py-5"><p class="text-danger">Error loading product details.</p></div>';
        });
}

// Render quick view
function renderQuickView(product) {
    const content = document.getElementById('quickViewContent');

    // Prepare all images (main + gallery)
    const allImages = [product.image];
    if (product.gallery_images && product.gallery_images.length > 0) {
        allImages.push(...product.gallery_images);
    }

    // Create image carousel if multiple images
    let imageHtml = '';
    if (allImages.length > 1) {
        const carouselId = `quickViewCarousel-${product.id}`;
        imageHtml = `
            <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    ${allImages.map((img, index) => `
                        <div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="${img}" alt="${product.name}" class="quick-view-image d-block w-100 rounded" onerror="this.src='/img/no-image.png'">
                        </div>
                    `).join('')}
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                <div class="carousel-indicators">
                    ${allImages.map((_, index) => `
                        <button type="button" data-bs-target="#${carouselId}" data-bs-slide-to="${index}" ${index === 0 ? 'class="active" aria-current="true"' : ''} aria-label="Slide ${index + 1}"></button>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        imageHtml = `<img src="${product.image}" alt="${product.name}" class="quick-view-image rounded" onerror="this.src='/img/no-image.png'">`;
    }

    content.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                ${imageHtml}
            </div>
            <div class="col-md-6">
                <div class="quick-view-details">
                    <h3 class="quick-view-title">${product.name}</h3>

                    <div class="quick-view-meta mb-3">
                        ${product.brand ? `<span class="badge bg-primary me-1">${product.brand}</span>` : ''}
                        ${product.model ? `<span class="badge bg-secondary me-1">${product.model}</span>` : ''}
                        ${product.category ? `<span class="badge bg-info">${product.category}</span>` : ''}
                    </div>

                    <div class="quick-view-price mb-3">
                        ${product.on_sale && product.sale_price ?
                            `<div class="price-sale">
                                <span class="original-price text-decoration-line-through text-muted me-2">
                                    RWF ${product.price.toLocaleString()}
                                </span>
                                <span class="sale-price text-danger fw-bold">
                                    RWF ${product.sale_price.toLocaleString()}
                                </span>
                            </div>` :
                            `RWF ${product.price.toLocaleString()}`
                        }
                    </div>

                    <div class="mb-3">
                        <span class="badge ${getStockBadgeClass(product.stock_status)} fs-6 px-3 py-2">
                            ${product.stock_status}
                        </span>
                    </div>

                    <div class="quick-view-description mb-4">
                        ${product.description || 'No description available.'}
                    </div>

                    ${product.year_from && product.year_to ? `
                        <div class="mb-3">
                            <strong>Compatible Years:</strong> ${product.year_from} - ${product.year_to}
                        </div>
                    ` : ''}

                    <div class="d-flex gap-2">
                        <a href="/pages/single.php?id=${product.id}" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>View Full Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Update modal button
    document.getElementById('quickViewAddToCart').onclick = function() {
        addToCart(product.id, product.name, product.price);
    };
}


// Enhanced toast notifications using the included system
function showToast(title, message, type = 'info') {
    // Map old types to new toast system
    const typeMap = {
        'success': 'success',
        'error': 'error',
        'warning': 'warning',
        'info': 'info'
    };

    showToastMessage(message, title, typeMap[type] || 'info');
}

// Comparison modal functions
function showComparisonModal() {
    if (comparisonList.length === 0) {
        showToast('No Products', 'Add products to compare first', 'warning');
        return;
    }

    const modal = new bootstrap.Modal(document.getElementById('comparisonModal'));
    renderComparisonModal();
    modal.show();
}

function renderComparisonModal() {
    const container = document.getElementById('comparisonContent');

    if (comparisonList.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-balance-scale fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products to compare</h5>
                <p class="text-muted">Add products to comparison by clicking the compare button on product cards.</p>
            </div>
        `;
        return;
    }

    // Load product details for comparison
    const ids = comparisonList.join(',');
    fetch(`/api/get_products.php?ids=${ids}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                const products = data.products;
                let html = '<div class="table-responsive"><table class="table table-bordered">';

                // Header row
                html += '<thead class="table-dark"><tr>';
                html += '<th>Product</th>';
                products.forEach(product => {
                    html += `<th class="text-center">
                        <img src="${product.image}" alt="${product.name}" style="width: 60px; height: 60px; object-fit: contain;" onerror="this.src='/img/no-image.png'">
                        <br><small class="fw-bold">${product.name}</small>
                        <br><button class="btn btn-sm btn-outline-danger mt-1" onclick="removeFromComparison(${product.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </th>`;
                });
                html += '</tr></thead>';

                // Body rows
                const attributes = [
                    { key: 'price', label: 'Price', format: (p) => `RWF ${p.price.toLocaleString()}` },
                    { key: 'brand', label: 'Brand', format: (p) => p.brand || 'N/A' },
                    { key: 'model', label: 'Model', format: (p) => p.model || 'N/A' },
                    { key: 'category', label: 'Category', format: (p) => p.category || 'N/A' },
                    { key: 'stock_status', label: 'Availability', format: (p) => p.stock_status },
                    { key: 'action', label: 'Action', format: (p) => `
                        <button class="btn btn-primary btn-sm" onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.price})">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    ` }
                ];

                attributes.forEach(attr => {
                    html += '<tr>';
                    html += `<td class="fw-bold">${attr.label}</td>`;
                    products.forEach(product => {
                        html += `<td class="text-center">${attr.format(product)}</td>`;
                    });
                    html += '</tr>';
                });

                html += '</table></div>';
                container.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error loading comparison:', error);
            container.innerHTML = '<div class="text-center py-5"><p class="text-danger">Error loading comparison data.</p></div>';
        });
}

function removeFromComparison(productId) {
    comparisonList = comparisonList.filter(id => id !== productId);
    localStorage.setItem('comparisonList', JSON.stringify(comparisonList));
    updateComparisonBadge();

    // Re-render modal if open
    const modal = document.getElementById('comparisonModal');
    if (modal.classList.contains('show')) {
        renderComparisonModal();
    }

    showToast('Removed', 'Product removed from comparison', 'info');
}

function clearComparison() {
    comparisonList = [];
    localStorage.removeItem('comparisonList');
    updateComparisonBadge();
    renderComparisonModal();
    showToast('Cleared', 'Comparison list cleared', 'info');
}

// Show no results
function showNoResults() {
    document.getElementById('productsContainer').innerHTML = '';
    document.getElementById('noResults').style.display = 'block';
    document.getElementById('paginationContainer').innerHTML = '';
}

// Set view mode (grid/list)
function setViewMode(mode) {
    const gridBtn = document.getElementById('gridView');
    const listBtn = document.getElementById('listView');

    if (mode === 'grid') {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        // Grid view is default
    } else {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        // List view implementation can be added later
    }
}

// Cart is initialized by navigation.php
</script>

<?php include '../includes/footer.php'; ?>
