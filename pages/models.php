<?php
$page_title = 'Browse Models - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

// Get brand parameter
$brand_slug = isset($_GET['brand']) ? trim($_GET['brand']) : '';

if (empty($brand_slug)) {
    header('Location: brands.php');
    exit;
}

// Get brand information
$brand_query = $conn->prepare("SELECT * FROM vehicle_brands_enhanced WHERE slug = ? AND is_active = 1");
$brand_query->bind_param('s', $brand_slug);
$brand_query->execute();
$brand_result = $brand_query->get_result();

if ($brand_result->num_rows === 0) {
    header('Location: brands.php');
    exit;
}

$brand = $brand_result->fetch_assoc();

// Format brand logo path
if (!empty($brand['logo_image'])) {
    $brand['logo_image'] = '/uploads/brands/' . $brand['logo_image'];
}

// Get models for this brand with product counts
$models_query = $conn->prepare("
    SELECT vm.*,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count
    FROM vehicle_models_enhanced vm
    LEFT JOIN products_enhanced p ON vm.id = p.model_id
    WHERE vm.brand_id = ? AND vm.is_active = 1
    GROUP BY vm.id
    ORDER BY vm.model_name
");
$models_query->bind_param('i', $brand['id']);
$models_query->execute();
$models_result = $models_query->get_result();

$models = [];
while ($model = $models_result->fetch_assoc()) {
    $models[] = $model;
}
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 text-white fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <i class="fas fa-car me-3"></i><?php echo htmlspecialchars($brand['brand_name']); ?> Models
                </h1>
                <p class="lead text-white-50 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    Browse all available models for <?php echo htmlspecialchars($brand['brand_name']); ?>. Find compatible parts for your specific vehicle model.
                </p>
                <div class="d-flex gap-3 flex-wrap wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-car-side fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo count($models); ?> Models Available</h6>
                            <small class="text-white-50">Complete Range</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-boxes fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">
                                <?php
                                $total_products = array_sum(array_column($models, 'active_product_count'));
                                echo number_format($total_products);
                                ?> Parts
                            </h6>
                            <small class="text-white-50">In Stock</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-tools fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Expert Support</h6>
                            <small class="text-white-50">Professional Advice</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center wow fadeInUp" data-wow-delay="0.7s">
                <div class="position-relative">
                    <?php if ($brand['logo_image']): ?>
                        <img src="<?php echo htmlspecialchars($brand['logo_image']); ?>" alt="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                             class="img-fluid rounded shadow-lg" style="max-width: 200px; background: white; padding: 20px;">
                    <?php else: ?>
                        <div class="bg-white rounded shadow-lg d-inline-flex align-items-center justify-content-center" style="width: 200px; height: 200px;">
                            <i class="bi bi-tag text-primary fs-1"></i>
                        </div>
                    <?php endif; ?>
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-to-r from-primary to-secondary rounded opacity-25"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Background Pattern -->
    <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 200px;">
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
        <li class="breadcrumb-item">
            <a href="brands.php" class="text-decoration-none">
                <i class="fas fa-tags me-1"></i>Brands
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-car me-1"></i><?php echo htmlspecialchars($brand['brand_name']); ?> Models
        </li>
    </ol>
</nav>

<!-- Brand Info Section -->
<div class="container-fluid py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-3">
                    <?php
                    // Brand logo display logic (same as brands.php)
                    $brandName = $brand['brand_name'];
                    $brandSlug = strtolower(str_replace([' ', '-'], '', $brand['brand_name']));
                    
                    // Mapping for uploaded brand logos with specific filenames
                    $uploadedLogos = [
                        'Mercedes-Benz' => 'mercedes-benz-9.svg',
                        'Renault' => 'renault-2.svg',
                        'Changan' => 'changan-automobile-logo-1.svg',
                        'BYD' => 'byd-1.svg',
                        'Geely' => 'geely-logo-2.svg',
                        'Chery' => 'chery-3.svg',
                        'Citroën' => 'citroen-racing-2009-2016-logo.svg',
                        'BAIC' => 'BAIC.png',
                        'Dongfeng' => 'DONGFENG.png',
                        'Great Wall' => 'great-wall-seeklogo.png',
                        'JAC' => 'jac-motors-seeklogo.png'
                    ];
                    
                    // Try uploaded logos first
                    $logoPath = null;
                    if (isset($uploadedLogos[$brand['brand_name']])) {
                        $logoPath = "/uploads/brands/" . $uploadedLogos[$brand['brand_name']];
                    }
                    
                    if ($logoPath):
                    ?>
                        <img src="<?php echo $logoPath; ?>"
                             alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                             class="me-3 rounded" style="width: 60px; height: 60px; object-fit: contain; background: white; padding: 10px;">
                    <?php else:
                        // Brands with Simple Icons
                        $simpleIconBrands = [
                            'toyota', 'nissan', 'hyundai', 'kia', 'mitsubishi', 'suzuki',
                            'volkswagen', 'bmw', 'audi', 'honda', 'ford', 'mazda', 'subaru',
                            'infiniti', 'acura', 'cadillac', 'jeep', 'chrysler', 'dodge', 'ram',
                            'tesla', 'porsche', 'ferrari', 'lamborghini', 'bentley', 'jaguar',
                            'volvo', 'saab', 'scania', 'iveco', 'daf', 'man', 'renault', 'peugeot',
                            'fiat', 'alfaromeo', 'maserati', 'lancia', 'abarth', 'skoda', 'seat',
                            'opel', 'vauxhall', 'mini', 'smart', 'maybach', 'lotus', 'mclaren',
                            'yamaha', 'kawasaki', 'ducati', 'triumph', 'aprilia', 'ktm', 'husqvarna',
                            'mvagusta', 'indian', 'royalenfield', 'vespa', 'piaggio', 'trek',
                            'specialized', 'cannondale', 'giant', 'johndeere', 'caseih', 'newholland',
                            'claas', 'fendt', 'kubota', 'yanmar', 'komatsu', 'caterpillar', 'liebherr',
                            'jcb', 'bobcat'
                        ];
                        
                        // Brands with custom logo URLs
                        $customLogoBrands = [
                            'byd' => 'https://www.carlogos.org/car-logos/bYD-logo.png',
                            'mercedes' => 'https://www.carlogos.org/car-logos/mercedes-benz-logo.png',
                            'landrover' => 'https://www.carlogos.org/car-logos/land-rover-logo.png',
                            'lexus' => 'https://www.carlogos.org/car-logos/lexus-logo.png',
                            'mg' => 'https://www.carlogos.org/car-logos/mg-logo.png',
                            'wuling' => 'https://www.carlogos.org/car-logos/wuling-logo.png',
                            'isuzu' => 'https://www.carlogos.org/car-logos/isuzu-logo.png',
                            'chevrolet' => 'https://www.carlogos.org/car-logos/chevrolet-logo.png',
                            'citroen' => 'https://www.carlogos.org/car-logos/citroen-logo.png'
                        ];
                        
                        if (in_array($brandSlug, $simpleIconBrands)):
                        ?>
                            <img src="https://cdn.simpleicons.org/<?php echo $brandSlug; ?>/007bff"
                                 alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                                 class="me-3 rounded" style="width: 60px; height: 60px; object-fit: contain; background: white; padding: 10px;">
                        <?php elseif (isset($customLogoBrands[$brandSlug])):
                        ?>
                            <img src="<?php echo $customLogoBrands[$brandSlug]; ?>"
                                 alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                                 class="me-3 rounded" style="width: 60px; height: 60px; object-fit: contain; background: white; padding: 10px;">
                        <?php else:
                            // Text-based logo for brands without images
                            ?>
                            <div class="text-logo me-3" style="width: 60px; height: 60px; object-fit: contain; background: white; padding: 10px; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                <span class="brand-initial" style="font-size: 18px; font-weight: bold; color: #007bff;"><?php echo strtoupper(substr($brand['brand_name'], 0, 1)); ?></span>
                            </div>
                        <?php endif;
                    endif;
                    ?>
                    <div>
                        <h2 class="mb-1"><?php echo htmlspecialchars($brand['brand_name']); ?></h2>
                        <?php if ($brand['country']): ?>
                            <p class="text-muted mb-0">
                                <i class="bi bi-geo-alt-fill me-1"></i><?php echo htmlspecialchars($brand['country']); ?>
                                <?php if ($brand['founded_year']): ?>
                                    • Founded <?php echo $brand['founded_year']; ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($brand['description']): ?>
                    <p class="text-muted"><?php echo htmlspecialchars($brand['description']); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="d-flex gap-2 justify-content-lg-end">
                    <a href="shop.php?brand=<?php echo urlencode($brand['brand_name']); ?>" class="btn btn-primary">
                        <i class="bi bi-box-seam-fill me-1"></i>View All Parts
                    </a>
                    <a href="brands.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Brands
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Models Section Start -->
<div class="container-fluid py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Available Models</h4>
            <h1 class="mb-0 display-5 wow fadeInUp" data-wow-delay="0.3s"><?php echo htmlspecialchars($brand['brand_name']); ?> Models</h1>
            <p class="text-muted mt-2 wow fadeInUp" data-wow-delay="0.5s">Select your specific model to find compatible parts and accessories</p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-light p-4 rounded-3 shadow-sm">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-search me-1 text-primary"></i>Search Models
                            </label>
                            <input type="text" class="form-control form-control-lg" id="modelSearchInput" placeholder="Search for your model...">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-sort me-1 text-primary"></i>Sort By
                            </label>
                            <select class="form-select form-select-lg" id="modelSortSelect">
                                <option value="name">Name (A-Z)</option>
                                <option value="products">Most Products</option>
                                <option value="year">Year Range</option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-calendar me-1 text-primary"></i>Year Filter
                            </label>
                            <select class="form-select form-select-lg" id="yearFilter">
                                <option value="">All Years</option>
                                <option value="2020+">2020 & Newer</option>
                                <option value="2010-2019">2010-2019</option>
                                <option value="2000-2009">2000-2009</option>
                                <option value="1990-1999">1990-1999</option>
                                <option value="pre-1990">Before 1990</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Models Grid -->
        <div class="row g-4" id="modelsContainer">
            <?php if (count($models) > 0): ?>
                <?php foreach ($models as $model): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6" data-model-name="<?php echo htmlspecialchars(strtolower($model['model_name'])); ?>"
                         data-year-from="<?php echo $model['year_from'] ?? 0; ?>" data-year-to="<?php echo $model['year_to'] ?? 9999; ?>"
                         data-products="<?php echo $model['active_product_count']; ?>" data-total="<?php echo $model['product_count']; ?>">
                        <div class="model-card h-100">
                            <div class="card border-0 shadow-sm h-100">
                                <!-- Model Image Header -->
                                <div class="card-image-header position-relative">
                                    <?php
                                    $model_image_path = $model['model_image'];
                                    if (!empty($model_image_path)) {
                                        $model_image_path = '/admin/' . ltrim($model_image_path, '/');
                                    }
                                    ?>
                                    <?php if (!empty($model_image_path)): ?>
                                        <img src="<?php echo htmlspecialchars($model_image_path); ?>"
                                             alt="<?php echo htmlspecialchars($model['model_name']); ?>"
                                             class="model-hero-image img-fluid">
                                        <div class="image-overlay"></div>
                                    <?php else: ?>
                                        <div class="model-hero-placeholder d-flex align-items-center justify-content-center">
                                            <i class="fas fa-car fa-3x text-primary"></i>
                                        </div>
                                    <?php endif; ?>
                                    <!-- Model Info Overlay -->
                                    <div class="model-info-overlay position-absolute bottom-0 start-0 end-0 p-3">
                                        <h5 class="card-title mb-1 fw-bold text-white"><?php echo htmlspecialchars($model['model_name']); ?></h5>
                                        <?php if (!empty($model['year_from']) && !empty($model['year_to'])): ?>
                                            <small class="text-white-50">
                                                <i class="fas fa-calendar me-1"></i><?php echo $model['year_from']; ?> - <?php echo $model['year_to']; ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body">
                                    <!-- Stats -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number text-primary fw-bold"><?php echo $model['active_product_count']; ?></div>
                                                <div class="stat-label small text-muted">Parts</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="stat-item">
                                                <div class="stat-number text-success fw-bold"><?php echo $model['product_count']; ?></div>
                                                <div class="stat-label small text-muted">Total</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <p class="card-text small text-muted mb-3 text-center">
                                        Browse genuine parts for this <?php echo htmlspecialchars($brand['brand_name']); ?> model.
                                    </p>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer bg-light border-0">
                                    <div class="d-grid gap-2">
                                        <a href="shop.php?brand=<?php echo urlencode($brand['brand_name']); ?>&model=<?php echo urlencode($model['model_name']); ?>"
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-boxes me-1"></i>Browse Parts
                                        </a>
                                        <?php if ($model['active_product_count'] > 0): ?>
                                            <button class="btn btn-outline-success btn-sm" onclick="quickViewModelParts(<?php echo $model['id']; ?>, '<?php echo htmlspecialchars($model['model_name']); ?>')">
                                                <i class="fas fa-eye me-1"></i>Quick View (<?php echo $model['active_product_count']; ?>)
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-car fa-4x text-muted mb-3"></i>
                        <h4>No Models Found</h4>
                        <p class="text-muted">No models are currently available for this brand.</p>
                        <a href="brands.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Browse Other Brands
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- No Results -->
        <div id="noModelsFound" class="text-center py-5 bg-light rounded-3 shadow-sm d-none">
            <i class="bi bi-search fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No models found</h4>
            <p class="text-muted">Try adjusting your search or filter criteria.</p>
            <button class="btn btn-primary" onclick="clearModelFilters()">Clear Filters</button>
        </div>
    </div>
</div>
<!-- Models Section End -->

<!-- Quick View Modal -->
<div class="modal fade" id="modelPartsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-eye me-2"></i>Parts for <span id="modalModelName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modelPartsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading parts...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading available parts...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="viewAllPartsLink" class="btn btn-primary">
                    <i class="bi bi-box-seam-fill me-1"></i>View All Parts
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Model Card Styles */
.model-card {
    transition: all 0.3s ease;
}

.model-card:hover {
    transform: translateY(-5px);
}

.model-card .card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.model-card:hover .card {
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}

/* Model Image Header Styles */
.card-image-header {
    height: 200px;
    overflow: hidden;
    position: relative;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.model-hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.model-card:hover .model-hero-image {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
}

.model-hero-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.model-info-overlay {
    z-index: 10;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0) 100%);
}

