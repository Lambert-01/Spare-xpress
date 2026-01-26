<?php
$page_title = 'Browse Brands - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

// Get all brands with product counts
$brands_query = $conn->query("
    SELECT vb.*,
           COUNT(DISTINCT vm.id) as model_count,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count
    FROM vehicle_brands_enhanced vb
    LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
    LEFT JOIN products_enhanced p ON vb.id = p.brand_id
    WHERE vb.is_active = 1
    GROUP BY vb.id
    ORDER BY vb.brand_name
");

$brands = [];
while ($brand = $brands_query->fetch_assoc()) {
    $brands[] = $brand;
}
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 text-white fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <i class="fas fa-tags me-3"></i>Browse Our Brands
                </h1>
                <p class="lead text-white-50 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    Discover genuine auto parts from trusted brands. Find the perfect parts for your vehicle across all major manufacturers.
                </p>
                <div class="d-flex gap-3 flex-wrap wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-shield-alt fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold"><?php echo count($brands); ?> Premium Brands</h6>
                            <small class="text-white-50">Worldwide Manufacturers</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-car fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Complete Coverage</h6>
                            <small class="text-white-50">All Vehicle Types</small>
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
                    <img src="/img/logo/logox.jpg" alt="SPARE XPRESS" class="img-fluid rounded shadow-lg" style="max-width: 280px; filter: brightness(1.1);">
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
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-tags me-1"></i>Browse Brands
        </li>
    </ol>
</nav>

<!-- Brands Section Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Our Partner Brands</h4>
            <h1 class="mb-0 display-5 wow fadeInUp" data-wow-delay="0.3s">Choose Your Vehicle Brand</h1>
            <p class="text-muted mt-2 wow fadeInUp" data-wow-delay="0.5s">Select your vehicle's manufacturer to browse compatible parts and accessories</p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 rounded-3 shadow-sm">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-8">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-search me-1 text-primary"></i>Search Brands
                            </label>
                            <input type="text" class="form-control form-control-lg" id="brandSearchInput" placeholder="Search for your brand...">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="fas fa-sort me-1 text-primary"></i>Sort By
                            </label>
                            <select class="form-select form-select-lg" id="brandSortSelect">
                                <option value="name">Name (A-Z)</option>
                                <option value="products">Most Products</option>
                                <option value="newest">Recently Added</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands Statistics -->
        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-lg-6">
                <div class="stats-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                            <i class="bi bi-tags-fill fs-1"></i>
                        </div>
                        <h3 class="card-value text-primary mb-2"><?php echo count($brands); ?></h3>
                        <p class="card-title mb-0">Total Brands</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="stats-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                            <i class="bi bi-car-front-fill fs-1"></i>
                        </div>
                        <h3 class="card-value text-success mb-2">
                            <?php
                            $total_models = array_sum(array_column($brands, 'model_count'));
                            echo number_format($total_models);
                            ?>
                        </h3>
                        <p class="card-title mb-0">Total Models</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="stats-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                            <i class="bi bi-box-seam-fill fs-1"></i>
                        </div>
                        <h3 class="card-value text-warning mb-2">
                            <?php
                            $total_products = array_sum(array_column($brands, 'active_product_count'));
                            echo number_format($total_products);
                            ?>
                        </h3>
                        <p class="card-title mb-0">Active Products</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="stats-card">
                    <div class="card-body text-center p-4">
                        <div class="card-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                            <i class="bi bi-globe fs-1"></i>
                        </div>
                        <h3 class="card-value text-info mb-2">
                            <?php
                            $countries = array_unique(array_column($brands, 'country'));
                            echo count(array_filter($countries));
                            ?>
                        </h3>
                        <p class="card-title mb-0">Countries</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brands Grid -->
        <div class="row g-4" id="brandsContainer">
            <?php foreach ($brands as $brand): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                    <div class="brand-card h-100" data-brand-name="<?php echo htmlspecialchars(strtolower($brand['brand_name'])); ?>">
                        <div class="card border-0 shadow-sm h-100">
                            <!-- Brand Header with Logo -->
                            <div class="card-header bg-white border-0 text-center py-4">
                                <div class="brand-logo-container mb-3">
                                    <?php
                                    $brandName = strtolower($brand['brand_name']);
                                    $iconClass = 'fas fa-car'; // Default icon

                                    // Map common brands to Font Awesome icons
                                    $brandIcons = [
                                        'toyota' => 'fas fa-car',
                                        'nissan' => 'fas fa-car',
                                        'hyundai' => 'fas fa-car',
                                        'kia' => 'fas fa-car',
                                        'byd' => 'fas fa-car',
                                        'mitsubishi' => 'fas fa-car',
                                        'suzuki' => 'fas fa-car',
                                        'volkswagen' => 'fas fa-car',
                                        'mercedes' => 'fas fa-car',
                                        'bmw' => 'fas fa-car',
                                        'audi' => 'fas fa-car',
                                        'honda' => 'fas fa-car',
                                        'ford' => 'fas fa-car',
                                        'chevrolet' => 'fas fa-car',
                                        'mazda' => 'fas fa-car',
                                        'subaru' => 'fas fa-car',
                                        'lexus' => 'fas fa-car',
                                        'infiniti' => 'fas fa-car',
                                        'acura' => 'fas fa-car',
                                        'cadillac' => 'fas fa-car',
                                        'jeep' => 'fas fa-car',
                                        'chrysler' => 'fas fa-car',
                                        'dodge' => 'fas fa-car',
                                        'ram' => 'fas fa-truck',
                                        'tesla' => 'fas fa-bolt',
                                        'porsche' => 'fas fa-car',
                                        'ferrari' => 'fas fa-car',
                                        'lamborghini' => 'fas fa-car',
                                        'bentley' => 'fas fa-car',
                                        'rolls-royce' => 'fas fa-car',
                                        'aston martin' => 'fas fa-car',
                                        'jaguar' => 'fas fa-car',
                                        'land rover' => 'fas fa-car',
                                        'volvo' => 'fas fa-car',
                                        'saab' => 'fas fa-car',
                                        'scania' => 'fas fa-truck',
                                        'iveco' => 'fas fa-truck',
                                        'daf' => 'fas fa-truck',
                                        'man' => 'fas fa-truck',
                                        'renault' => 'fas fa-car',
                                        'peugeot' => 'fas fa-car',
                                        'citroen' => 'fas fa-car',
                                        'fiat' => 'fas fa-car',
                                        'alfa romeo' => 'fas fa-car',
                                        'maserati' => 'fas fa-car',
                                        'lancia' => 'fas fa-car',
                                        'abarth' => 'fas fa-car',
                                        'skoda' => 'fas fa-car',
                                        'seat' => 'fas fa-car',
                                        'opel' => 'fas fa-car',
                                        'vauxhall' => 'fas fa-car',
                                        'mini' => 'fas fa-car',
                                        'smart' => 'fas fa-car',
                                        'maybach' => 'fas fa-car',
                                        'smart' => 'fas fa-car',
                                        'porsche' => 'fas fa-car',
                                        'lotus' => 'fas fa-car',
                                        'mclaren' => 'fas fa-car',
                                        'morgan' => 'fas fa-car',
                                        'caterham' => 'fas fa-car',
                                        'radical' => 'fas fa-car',
                                        'ginetta' => 'fas fa-car',
                                        'caparo' => 'fas fa-car',
                                        'noble' => 'fas fa-car',
                                        'pagani' => 'fas fa-car',
                                        'koenigsegg' => 'fas fa-car',
                                        'bugatti' => 'fas fa-car',
                                        'saleen' => 'fas fa-car',
                                        'shelby' => 'fas fa-car',
                                        'pontiac' => 'fas fa-car',
                                        'buick' => 'fas fa-car',
                                        'lincoln' => 'fas fa-car',
                                        'mercury' => 'fas fa-car',
                                        'saturn' => 'fas fa-car',
                                        'hummer' => 'fas fa-car',
                                        'gmc' => 'fas fa-truck',
                                        'isuzu' => 'fas fa-truck',
                                        'mack' => 'fas fa-truck',
                                        'peterbilt' => 'fas fa-truck',
                                        'kenworth' => 'fas fa-truck',
                                        'freightliner' => 'fas fa-truck',
                                        'western star' => 'fas fa-truck',
                                        'international' => 'fas fa-truck',
                                        'sterling' => 'fas fa-truck',
                                        'prevost' => 'fas fa-bus',
                                        'beaver' => 'fas fa-bus',
                                        'blue bird' => 'fas fa-bus',
                                        'thomas' => 'fas fa-bus',
                                        'gillig' => 'fas fa-bus',
                                        'new flyer' => 'fas fa-bus',
                                        'motor coach' => 'fas fa-bus',
                                        'prevost' => 'fas fa-bus',
                                        'setra' => 'fas fa-bus',
                                        'neoplan' => 'fas fa-bus',
                                        'van hool' => 'fas fa-bus',
                                        'bova' => 'fas fa-bus',
                                        'irkut' => 'fas fa-plane',
                                        'tupolev' => 'fas fa-plane',
                                        'ilyushin' => 'fas fa-plane',
                                        'antonov' => 'fas fa-plane',
                                        'beriev' => 'fas fa-plane',
                                        'sukhoi' => 'fas fa-plane',
                                        'mikoyan' => 'fas fa-plane',
                                        'yakovlev' => 'fas fa-plane',
                                        'tupolev' => 'fas fa-plane',
                                        'kamov' => 'fas fa-helicopter',
                                        'mil' => 'fas fa-helicopter',
                                        'bell' => 'fas fa-helicopter',
                                        'sikorsky' => 'fas fa-helicopter',
                                        'agusta' => 'fas fa-helicopter',
                                        'eurocopter' => 'fas fa-helicopter',
                                        'boeing' => 'fas fa-plane',
                                        'airbus' => 'fas fa-plane',
                                        'embraer' => 'fas fa-plane',
                                        'bombardier' => 'fas fa-plane',
                                        'cessna' => 'fas fa-plane',
                                        'piper' => 'fas fa-plane',
                                        'cirrus' => 'fas fa-plane',
                                        'diamond' => 'fas fa-plane',
                                        ' Mooney' => 'fas fa-plane',
                                        'maule' => 'fas fa-plane',
                                        'cub crafters' => 'fas fa-plane',
                                        'icon' => 'fas fa-plane',
                                        'sea ray' => 'fas fa-ship',
                                        'bayliner' => 'fas fa-ship',
                                        'chaparral' => 'fas fa-ship',
                                        'cruisers' => 'fas fa-ship',
                                        'formula' => 'fas fa-ship',
                                        'four winns' => 'fas fa-ship',
                                        'glastron' => 'fas fa-ship',
                                        'larson' => 'fas fa-ship',
                                        'maxum' => 'fas fa-ship',
                                        'monterey' => 'fas fa-ship',
                                        'regal' => 'fas fa-ship',
                                        'ranger' => 'fas fa-ship',
                                        'stingray' => 'fas fa-ship',
                                        'tige' => 'fas fa-ship',
                                        'tracker' => 'fas fa-ship',
                                        'trojan' => 'fas fa-ship',
                                        'wellcraft' => 'fas fa-ship',
                                        'yamaha' => 'fas fa-motorcycle',
                                        'honda' => 'fas fa-motorcycle',
                                        'kawasaki' => 'fas fa-motorcycle',
                                        'suzuki' => 'fas fa-motorcycle',
                                        'harley davidson' => 'fas fa-motorcycle',
                                        'ducati' => 'fas fa-motorcycle',
                                        'bmw' => 'fas fa-motorcycle',
                                        'triumph' => 'fas fa-motorcycle',
                                        'aprilia' => 'fas fa-motorcycle',
                                        'ktm' => 'fas fa-motorcycle',
                                        'husqvarna' => 'fas fa-motorcycle',
                                        'mv agusta' => 'fas fa-motorcycle',
                                        'indian' => 'fas fa-motorcycle',
                                        'royal enfield' => 'fas fa-motorcycle',
                                        'vespa' => 'fas fa-scooter',
                                        'piaggio' => 'fas fa-scooter',
                                        'gilera' => 'fas fa-scooter',
                                        'lambretta' => 'fas fa-scooter',
                                        'adly' => 'fas fa-scooter',
                                        'sym' => 'fas fa-scooter',
                                        'kyburz' => 'fas fa-scooter',
                                        'rieju' => 'fas fa-scooter',
                                        'baotian' => 'fas fa-scooter',
                                        'jonway' => 'fas fa-scooter',
                                        'zongshen' => 'fas fa-scooter',
                                        'loncin' => 'fas fa-scooter',
                                        'lifan' => 'fas fa-scooter',
                                        'zongshen' => 'fas fa-scooter',
                                        'dayang' => 'fas fa-scooter',
                                        'tgb' => 'fas fa-scooter',
                                        'zipper' => 'fas fa-scooter',
                                        'ecScooter' => 'fas fa-scooter',
                                        'askoll' => 'fas fa-scooter',
                                        'govecs' => 'fas fa-scooter',
                                        'super socco' => 'fas fa-scooter',
                                        'taotao' => 'fas fa-scooter',
                                        'roketa' => 'fas fa-scooter',
                                        'bmw' => 'fas fa-bicycle',
                                        'trek' => 'fas fa-bicycle',
                                        'specialized' => 'fas fa-bicycle',
                                        'cannondale' => 'fas fa-bicycle',
                                        'giant' => 'fas fa-bicycle',
                                        'santa cruz' => 'fas fa-bicycle',
                                        'yeti' => 'fas fa-bicycle',
                                        'pivot' => 'fas fa-bicycle',
                                        'ibis' => 'fas fa-bicycle',
                                        'norco' => 'fas fa-bicycle',
                                        'devinci' => 'fas fa-bicycle',
                                        'salsa' => 'fas fa-bicycle',
                                        'surly' => 'fas fa-bicycle',
                                        'all city' => 'fas fa-bicycle',
                                        'co-op' => 'fas fa-bicycle',
                                        'revel' => 'fas fa-bicycle',
                                        'diamondback' => 'fas fa-bicycle',
                                        'gt' => 'fas fa-bicycle',
                                        'huffy' => 'fas fa-bicycle',
                                        'kent' => 'fas fa-bicycle',
                                        'mongoose' => 'fas fa-bicycle',
                                        'next' => 'fas fa-bicycle',
                                        'pacific' => 'fas fa-bicycle',
                                        'schwinn' => 'fas fa-bicycle',
                                        'sears' => 'fas fa-bicycle',
                                        'western flyer' => 'fas fa-bicycle',
                                        'john deere' => 'fas fa-tractor',
                                        'case ih' => 'fas fa-tractor',
                                        'new holland' => 'fas fa-tractor',
                                        'claas' => 'fas fa-tractor',
                                        'fendt' => 'fas fa-tractor',
                                        'deutz-fahr' => 'fas fa-tractor',
                                        'same' => 'fas fa-tractor',
                                        'lander' => 'fas fa-tractor',
                                        'mccormick' => 'fas fa-tractor',
                                        'arbos' => 'fas fa-tractor',
                                        'pottinger' => 'fas fa-tractor',
                                        'kverneland' => 'fas fa-tractor',
                                        'vaderstad' => 'fas fa-tractor',
                                        'amazone' => 'fas fa-tractor',
                                        'lemken' => 'fas fa-tractor',
                                        'grimme' => 'fas fa-tractor',
                                        'rabe' => 'fas fa-tractor',
                                        'holmer' => 'fas fa-tractor',
                                        'capello' => 'fas fa-tractor',
                                        'massey ferguson' => 'fas fa-tractor',
                                        'fiatagri' => 'fas fa-tractor',
                                        'ford' => 'fas fa-tractor',
                                        'international harvester' => 'fas fa-tractor',
                                        'allied' => 'fas fa-tractor',
                                        'oliver' => 'fas fa-tractor',
                                        'minneapolis moline' => 'fas fa-tractor',
                                        'co-op' => 'fas fa-tractor',
                                        'versatile' => 'fas fa-tractor',
                                        'challenger' => 'fas fa-tractor',
                                        'agco' => 'fas fa-tractor',
                                        'white' => 'fas fa-tractor',
                                        'hesston' => 'fas fa-tractor',
                                        'gehl' => 'fas fa-tractor',
                                        'kubota' => 'fas fa-tractor',
                                        'yanmar' => 'fas fa-tractor',
                                        'iseki' => 'fas fa-tractor',
                                        'shibaura' => 'fas fa-tractor',
                                        'mitsubishi' => 'fas fa-tractor',
                                        'hitachi' => 'fas fa-tractor',
                                        'komatsu' => 'fas fa-tractor',
                                        'caterpillar' => 'fas fa-tractor',
                                        'liebherr' => 'fas fa-tractor',
                                        'volvo' => 'fas fa-tractor',
                                        'jcb' => 'fas fa-tractor',
                                        'bobcat' => 'fas fa-tractor',
                                        'takeuchi' => 'fas fa-tractor',
                                        'gehl' => 'fas fa-tractor',
                                        'mustang' => 'fas fa-tractor',
                                        'ditch witch' => 'fas fa-tractor',
                                        'vermeer' => 'fas fa-tractor',
                                        'ditch witch' => 'fas fa-tractor',
                                        'torque' => 'fas fa-tractor',
                                        'earthforce' => 'fas fa-tractor',
                                        'bandit' => 'fas fa-tractor',
                                        'morbark' => 'fas fa-tractor',
                                        'timberjack' => 'fas fa-tractor',
                                        'john deere' => 'fas fa-tractor',
                                        'timberking' => 'fas fa-tractor',
                                        'barko' => 'fas fa-tractor',
                                        'hydro-ax' => 'fas fa-tractor',
                                        'waratah' => 'fas fa-tractor',
                                        'keto' => 'fas fa-tractor',
                                        'risley' => 'fas fa-tractor',
                                        'denharco' => 'fas fa-tractor',
                                        'peterson' => 'fas fa-tractor',
                                        'precision husky' => 'fas fa-tractor',
                                        'tigercat' => 'fas fa-tractor',
                                        'ponsse' => 'fas fa-tractor',
                                        'komatsu' => 'fas fa-tractor',
                                        'hitachi' => 'fas fa-tractor',
                                        'caterpillar' => 'fas fa-tractor',
                                        'liebherr' => 'fas fa-tractor',
                                        'volvo' => 'fas fa-tractor',
                                        'jcb' => 'fas fa-tractor',
                                        'bobcat' => 'fas fa-tractor',
                                        'takeuchi' => 'fas fa-tractor',
                                        'gehl' => 'fas fa-tractor',
                                        'mustang' => 'fas fa-tractor',
                                        'ditch witch' => 'fas fa-tractor',
                                        'vermeer' => 'fas fa-tractor',
                                        'ditch witch' => 'fas fa-tractor',
                                        'torque' => 'fas fa-tractor',
                                        'earthforce' => 'fas fa-tractor',
                                        'bandit' => 'fas fa-tractor',
                                        'morbark' => 'fas fa-tractor',
                                        'timberjack' => 'fas fa-tractor',
                                        'john deere' => 'fas fa-tractor',
                                        'timberking' => 'fas fa-tractor',
                                        'barko' => 'fas fa-tractor',
                                        'hydro-ax' => 'fas fa-tractor',
                                        'waratah' => 'fas fa-tractor',
                                        'keto' => 'fas fa-tractor',
                                        'risley' => 'fas fa-tractor',
                                        'denharco' => 'fas fa-tractor',
                                        'peterson' => 'fas fa-tractor',
                                        'precision husky' => 'fas fa-tractor',
                                        'tigercat' => 'fas fa-tractor',
                                        'ponsse' => 'fas fa-tractor',
                                        'komatsu' => 'fas fa-tractor',
                                        'hitachi' => 'fas fa-tractor',
                                        'caterpillar' => 'fas fa-tractor',
                                        'liebherr' => 'fas fa-tractor',
                                        'volvo' => 'fas fa-tractor',
                                        'jcb' => 'fas fa-tractor',
                                        'bobcat' => 'fas fa-tractor',
                                        'takeuchi' => 'fas fa-tractor',
                                        'gehl' => 'fas fa-tractor',
                                        'mustang' => 'fas fa-tractor',
                                        'ditch witch' => 'fas fa-tractor',
                                        'vermeer' => 'fas fa-tractor',
                                        'ditch witch' => 'fas fa-tractor',
                                        'torque' => 'fas fa-tractor',
                                        'earthforce' => 'fas fa-tractor',
                                        'bandit' => 'fas fa-tractor',
                                        'morbark' => 'fas fa-tractor',
                                        'timberjack' => 'fas fa-tractor',
                                        'john deere' => 'fas fa-tractor',
                                        'timberking' => 'fas fa-tractor',
                                        'barko' => 'fas fa-tractor',
                                        'hydro-ax' => 'fas fa-tractor',
                                        'waratah' => 'fas fa-tractor',
                                        'keto' => 'fas fa-tractor',
                                        'risley' => 'fas fa-tractor',
                                        'denharco' => 'fas fa-tractor',
                                        'peterson' => 'fas fa-tractor',
                                        'precision husky' => 'fas fa-tractor',
                                        'tigercat' => 'fas fa-tractor',
                                        'ponsse' => 'fas fa-tractor'
                                    ];

                                    if (isset($brandIcons[$brandName])) {
                                        $iconClass = $brandIcons[$brandName];
                                    }
                                    ?>
                                    <?php
                                    $brandSlug = strtolower(str_replace([' ', '-'], '', $brand['brand_name']));
                                    $brandNameUpper = strtoupper($brand['brand_name']);

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

                                    // Try uploaded logos first (let browser handle loading/errors)
                                    $logoPath = null;
                                    if (isset($uploadedLogos[$brand['brand_name']])) {
                                        $logoPath = "/uploads/brands/" . $uploadedLogos[$brand['brand_name']];
                                    }

                                    if ($logoPath): ?>
                                        <img src="<?php echo $logoPath; ?>"
                                             alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                                             class="brand-logo"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                        <i class="fas fa-car fa-3x text-primary" style="display: none;"></i>
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

                                        // Brands with custom logo URLs (free car logo service)
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

                                        if (in_array($brandSlug, $simpleIconBrands)): ?>
                                            <img src="https://cdn.simpleicons.org/<?php echo $brandSlug; ?>/007bff"
                                                 alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                                                 class="brand-logo"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <i class="fas fa-car fa-3x text-primary" style="display: none;"></i>
                                        <?php elseif (isset($customLogoBrands[$brandSlug])): ?>
                                            <img src="<?php echo $customLogoBrands[$brandSlug]; ?>"
                                                 alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                                                 class="brand-logo"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <i class="fas fa-car fa-3x text-primary" style="display: none;"></i>
                                        <?php else: ?>
                                            <!-- Text-based logo for brands without images -->
                                            <div class="text-logo">
                                                <span class="brand-initial"><?php echo strtoupper(substr($brand['brand_name'], 0, 1)); ?></span>
                                                <span class="brand-text"><?php echo htmlspecialchars($brand['brand_name']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title mb-2 fw-bold"><?php echo htmlspecialchars($brand['brand_name']); ?></h5>
                                <?php if ($brand['country']): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($brand['country']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body text-center">
                                <!-- Stats -->
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-primary fw-bold"><?php echo $brand['model_count']; ?></div>
                                            <div class="stat-label small text-muted">Models</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-success fw-bold"><?php echo $brand['active_product_count']; ?></div>
                                            <div class="stat-label small text-muted">Parts</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <div class="stat-number text-info fw-bold"><?php echo $brand['product_count']; ?></div>
                                            <div class="stat-label small text-muted">Total</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <?php if ($brand['description']): ?>
                                    <p class="card-text small text-muted mb-3">
                                        <?php echo htmlspecialchars(substr($brand['description'], 0, 80)); ?>
                                        <?php if (strlen($brand['description']) > 80): ?>...<?php endif; ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-light border-0">
                                <div class="d-grid gap-2">
                                    <a href="models.php?brand=<?php echo urlencode($brand['slug']); ?>"
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-car me-1"></i>Browse Models
                                    </a>
                                    <a href="shop.php?brand=<?php echo urlencode($brand['brand_name']); ?>"
                                       class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-boxes me-1"></i>View Parts
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results -->
        <div id="noBrandsFound" class="text-center py-5 bg-white rounded-3 shadow-sm d-none">
            <i class="bi bi-search fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-3">No brands found</h4>
            <p class="text-muted">Try adjusting your search terms.</p>
            <button class="btn btn-primary" onclick="clearBrandSearch()">Clear Search</button>
        </div>
    </div>
</div>
<!-- Brands Section End -->

<!-- Popular Categories Section -->
<div class="container-fluid py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Popular Categories</h4>
            <h1 class="mb-0 display-5 wow fadeInUp" data-wow-delay="0.3s">Shop by Part Type</h1>
            <p class="text-muted mt-2 wow fadeInUp" data-wow-delay="0.5s">Find the specific parts you need for your vehicle</p>
        </div>

        <div class="row g-4">
            <?php
            $categories = $conn->query("SELECT * FROM categories ORDER BY category_name LIMIT 8");
            while ($category = $categories->fetch_assoc()):
            ?>
                <div class="col-lg-3 col-md-6">
                    <a href="shop.php?category=<?php echo urlencode($category['slug']); ?>" class="text-decoration-none">
                        <div class="category-card-enhanced h-100">
                            <div class="category-icon mb-3">
                                <i class="<?php echo $category['icon']; ?> fa-3x text-primary"></i>
                            </div>
                            <h5 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <p class="category-description text-muted small">
                                <?php echo htmlspecialchars(substr($category['description'] ?? 'Browse our selection of ' . $category['name'], 0, 80)); ?>...
                            </p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
/* Brand Card Styles */
.brand-card {
    transition: all 0.3s ease;
}

.brand-card:hover {
    transform: translateY(-5px);
}

.brand-logo-container {
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

.brand-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 50%;
}

.text-logo {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border-radius: 50%;
    color: white;
    font-weight: bold;
    text-align: center;
    padding: 8px;
}

.brand-initial {
    font-size: 24px;
    line-height: 1;
    margin-bottom: 2px;
}

.brand-text {
    font-size: 10px;
    line-height: 1;
    word-break: break-word;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.stat-item {
    padding: 0.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: #e9ecef;
}

.stat-number {
    font-size: 1.2rem;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

/* Category Card Styles */
.category-card-enhanced {
    padding: 2rem 1.5rem;
    text-align: center;
    border: 1px solid #e9ecef !important;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: white;
    text-decoration: none;
    color: inherit;
}

.category-card-enhanced:hover {
    border-color: #007bff !important;
    box-shadow: 0 8px 25px rgba(0,123,255,0.1);
    transform: translateY(-4px);
    text-decoration: none;
    color: inherit;
}

.category-icon {
    margin-bottom: 1rem;
}

.category-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.category-description {
    line-height: 1.5;
}

/* Stats Card Styles */
.stats-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
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

/* Enhanced Visual Design */
.card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.card:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    transform: translateY(-5px);
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-bottom: 1px solid #e9ecef;
}

.card-title {
    color: #2d3748;
    font-weight: 700;
    margin-bottom: 0.5rem;
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

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
}

.btn-outline-success {
    border: 2px solid #28a745;
    color: #28a745;
    background: transparent;
}

.btn-outline-success:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
}

/* Category Card Enhancements */
.category-card-enhanced {
    padding: 2rem 1.5rem;
    text-align: center;
    border: 2px solid #e9ecef !important;
    border-radius: 15px;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    text-decoration: none;
    color: inherit;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.category-card-enhanced:hover {
    border-color: #007bff !important;
    box-shadow: 0 8px 30px rgba(0,123,255,0.15);
    transform: translateY(-5px);
    text-decoration: none;
    color: inherit;
}

.category-icon {
    margin-bottom: 1rem;
    color: #007bff;
    transition: all 0.3s ease;
}

.category-card-enhanced:hover .category-icon {
    transform: scale(1.1);
    color: #0056b3;
}

.category-title {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.category-description {
    line-height: 1.5;
    color: #6c757d;
}

/* Stats Card Enhancements */
.stats-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
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
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}

.card-value {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.card-title {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
    font-weight: 600;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .brand-logo-container {
        width: 70px;
        height: 70px;
    }

    .brand-logo-container i {
        font-size: 2.5rem;
    }

    .card-value {
        font-size: 2rem;
    }

    .stat-number {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .brand-logo-container {
        width: 60px;
        height: 60px;
    }

    .brand-logo-container i {
        font-size: 2rem;
    }

    .card-title {
        font-size: 1rem;
    }

    .stat-number {
        font-size: 0.9rem;
    }
}
</style>

<script>
// Enhanced brand search and filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('brandSearchInput');
    const sortSelect = document.getElementById('brandSortSelect');
    const brandsContainer = document.getElementById('brandsContainer');
    const noResults = document.getElementById('noBrandsFound');

    let allBrands = Array.from(brandsContainer.children);
    let filteredBrands = [...allBrands];

    // Store original brand data for sorting
    allBrands.forEach((brand, index) => {
        const brandCard = brand.querySelector('.brand-card');
        const statItems = brandCard.querySelectorAll('.stat-number');

        brand.brandData = {
            name: brandCard.dataset.brandName,
            models: parseInt(statItems[0]?.textContent || 0),
            parts: parseInt(statItems[1]?.textContent || 0),
            total: parseInt(statItems[2]?.textContent || 0),
            index: index // Preserve original order
        };
    });

    // Search functionality - instant filtering
    searchInput.addEventListener('input', function() {
        filterBrands();
    });

    // Sort functionality - instant sorting
    sortSelect.addEventListener('change', function() {
        sortBrands();
    });

    // Auto-focus search on page load
    searchInput.focus();

    function filterBrands() {
        const searchTerm = searchInput.value.toLowerCase().trim();

        if (!searchTerm) {
            filteredBrands = [...allBrands];
        } else {
            filteredBrands = allBrands.filter(brand => {
                const brandName = brand.brandData.name.toLowerCase();
                const brandCard = brand.querySelector('.card-title');
                const fullName = brandCard ? brandCard.textContent.toLowerCase() : brandName;
    
                // Search in brand name and full display name
                return brandName.includes(searchTerm) || fullName.includes(searchTerm);
            });
        }

        // Re-sort after filtering
        sortBrands();
    }

    function sortBrands() {
        const sortBy = sortSelect.value;

        filteredBrands.sort((a, b) => {
            switch (sortBy) {
                case 'name':
                    return a.brandData.name.localeCompare(b.brandData.name);
                case 'products':
                    // Sort by active parts count (descending)
                    return b.brandData.parts - a.brandData.parts;
                case 'newest':
                    // Sort by total products (as proxy for activity)
                    return b.brandData.total - a.brandData.total;
                default:
                    return a.brandData.index - b.brandData.index; // Original order
            }
        });

        updateDisplay();
    }

    function updateDisplay() {
        // Clear container
        brandsContainer.innerHTML = '';

        if (filteredBrands.length === 0) {
            noResults.classList.remove('d-none');
            // Update no results message
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                noResults.querySelector('h4').textContent = `No brands found for "${searchTerm}"`;
            } else {
                noResults.querySelector('h4').textContent = 'No brands found';
            }
        } else {
            noResults.classList.add('d-none');

            // Add brands with animation delay
            filteredBrands.forEach((brand, index) => {
                const brandClone = brand.cloneNode(true);
                brandClone.brandData = brand.brandData; // Copy brandData to clone
                brandClone.style.animationDelay = `${index * 0.1}s`;
                brandClone.classList.add('fade-in');
                brandsContainer.appendChild(brandClone);
            });
        }

        // Update results count in header
        updateResultsCount();
    }

    function updateResultsCount() {
        const totalCount = allBrands.length;
        const filteredCount = filteredBrands.length;
        const searchTerm = searchInput.value.trim();

        // Update the header text to show current filter status
        const headerText = document.querySelector('.display-5');
        if (headerText) {
            if (searchTerm) {
                headerText.textContent = `Found ${filteredCount} brand${filteredCount !== 1 ? 's' : ''} for "${searchTerm}"`;
            } else {
                headerText.textContent = 'Choose Your Vehicle Brand';
            }
        }
    }

    // Initialize
    sortBrands(); // Apply initial sort
    updateResultsCount(); // Update initial count
});

// Enhanced clear search function
function clearBrandSearch() {
    const searchInput = document.getElementById('brandSearchInput');
    const sortSelect = document.getElementById('brandSortSelect');

    searchInput.value = '';
    sortSelect.value = 'name'; // Reset to default sort

    // Trigger events
    searchInput.dispatchEvent(new Event('input'));
    sortSelect.dispatchEvent(new Event('change'));
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K to focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('brandSearchInput').focus();
    }

    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('brandSearchInput');
        if (searchInput === document.activeElement && searchInput.value) {
            clearBrandSearch();
        }
    }
});
</script>

<style>
/* Enhanced filter animations */
.fade-in {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced search input styling */
#brandSearchInput {
    transition: all 0.3s ease;
}

#brandSearchInput:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    border-color: #007bff;
    transform: translateY(-1px);
}

/* Sort select enhancement */
#brandSortSelect {
    transition: all 0.3s ease;
}

#brandSortSelect:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    border-color: #007bff;
}

/* Loading state for filters */
.filter-loading {
    position: relative;
}

.filter-loading::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 10px;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<?php include '../includes/footer.php'; ?>