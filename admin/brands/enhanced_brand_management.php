<?php
ob_start();
// Enhanced Brand Management System for SPARE XPRESS LTD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('../logs/brand_management.log', date('Y-m-d H:i:s') . " - POST received\n", FILE_APPEND);
}

include '../includes/auth.php';
include '../includes/functions.php';
include '../logs/error_log.php';
include '../header.php';

// Custom logging for brand management
function logBrandAction($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "$timestamp - $message" . PHP_EOL;
    file_put_contents('../logs/brand_management.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_brand'])) {
        // Add new brand
        logBrandAction("Add brand form submitted");
        ErrorLogger::logSuccess("Add brand API called", ['post_data' => $_POST]);

        $brand_name = trim($_POST['brand_name']);
        $slug = generateSlug($brand_name);
        $description = trim($_POST['description']);
        $country = trim($_POST['country']);
        $founded_year = !empty($_POST['founded_year']) ? (int)$_POST['founded_year'] : null;
        $manufacturer_details = trim($_POST['manufacturer_details']);
        $website = trim($_POST['website']);
        $contact_email = trim($_POST['contact_email']);
        $contact_phone = trim($_POST['contact_phone']);
        $seo_title = trim($_POST['seo_title']);
        $seo_description = trim($_POST['seo_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle logo upload
        $logo_image = '';
        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/brands/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_extension = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_logo.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $target_path)) {
                $logo_image = '../uploads/brands/' . $file_name;
            }
        }

        // Handle brand image upload
        $brand_image = '';
        if (isset($_FILES['brand_image']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/brands/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_extension = pathinfo($_FILES['brand_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_brand.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['brand_image']['tmp_name'], $target_path)) {
                $brand_image = '../uploads/brands/' . $file_name;
            }
        }

        $stmt = $conn->prepare("INSERT INTO vehicle_brands_enhanced
            (brand_name, slug, logo_image, brand_image, description, country, founded_year,
             manufacturer_details, website, contact_email, contact_phone, seo_title, seo_description, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssissssssi",
            $brand_name, $slug, $logo_image, $brand_image, $description, $country, $founded_year,
            $manufacturer_details, $website, $contact_email, $contact_phone, $seo_title, $seo_description, $is_active);

        if ($stmt->execute()) {
            logBrandAction("Brand added successfully: $brand_name");
            ErrorLogger::logSuccess("Brand added successfully: $brand_name");
            $_SESSION['success'] = 'Brand added successfully!';
            header('Location: enhanced_brand_management.php');
            exit;
        } else {
            logBrandAction("Failed to add brand: $brand_name - Error: " . $conn->error);
            ErrorLogger::logError("Failed to add brand: $brand_name", ['error' => $conn->error]);
            $_SESSION['error'] = 'Failed to add brand: ' . $conn->error;
        }
    }

    if (isset($_POST['update_brand'])) {
        // Update existing brand
        logBrandAction("Update brand form submitted");
        ErrorLogger::logSuccess("Update brand API called", ['post_data' => $_POST]);

        $id = (int)$_POST['brand_id'];
        $brand_name = trim($_POST['brand_name']);
        $slug = generateSlug($brand_name);
        $description = trim($_POST['description']);
        $country = trim($_POST['country']);
        $founded_year = !empty($_POST['founded_year']) ? (int)$_POST['founded_year'] : null;
        $manufacturer_details = trim($_POST['manufacturer_details']);
        $website = trim($_POST['website']);
        $contact_email = trim($_POST['contact_email']);
        $contact_phone = trim($_POST['contact_phone']);
        $seo_title = trim($_POST['seo_title']);
        $seo_description = trim($_POST['seo_description']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        logBrandAction("Brand data prepared for ID: $id, Name: $brand_name");
        ErrorLogger::logSuccess("Brand data prepared", [
            'id' => $id,
            'brand_name' => $brand_name,
            'slug' => $slug,
            'is_active' => $is_active
        ]);

        // Handle logo upload
        $logo_image = $_POST['existing_logo'] ?? '';
        ErrorLogger::logSuccess("Existing logo: $logo_image");

        if (isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
            ErrorLogger::logSuccess("New logo upload detected", ['file' => $_FILES['logo_image']['name']]);

            $upload_dir = '../uploads/brands/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_extension = pathinfo($_FILES['logo_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_logo.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $target_path)) {
                $logo_image = '../uploads/brands/' . $file_name;
                ErrorLogger::logSuccess("Logo uploaded successfully: $logo_image");
            } else {
                ErrorLogger::logError("Failed to upload logo to $target_path");
            }
        }

        // Handle brand image upload
        $brand_image = $_POST['existing_brand_image'] ?? '';
        if (isset($_FILES['brand_image']) && $_FILES['brand_image']['error'] === UPLOAD_ERR_OK) {
            ErrorLogger::logSuccess("New brand image upload detected", ['file' => $_FILES['brand_image']['name']]);

            $upload_dir = '../uploads/brands/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_extension = pathinfo($_FILES['brand_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_brand.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['brand_image']['tmp_name'], $target_path)) {
                $brand_image = '../uploads/brands/' . $file_name;
                ErrorLogger::logSuccess("Brand image uploaded successfully: $brand_image");
            } else {
                ErrorLogger::logError("Failed to upload brand image to $target_path");
            }
        }

        $update_query = "UPDATE vehicle_brands_enhanced SET
            brand_name = ?, slug = ?, logo_image = ?, brand_image = ?, description = ?,
            country = ?, founded_year = ?, manufacturer_details = ?, website = ?,
            contact_email = ?, contact_phone = ?, seo_title = ?, seo_description = ?, is_active = ?
            WHERE id = ?";

        ErrorLogger::logSuccess("Update query prepared", ['query' => $update_query]);

        $stmt = $conn->prepare($update_query);

        $stmt->bind_param("ssssssissssssii",
            $brand_name, $slug, $logo_image, $brand_image, $description, $country, $founded_year,
            $manufacturer_details, $website, $contact_email, $contact_phone, $seo_title, $seo_description, $is_active, $id);

        ErrorLogger::logQuery($update_query, "ssssssissssssii", [
            $brand_name, $slug, $logo_image, $brand_image, $description, $country, $founded_year,
            $manufacturer_details, $website, $contact_email, $contact_phone, $seo_title, $seo_description, $is_active, $id
        ]);

        if ($stmt->execute()) {
            logBrandAction("Brand updated successfully for ID: $id");
            ErrorLogger::logSuccess("Brand updated successfully for ID: $id");
            $_SESSION['success'] = 'Brand updated successfully!';
            header('Location: enhanced_brand_management.php');
            exit;
        } else {
            logBrandAction("Failed to update brand for ID: $id - Error: " . $conn->error);
            ErrorLogger::logError("Failed to update brand for ID: $id", ['error' => $conn->error]);
            $_SESSION['error'] = 'Failed to update brand: ' . $conn->error;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if brand has associated models or products
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM vehicle_models_enhanced WHERE brand_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $check_models = $stmt->get_result()->fetch_assoc()['count'];

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products_enhanced WHERE brand_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $check_products = $stmt->get_result()->fetch_assoc()['count'];

    if ($check_models > 0 || $check_products > 0) {
        $_SESSION['error'] = 'Cannot delete brand with associated models or products. Please reassign or delete them first.';
    } else {
        $stmt = $conn->prepare("DELETE FROM vehicle_brands_enhanced WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Brand deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete brand.';
        }
    }

    header('Location: enhanced_brand_management.php');
    exit;
}