.model-card .card-title {
    color: #2d3748;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.model-icon-container {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #007bff 100%);
    border-radius: 50%;
    border: 3px solid #007bff;
    color: white;
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}

.stat-item {
    padding: 0.75rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.stat-item:hover {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    transform: scale(1.05);
}

/* Responsive image height */
@media (max-width: 768px) {
    .card-image-header {
        height: 180px;
    }
}

@media (max-width: 576px) {
    .card-image-header {
        height: 160px;
    }
}

.stat-number {
    font-size: 1.25rem;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.8rem;
    margin-top: 0.25rem;
    opacity: 0.8;
}

.model-card .btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.model-card .btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}

.model-card .btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
}

.model-card .btn-outline-success {
    border: 2px solid #28a745;
    color: #28a745;
    background: transparent;
}

.model-card .btn-outline-success:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
}

/* Empty State */
.empty-state {
    padding: 3rem 1rem;
}

.empty-state i {
    opacity: 0.5;
}

/* Modal Enhancements */
.modal-content {
    border: none;
    border-radius: 1rem;
    overflow: hidden;
}

.modal-header {
    border-bottom: 1px solid #dee2e6;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .model-icon-container {
        width: 70px;
        height: 70px;
    }

    .model-icon-container i {
        font-size: 2.5rem;
    }

    .model-card .card-title {
        font-size: 1.1rem;
    }

    .stat-number {
        font-size: 1rem;
    }

    .model-card .card-value {
        font-size: 1.8rem;
    }
}