// Get brands with analytics
$query = "
    SELECT vb.*,
           COUNT(DISTINCT vm.id) as model_count,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_brands_enhanced vb
    LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
    LEFT JOIN products_enhanced p ON vb.id = p.brand_id
    GROUP BY vb.id
    ORDER BY vb.brand_name
";

$result = $conn->query($query);

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$country_filter = $_GET['country'] ?? 'all';
$search = $_GET['search'] ?? '';

// Apply filters
$where_conditions = [];
if ($status_filter !== 'all') {
    $where_conditions[] = "vb.is_active = " . ($status_filter === 'active' ? 1 : 0);
}
if ($country_filter !== 'all') {
    $where_conditions[] = "vb.country = '" . $conn->real_escape_string($country_filter) . "'";
}
if (!empty($search)) {
    $where_conditions[] = "(vb.brand_name LIKE '%" . $conn->real_escape_string($search) . "%' OR vb.description LIKE '%" . $conn->real_escape_string($search) . "%')";
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "
    SELECT vb.*,
           COUNT(DISTINCT vm.id) as model_count,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_brands_enhanced vb
    LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
    LEFT JOIN products_enhanced p ON vb.id = p.brand_id
    $where_clause
    GROUP BY vb.id
    ORDER BY vb.brand_name
";

$result = $conn->query($query);

// Also get total counts for statistics (unfiltered)
$total_brands_query = "SELECT COUNT(*) as count FROM vehicle_brands_enhanced";
$total_models_query = "SELECT COUNT(*) as count FROM vehicle_models_enhanced";
$total_active_products_query = "SELECT COUNT(*) as count FROM products_enhanced WHERE is_active = 1";
$total_sales_query = "SELECT COALESCE(SUM(sales_count), 0) as total FROM products_enhanced WHERE is_active = 1";

// If filters are applied, show filtered stats, otherwise show total stats
if (!empty($where_conditions)) {
    $filtered_stats_query = "
        SELECT 
            COUNT(DISTINCT vb.id) as total_brands,
            COUNT(DISTINCT vm.id) as total_models,
            COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_products,
            COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
        FROM vehicle_brands_enhanced vb
        LEFT JOIN vehicle_models_enhanced vm ON vb.id = vm.brand_id
        LEFT JOIN products_enhanced p ON vb.id = p.brand_id
        $where_clause
    ";
    $stats_result = $conn->query($filtered_stats_query);
    $filtered_stats = $stats_result->fetch_assoc();
} else {
    $filtered_stats = [
        'total_brands' => $conn->query($total_brands_query)->fetch_assoc()['count'],
        'total_models' => $conn->query($total_models_query)->fetch_assoc()['count'],
        'active_products' => $conn->query($total_active_products_query)->fetch_assoc()['count'],
        'total_sales' => $conn->query($total_sales_query)->fetch_assoc()['total']
    ];
}

// Get unique countries for filter
$countries_query = $conn->query("SELECT DISTINCT country FROM vehicle_brands_enhanced WHERE country IS NOT NULL AND country != '' ORDER BY country");
$countries = [];
while ($country = $countries_query->fetch_assoc()) {
    $countries[] = $country['country'];
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Brand Management - SPARE XPRESS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/admin-style.css">
    <link rel="stylesheet" href="/css/brand-management.css?v=2">
</head>
<body>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-tags-fill text-primary me-3"></i>
                Enhanced Brand Management
            </h1>
            <p class="text-muted mb-0 fs-5">Professional brand management with analytics and advanced features</p>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Brand
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
                        <i class="bi bi-tags-fill fs-1"></i>
                    </div>
                    <h3 class="card-value text-primary mb-2" id="totalBrands"><?php echo number_format($filtered_stats['total_brands']); ?></h3>
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
                    <h3 class="card-value text-success mb-2" id="totalModels">
                        <?php echo number_format($filtered_stats['total_models']); ?>
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
                    <h3 class="card-value text-warning mb-2" id="activeProducts">
                        <?php echo number_format($filtered_stats['active_products']); ?>
                    </h3>
                    <p class="card-title mb-0">Active Products</p>
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
                        <?php echo number_format($filtered_stats['total_sales']); ?>
                    </h3>
                    <p class="card-title mb-0">Total Sales</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="bulk-actions-bar form-card mb-4" id="bulkActionsBar" style="display: none;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span id="selectedCount" class="fw-semibold text-primary">0 brands selected</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm" onclick="bulkActivate()">
                    <i class="bi bi-check-circle me-1"></i>Activate
                </button>
                <button type="button" class="btn btn-warning btn-sm" onclick="bulkDeactivate()">
                    <i class="bi bi-pause-circle me-1"></i>Deactivate
                </button>
                <button type="button" class="btn btn-danger btn-sm" onclick="bulkDelete()">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearSelection()">
                    <i class="bi bi-x me-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="form-card mb-4">
        <form method="GET" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Search Brands</label>
                    <input type="text" class="form-control filter-input" id="searchFilter" name="search" placeholder="Search by name or description..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select filter-select" id="statusFilter" name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Country</label>
                    <select class="form-select filter-select" id="countryFilter" name="country">
                        <option value="all">All Countries</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo htmlspecialchars($country); ?>" <?php echo $country_filter === $country ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($country); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportBrands()">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Brands Grid/List View -->
    <div class="row g-4" id="brands-container">
        <?php if ($result->num_rows > 0): ?>
            <?php
            $delay = 0;
            while ($brand = $result->fetch_assoc()):
                $delay += 0.1;
            ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 fade-in-up" style="animation-delay: <?php echo $delay; ?>s;">
                    <div class="brand-card enhanced-card h-100">
                        <!-- Brand Image Header -->
                        <div class="card-image-header">
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
                                     class="brand-hero-image"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="brand-hero-placeholder" style="display: none;">
                                    <i class="bi bi-building display-4 text-white opacity-75"></i>
                                    <div class="image-overlay"></div>
                                </div>
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
                                    'yamaha', 'kawasaki', 'suzuki', 'ducati', 'triumph', 'aprilia', 'ktm', 'husqvarna',
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
                                         class="brand-hero-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="brand-hero-placeholder" style="display: none;">
                                        <i class="bi bi-building display-4 text-white opacity-75"></i>
                                        <div class="image-overlay"></div>
                                    </div>
                                <?php elseif (isset($customLogoBrands[$brandSlug])): ?>
                                    <img src="<?php echo $customLogoBrands[$brandSlug]; ?>"
                                         alt="<?php echo htmlspecialchars($brand['brand_name']); ?> logo"
                                         class="brand-hero-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="brand-hero-placeholder" style="display: none;">
                                        <i class="bi bi-building display-4 text-white opacity-75"></i>
                                        <div class="image-overlay"></div>
                                    </div>
                                <?php else: ?>
                                    <!-- Text-based logo for brands without images -->
                                    <div class="brand-hero-placeholder">
                                        <i class="bi bi-building display-4 text-white opacity-75"></i>
                                        <div class="image-overlay"></div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div class="image-overlay"></div>
                        </div>

                        <!-- Floating Logo -->
                        <div class="floating-logo">
                            <?php
                            // Use the same logic for floating logo
                            $floatingLogoPath = null;
                            if (isset($uploadedLogos[$brand['brand_name']])) {
                                $floatingLogoPath = "/uploads/brands/" . $uploadedLogos[$brand['brand_name']];
                            }

                            if ($floatingLogoPath): ?>
                                <img src="<?php echo $floatingLogoPath; ?>"
                                     alt="<?php echo htmlspecialchars($brand['brand_name']); ?> Logo"
                                     class="brand-logo-floating"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="brand-logo-placeholder-floating" style="display: none;">
                                    <i class="bi bi-tag text-white fs-3"></i>
                                </div>
                            <?php elseif (in_array($brandSlug, $simpleIconBrands)): ?>
                                <img src="https://cdn.simpleicons.org/<?php echo $brandSlug; ?>/ffffff"
                                     alt="<?php echo htmlspecialchars($brand['brand_name']); ?> Logo"
                                     class="brand-logo-floating"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="brand-logo-placeholder-floating" style="display: none;">
                                    <i class="bi bi-tag text-white fs-3"></i>
                                </div>
                            <?php elseif (isset($customLogoBrands[$brandSlug])): ?>
                                <img src="<?php echo $customLogoBrands[$brandSlug]; ?>"
                                     alt="<?php echo htmlspecialchars($brand['brand_name']); ?> Logo"
                                     class="brand-logo-floating"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div class="brand-logo-placeholder-floating" style="display: none;">
                                    <i class="bi bi-tag text-white fs-3"></i>
                                </div>
                            <?php else: ?>
                                <div class="brand-logo-placeholder-floating">
                                    <i class="bi bi-tag text-white fs-3"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                            <!-- Card Controls -->
                            <div class="card-controls">
                                <div class="form-check me-2">
                                    <input class="form-check-input bulk-select" type="checkbox" value="<?php echo $brand['id']; ?>" id="brand_<?php echo $brand['id']; ?>">
                                    <label class="form-check-label visually-hidden" for="brand_<?php echo $brand['id']; ?>">
                                        Select <?php echo htmlspecialchars($brand['brand_name']); ?>
                                    </label>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle opacity-75" type="button" data-bs-toggle="dropdown" onclick="console.log('Three dots clicked for brand <?php echo $brand['id']; ?>')">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end show" style="position: absolute; right: 0; z-index: 1000;">
                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); editBrand(<?php echo $brand['id']; ?>); console.log('Edit clicked for brand <?php echo $brand['id']; ?>')">
                                            <i class="bi bi-pencil me-2"></i>Edit Brand</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); viewAnalytics(<?php echo $brand['id']; ?>); console.log('Analytics clicked for brand <?php echo $brand['id']; ?>')">
                                            <i class="bi bi-graph-up me-2"></i>View Analytics</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); deleteBrand(<?php echo $brand['id']; ?>, '<?php echo htmlspecialchars(addslashes($brand['brand_name'])); ?>'); console.log('Delete clicked for brand <?php echo $brand['id']; ?>')">
                                            <i class="bi bi-trash me-2"></i>Delete Brand</a></li>
                                    </ul>
                                </div>
                            </div>

                        <!-- Card Content -->
                        <div class="card-content">
                        <!-- Brand Header -->
                        <div class="brand-header mb-3">
                            <h4 class="brand-name mb-2"><?php echo htmlspecialchars($brand['brand_name']); ?></h4>
                            <div class="brand-meta">
                                <?php if ($brand['country']): ?>
                                    <div class="meta-item">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        <span><?php echo htmlspecialchars($brand['country']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($brand['founded_year']): ?>
                                    <div class="meta-item">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <span><?php echo $brand['founded_year']; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                            <!-- Description -->
                            <?php if ($brand['description']): ?>
                                <div class="brand-description mb-3">
                                    <p class="text-muted small mb-0">
                                        <?php echo htmlspecialchars(substr($brand['description'], 0, 100)); ?>
                                        <?php if (strlen($brand['description']) > 100): ?>...<?php endif; ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Analytics Grid -->
                            <div class="analytics-grid mb-3">
                                <div class="analytics-item">
                                    <a href="../models/enhanced_model_management.php?brand=<?php echo $brand['id']; ?>" class="text-decoration-none">
                                        <div class="analytics-value text-primary fw-bold"><?php echo $brand['model_count']; ?></div>
                                        <div class="analytics-label small text-muted">Models</div>
                                    </a>
                                </div>
                                <div class="analytics-item">
                                    <a href="../products/enhanced_product_management.php?brand=<?php echo $brand['id']; ?>" class="text-decoration-none">
                                        <div class="analytics-value text-success fw-bold"><?php echo $brand['active_product_count']; ?></div>
                                        <div class="analytics-label small text-muted">Products</div>
                                    </a>
                                </div>
                                <div class="analytics-item">
                                    <div class="analytics-value text-info fw-bold"><?php echo number_format($brand['total_sales']); ?></div>
                                    <div class="analytics-label small text-muted">Sales</div>
                                </div>
                            </div>

                            <!-- Status and Actions -->
                            <div class="card-footer-section">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="status-badge <?php echo $brand['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <i class="bi bi-circle-fill me-1"></i>
                                        <?php echo $brand['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <div class="action-buttons">
                                        <a href="../models/enhanced_model_management.php?brand=<?php echo $brand['id']; ?>"
                                           class="btn btn-primary btn-sm action-btn" title="View Models">
                                            <i class="bi bi-car-front-fill"></i>
                                        </a>
                                        <a href="../products/enhanced_product_management.php?brand=<?php echo $brand['id']; ?>"
                                           class="btn btn-success btn-sm action-btn" title="View Products">
                                            <i class="bi bi-box-seam-fill"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="empty-state text-center py-5">
                    <i class="bi bi-tags display-1 text-muted mb-3"></i>
                    <h4>No Brands Found</h4>
                    <p class="text-muted">No brands match your current filters or search criteria.</p>
                    <a href="enhanced_brand_management.php" class="btn btn-primary">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Clear Filters
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add New Brand
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_brand" value="1">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand Name *</label>
                            <input type="text" class="form-control" name="brand_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" class="form-control" name="country" placeholder="e.g., Japan, Germany">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Founded Year</label>
                            <input type="number" class="form-control" name="founded_year" min="1900" max="<?php echo date('Y'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="url" class="form-control" name="website" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="tel" class="form-control" name="contact_phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Logo Image</label>
                            <input type="file" class="form-control" name="logo_image" accept="image/*">
                            <small class="text-muted">Recommended: 200x200px, PNG/SVG</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand Image</label>
                            <input type="file" class="form-control" name="brand_image" accept="image/*">
                            <small class="text-muted">Banner image for brand page</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Brand description..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Manufacturer Details</label>
                            <textarea class="form-control" name="manufacturer_details" rows="2" placeholder="Additional manufacturer information..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Title</label>
                            <input type="text" class="form-control" name="seo_title" placeholder="SEO title for brand page">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Description</label>
                            <textarea class="form-control" name="seo_description" rows="2" placeholder="SEO description..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addBrandActive" checked>
                                <label class="form-check-label" for="addBrandActive">
                                    Brand is active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_brand" class="btn btn-primary">Add Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                <i class="bi bi-pencil me-2"></i>Edit Brand
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" enctype="multipart/form-data" id="editBrandForm" onsubmit="console.log('Form submitting'); return true;">
            <input type="hidden" name="brand_id" id="editBrandId">
            <input type="hidden" name="update_brand" value="1">
            <div class="modal-body" id="editBrandContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" onclick="submitEditBrandForm()" class="btn btn-primary">Update Brand</button>
            </div>
        </form>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/brand-management.js?v=2"></script>

<script>
console.log('Brand management script loaded');

// Test dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking dropdowns');
    
    // Test if dropdown buttons work
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Dropdown button clicked');
        });
    });
    
    // Test if dropdown items work
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            console.log('Dropdown item clicked:', e.target.textContent.trim());
        });
    });
});

// Submit edit brand form function
function submitEditBrandForm() {
    console.log('Submitting edit brand form');
    document.getElementById('editBrandForm').submit();
}

// Brand management functions
function editBrand(brandId) {
    console.log('editBrand called with ID:', brandId);
    fetch(`../api/get_brand.php?id=${brandId}`)
        .then(response => {
            console.log('API response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('API response data:', data);
            if (data.success) {
                const brand = data.brand;
                console.log('Brand data:', brand);
                document.getElementById('editBrandId').value = brand.id;

                // Populate the modal content
                const content = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand Name *</label>
                            <input type="text" class="form-control" name="brand_name" value="${brand.brand_name}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" class="form-control" name="country" value="${brand.country || ''}" placeholder="e.g., Japan, Germany">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Founded Year</label>
                            <input type="number" class="form-control" name="founded_year" value="${brand.founded_year || ''}" min="1900" max="${new Date().getFullYear()}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="url" class="form-control" name="website" value="${brand.website || ''}" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="${brand.contact_email || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="tel" class="form-control" name="contact_phone" value="${brand.contact_phone || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Logo Image</label>
                            <input type="file" class="form-control" name="logo_image" accept="image/*">
                            <input type="hidden" name="existing_logo" value="${brand.logo_image || ''}">
                            ${brand.logo_image ? `<small class="text-muted">Current: ${brand.logo_image.split('/').pop()}</small>` : ''}
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand Image</label>
                            <input type="file" class="form-control" name="brand_image" accept="image/*">
                            <input type="hidden" name="existing_brand_image" value="${brand.brand_image || ''}">
                            <small class="text-muted">Banner image for brand page</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3">${brand.description || ''}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Manufacturer Details</label>
                            <textarea class="form-control" name="manufacturer_details" rows="2">${brand.manufacturer_details || ''}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Title</label>
                            <input type="text" class="form-control" name="seo_title" value="${brand.seo_title || ''}" placeholder="SEO title for brand page">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Description</label>
                            <textarea class="form-control" name="seo_description" rows="2">${brand.seo_description || ''}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editBrandActive" ${brand.is_active ? 'checked' : ''}>
                                <label class="form-check-label" for="editBrandActive">
                                    Brand is active
                                </label>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('editBrandContent').innerHTML = content;
                console.log('Modal content set, showing modal');
                new bootstrap.Modal(document.getElementById('editBrandModal')).show();
            } else {
                console.error('API returned success=false:', data.message);
                alert('Error loading brand data: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading brand data:', error);
            alert('Error loading brand data. Please try again.');
        });
}

function viewAnalytics(brandId) {
    console.log('viewAnalytics called with ID:', brandId);
    // For now, show a simple alert. You can enhance this to show detailed analytics
    alert('Brand analytics feature coming soon! Brand ID: ' + brandId);
}

function deleteBrand(brandId, brandName) {
    console.log('deleteBrand called with ID:', brandId, 'Name:', brandName);
    if (confirm(`Are you sure you want to delete "${brandName}"? This action cannot be undone.`)) {
        console.log('User confirmed deletion');
        // Show loading state
        const btn = event.target.closest('.dropdown-item');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Deleting...';
        btn.style.pointerEvents = 'none';

        console.log('Making delete request to:', `?delete=${brandId}`);
        fetch(`?delete=${brandId}`, {
            method: 'GET'
        })
        .then(response => {
            console.log('Delete response status:', response.status);
            if (response.ok) {
                // Show success message and reload
                alert('Brand deleted successfully!');
                location.reload();
            } else {
                throw new Error('Delete failed with status: ' + response.status);
            }
        })
        .catch(error => {
            console.error('Error deleting brand:', error);
            alert('Error deleting brand. Please try again.');
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';
        });
    } else {
        console.log('User cancelled deletion');
    }
}
</script>

<?php
ob_end_flush();
include '../footer.php';
?>

</body>
</html>