@media (max-width: 576px) {
    .model-icon-container {
        width: 60px;
        height: 60px;
    }

    .model-icon-container i {
        font-size: 2rem;
    }

    .model-card .card-title {
        font-size: 1rem;
    }

    .stat-number {
        font-size: 0.9rem;
    }

    .model-card .card-value {
        font-size: 1.5rem;
    }
}
</style>

<script>
// Model search and filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('modelSearchInput');
    const sortSelect = document.getElementById('modelSortSelect');
    const yearFilter = document.getElementById('yearFilter');
    const modelsContainer = document.getElementById('modelsContainer');
    const noResults = document.getElementById('noModelsFound');

    let allModels = Array.from(modelsContainer.children).filter(child => child.classList.contains('col-xl-3'));
    let filteredModels = [...allModels];

    // Search functionality
    searchInput.addEventListener('input', debounce(function() {
        filterModels();
    }, 300));

    // Sort functionality
    sortSelect.addEventListener('change', function() {
        sortModels();
    });

    // Year filter functionality
    yearFilter.addEventListener('change', function() {
        filterModels();
    });

    function filterModels() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const yearRange = yearFilter.value;

        filteredModels = allModels.filter(model => {
            const modelName = model.dataset.modelName;
            const yearFrom = parseInt(model.dataset.yearFrom) || 0;
            const yearTo = parseInt(model.dataset.yearTo) || 9999;

            // Search filter
            const matchesSearch = !searchTerm || modelName.includes(searchTerm);

            // Year filter
            let matchesYear = true;
            if (yearRange) {
                const currentYear = new Date().getFullYear();
                switch (yearRange) {
                    case '2020+':
                        matchesYear = yearTo >= 2020;
                        break;
                    case '2010-2019':
                        matchesYear = yearTo >= 2010 && yearFrom <= 2019;
                        break;
                    case '2000-2009':
                        matchesYear = yearTo >= 2000 && yearFrom <= 2009;
                        break;
                    case '1990-1999':
                        matchesYear = yearTo >= 1990 && yearFrom <= 1999;
                        break;
                    case 'pre-1990':
                        matchesYear = yearTo < 1990;
                        break;
                }
            }

            return matchesSearch && matchesYear;
        });

        updateDisplay();
    }

    function sortModels() {
        const sortBy = sortSelect.value;

        filteredModels.sort((a, b) => {
            const nameA = a.dataset.modelName;
            const nameB = b.dataset.modelName;

            switch (sortBy) {
                case 'name':
                    return nameA.localeCompare(nameB);
                case 'products':
                    const productsA = parseInt(a.dataset.products) || 0;
                    const productsB = parseInt(b.dataset.products) || 0;
                    return productsB - productsA; // Most products first
                case 'year':
                    const yearA = parseInt(a.dataset.yearFrom) || 0;
                    const yearB = parseInt(b.dataset.yearFrom) || 0;
                    return yearB - yearA; // Newest first
                default:
                    return nameA.localeCompare(nameB);
            }
        });

        updateDisplay();
    }

    function updateDisplay() {
        // Clear container
        const container = document.getElementById('modelsContainer');
        const existingModels = container.querySelectorAll('.col-xl-3');
        existingModels.forEach(model => model.remove());

        if (filteredModels.length === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
            filteredModels.forEach(model => {
                container.appendChild(model);
            });
        }
    }

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
});

function clearModelFilters() {
    document.getElementById('modelSearchInput').value = '';
    document.getElementById('modelSortSelect').value = 'name';
    document.getElementById('yearFilter').value = '';
    document.getElementById('modelSearchInput').dispatchEvent(new Event('input'));
}

function quickViewModelParts(modelId, modelName) {
    const modal = new bootstrap.Modal(document.getElementById('modelPartsModal'));
    const content = document.getElementById('modelPartsContent');
    const modalTitle = document.getElementById('modalModelName');
    const viewAllLink = document.getElementById('viewAllPartsLink');

    modalTitle.textContent = modelName;
    viewAllLink.href = `shop.php?brand=<?php echo urlencode($brand['brand_name']); ?>&model=${encodeURIComponent(modelName)}`;

    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading parts...</span>
            </div>
            <p class="mt-3 text-muted">Loading available parts...</p>
        </div>
    `;

    modal.show();

    // Fetch model parts
    fetch(`../api/get_products.php?model_id=${modelId}&limit=6`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.products.length > 0) {
                let html = '<div class="row g-3">';
                data.products.forEach(product => {
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <img src="${product.image}" alt="${product.name}" class="img-fluid mb-2" style="height: 80px; object-fit: contain;" onerror="this.src='/img/no-image.png'">
                                    <h6 class="card-title small">${product.name}</h6>
                                    <p class="card-text text-primary fw-bold">RWF ${product.price.toLocaleString()}</p>
                                    <span class="badge ${getStockBadgeClass(product.stock_status)}">${product.stock_status}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-info-circle fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Parts Available</h5>
                        <p class="text-muted">No parts are currently available for this model.</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading model parts:', error);
            content.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-exclamation-triangle fa-3x text-danger mb-3"></i>
                    <h5 class="text-danger">Error Loading Parts</h5>
                    <p class="text-muted">Please try again later.</p>
                </div>
            `;
        });
}

function getStockBadgeClass(status) {
    switch (status) {
        case 'In Stock': return 'bg-success';
        case 'Low Stock': return 'bg-warning text-dark';
        case 'Special Order': return 'bg-info';
        default: return 'bg-secondary';
    }
}
</script>

<?php include '../includes/footer.php'; ?></content>
</xai:function_call">Create a models page that displays all models for a specific brand, with filtering and search capabilities. Users can browse models and navigate to parts for specific models.