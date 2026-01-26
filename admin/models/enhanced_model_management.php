<?php
// Enhanced Model Management System for SPARE XPRESS LTD
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';
// Handle form submissions
$is_ajax = isset($_POST['ajax']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents("admin/logs/model_management.log", date('Y-m-d H:i:s') . " - POST request received. is_ajax: " . ($is_ajax ? 'true' : 'false') . "\n", FILE_APPEND);
    if (isset($_POST['add_model'])) {
        // Add new model
        $brand_id = (int)$_POST['brand_id'];
        $model_name = trim($_POST['model_name']);
        $slug = generateSlug($model_name);
        $year_from = !empty($_POST['year_from']) ? (int)$_POST['year_from'] : null;
        $year_to = !empty($_POST['year_to']) ? (int)$_POST['year_to'] : null;
        // Handle arrays
        $engine_types = isset($_POST['engine_types']) ? json_encode($_POST['engine_types']) : '[]';
        $fuel_types = isset($_POST['fuel_types']) ? json_encode($_POST['fuel_types']) : '[]';
        $transmission_types = isset($_POST['transmission_types']) ? json_encode($_POST['transmission_types']) : '[]';
        $body_types = isset($_POST['body_types']) ? json_encode($_POST['body_types']) : '[]';
        $compatibility_info = trim($_POST['compatibility_info']);
        
        // Validate and format technical specs
        $technical_specs = trim($_POST['technical_specs']);
        if (!empty($technical_specs)) {
            // Validate JSON format
            $decoded = json_decode($technical_specs);
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                // Invalid JSON, set to empty JSON object
                $technical_specs = '{}';
            }
        } else {
            $technical_specs = '{}';
        }
        
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        // Handle model image upload
        $model_image = '';
        if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
            error_log("Uploading model image for add");
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/models/';
            error_log("Upload dir: " . realpath($upload_dir));
            if (!is_dir($upload_dir)) {
                error_log("Creating upload dir");
                mkdir($upload_dir, 0755, true);
            }
            $file_extension = pathinfo($_FILES['model_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_model.' . $file_extension;
            $target_path = $upload_dir . $file_name;
            error_log("Target path: " . $target_path);
            if (move_uploaded_file($_FILES['model_image']['tmp_name'], $target_path)) {
                error_log("Upload successful");
                $model_image = '/uploads/models/' . $file_name;
            } else {
                error_log("Upload failed: " . $_FILES['model_image']['error']);
                $_SESSION['error'] = 'Failed to upload model image.';
            }
        }
        $stmt = $conn->prepare("INSERT INTO vehicle_models_enhanced
            (brand_id, model_name, slug, model_image, year_from, year_to, engine_types, fuel_types,
             transmission_types, body_types, compatibility_info, technical_specs, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssssssssi",
            $brand_id, $model_name, $slug, $model_image, $year_from, $year_to,
            $engine_types, $fuel_types, $transmission_types, $body_types,
            $compatibility_info, $technical_specs, $is_active);
        if ($stmt->execute()) {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Model added successfully!']);
                exit;
            } else {
                $_SESSION['success'] = 'Model added successfully!';
                header('Location: enhanced_model_management.php');
                exit;
            }
        } else {
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to add model: ' . $conn->error]);
                exit;
            } else {
                $_SESSION['error'] = 'Failed to add model: ' . $conn->error;
            }
        }
    }
    if (isset($_POST['update_model'])) {
        file_put_contents("admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Update model request for ID: " . $_POST['model_id'] . "\n", FILE_APPEND);
        // Update existing model
        $id = (int)$_POST['model_id'];
        $brand_id = (int)$_POST['brand_id'];
        $model_name = trim($_POST['model_name']);
        $slug = generateSlug($model_name);
        $year_from = !empty($_POST['year_from']) ? (int)$_POST['year_from'] : null;
        $year_to = !empty($_POST['year_to']) ? (int)$_POST['year_to'] : null;
        // Handle arrays
        $engine_types = isset($_POST['engine_types']) ? json_encode($_POST['engine_types']) : '[]';
        $fuel_types = isset($_POST['fuel_types']) ? json_encode($_POST['fuel_types']) : '[]';
        $transmission_types = isset($_POST['transmission_types']) ? json_encode($_POST['transmission_types']) : '[]';
        $body_types = isset($_POST['body_types']) ? json_encode($_POST['body_types']) : '[]';
        $compatibility_info = trim($_POST['compatibility_info']);
        
        // Validate and format technical specs
        $technical_specs = trim($_POST['technical_specs']);
        if (!empty($technical_specs)) {
            // Validate JSON format
            $decoded = json_decode($technical_specs);
            if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
                // Invalid JSON, set to empty JSON object
                $technical_specs = '{}';
            }
        } else {
            $technical_specs = '{}';
        }
        
        $is_active = isset($_POST['is_active']) ? 1 : 0;
// Handle model image upload
$model_image = $_POST['existing_model_image'] ?? '';
if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
    error_log("Uploading model image for update");
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/models/';
    error_log("Upload dir: " . realpath($upload_dir));
    if (!is_dir($upload_dir)) {
        error_log("Creating upload dir");
        mkdir($upload_dir, 0755, true);
    }
    $file_extension = pathinfo($_FILES['model_image']['name'], PATHINFO_EXTENSION);
    $file_name = $slug . '_model.' . $file_extension;
    $target_path = $upload_dir . $file_name;
    error_log("Target path: " . $target_path);
    if (move_uploaded_file($_FILES['model_image']['tmp_name'], $target_path)) {
        error_log("Upload successful");
        $model_image = '/uploads/models/' . $file_name;
    } else {
        error_log("Upload failed: " . $_FILES['model_image']['error']);
        $_SESSION['error'] = 'Failed to upload model image.';
    }
}
        $stmt = $conn->prepare("UPDATE vehicle_models_enhanced SET
            brand_id = ?, model_name = ?, slug = ?, model_image = ?, year_from = ?, year_to = ?,
            engine_types = ?, fuel_types = ?, transmission_types = ?, body_types = ?,
            compatibility_info = ?, technical_specs = ?, is_active = ?
            WHERE id = ?");
        $stmt->bind_param("issiisssssssii",
            $brand_id, $model_name, $slug, $model_image, $year_from, $year_to,
            $engine_types, $fuel_types, $transmission_types, $body_types,
            $compatibility_info, $technical_specs, $is_active, $id);
        if ($stmt->execute()) {
            file_put_contents("admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Model updated successfully for ID: $id\n", FILE_APPEND);
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Model updated successfully!']);
                exit;
            } else {
                $_SESSION['success'] = 'Model updated successfully!';
                header('Location: enhanced_model_management.php');
                exit;
            }
        } else {
            file_put_contents("admin/logs/model_management.log", date('Y-m-d H:i:s') . " - Failed to update model for ID: $id, Error: " . $conn->error . "\n", FILE_APPEND);
            if ($is_ajax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Failed to update model: ' . $conn->error]);
                exit;
            } else {
                $_SESSION['error'] = 'Failed to update model: ' . $conn->error;
            }
        }
    }
}
// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Check if model has associated products
    $check_products = $conn->query("SELECT COUNT(*) as count FROM products_enhanced WHERE model_id = $id")->fetch_assoc()['count'];
    if ($check_products > 0) {
        $_SESSION['error'] = 'Cannot delete model with associated products. Please reassign or delete them first.';
    } else {
        if ($conn->query("DELETE FROM vehicle_models_enhanced WHERE id = $id")) {
            $_SESSION['success'] = 'Model deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete model.';
        }
    }
    header('Location: enhanced_model_management.php');
    exit;
}
// Get models with analytics
$query = "
    SELECT vm.*,
           vb.brand_name,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_models_enhanced vm
    LEFT JOIN vehicle_brands_enhanced vb ON vm.brand_id = vb.id
    LEFT JOIN products_enhanced p ON vm.id = p.model_id
    GROUP BY vm.id
    ORDER BY vb.brand_name, vm.model_name
";
$result = $conn->query($query);
// Get filter parameters
$brand_filter = $_GET['brand'] ?? 'all';
$status_filter = $_GET['status'] ?? 'all';
$year_filter = $_GET['year'] ?? 'all';
$search = $_GET['search'] ?? '';
// Apply filters
$where_conditions = [];
if ($brand_filter !== 'all') {
    $where_conditions[] = "vm.brand_id = " . (int)$brand_filter;
}
if ($status_filter !== 'all') {
    $where_conditions[] = "vm.is_active = " . ($status_filter === 'active' ? 1 : 0);
}
if ($year_filter !== 'all') {
    $current_year = date('Y');
    $where_conditions[] = "((vm.year_from <= $current_year AND vm.year_to >= $current_year) OR vm.year_to IS NULL)";
}
if (!empty($search)) {
    $where_conditions[] = "(vm.model_name LIKE '%" . $conn->real_escape_string($search) . "%' OR vb.brand_name LIKE '%" . $conn->real_escape_string($search) . "%')";
}
$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";
$query = "
    SELECT vm.*,
           vb.brand_name,
           COUNT(DISTINCT p.id) as product_count,
           COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count,
           COALESCE(SUM(CASE WHEN p.is_active = 1 THEN p.sales_count ELSE 0 END), 0) as total_sales
    FROM vehicle_models_enhanced vm
    LEFT JOIN vehicle_brands_enhanced vb ON vm.brand_id = vb.id
    LEFT JOIN products_enhanced p ON vm.id = p.model_id
    $where_clause
    GROUP BY vm.id
    ORDER BY vb.brand_name, vm.model_name
";
$result = $conn->query($query);
// Get brands for filter dropdown
$brands_query = $conn->query("SELECT id, brand_name FROM vehicle_brands_enhanced WHERE is_active = 1 ORDER BY brand_name");
$brands = [];
while ($brand = $brands_query->fetch_assoc()) {
    $brands[] = $brand;
}
// Get unique years for filter
$years_query = $conn->query("
    SELECT DISTINCT year_from as year FROM vehicle_models_enhanced WHERE year_from IS NOT NULL
    UNION
    SELECT DISTINCT year_to as year FROM vehicle_models_enhanced WHERE year_to IS NOT NULL
    ORDER BY year DESC
");
$years = [];
while ($year = $years_query->fetch_assoc()) {
    $years[] = $year['year'];
}
function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Pass brands to JavaScript
$brands_json = json_encode($brands);
function formatArrayField($json_string) {
    if (empty($json_string) || $json_string === '[]') return 'None specified';
    $array = json_decode($json_string, true);
    if (!is_array($array) || empty($array)) return 'None specified';
    return implode(', ', array_map('ucfirst', $array));
}
?>
<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-car-front-fill text-primary me-3"></i>
                Enhanced Model Management
            </h1>
            <p class="text-muted mb-0 fs-5">Professional vehicle model management with technical specifications</p>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addModelModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Model
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
                        <i class="bi bi-car-front-fill fs-1"></i>
                    </div>
                    <h3 class="card-value text-primary mb-2" id="totalModels"><?php echo $result->num_rows; ?></h3>
                    <p class="card-title mb-0">Total Models</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-tags-fill fs-1"></i>
                    </div>
                    <h3 class="card-value text-success mb-2" id="totalBrands"><?php echo count($brands); ?></h3>
                    <p class="card-title mb-0">Active Brands</p>
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
                        <?php
                        $total_products = $conn->query("SELECT COUNT(*) as count FROM products_enhanced WHERE is_active = 1")->fetch_assoc()['count'];
                        echo $total_products;
                        ?>
                    </h3>
                    <p class="card-title mb-0">Linked Products</p>
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
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select filter-select" id="statusFilter" data-filter="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Production Year</label>
                <select class="form-select filter-select" id="yearFilter" data-filter="year">
                    <option value="all">All Years</option>
                    <option value="current" <?php echo $year_filter === 'current' ? 'selected' : ''; ?>>Currently Produced</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>" <?php echo $year_filter == $year ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-success w-100" onclick="exportModels()">
                    <i class="bi bi-download me-1"></i>Export
                </button>
            </div>
        </div>
    </div>
    <!-- Models Grid/List View -->
    <div class="row g-4" id="models-container">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($model = $result->fetch_assoc()): ?>
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="model-card enhanced-card h-100">
                        <!-- Model Image Header -->
                        <div class="card-image-header">
                            <?php if ($model['model_image']): ?>
                                <img src="<?php echo htmlspecialchars($model['model_image']); ?>"
                                     alt="<?php echo htmlspecialchars($model['model_name']); ?>"
                                     class="model-hero-image">
                                <div class="image-overlay"></div>
                            <?php else: ?>
                                <div class="model-hero-placeholder">
                                    <i class="bi bi-car-front-fill display-4 text-white opacity-75"></i>
                                    <div class="image-overlay"></div>
                                </div>
                            <?php endif; ?>
                            <!-- Floating Logo/Brand Badge -->
                            <div class="floating-logo">
                                <div class="brand-badge">
                                    <span class="badge bg-primary text-white px-3 py-2">
                                        <i class="bi bi-tags-fill me-1"></i>
                                        <?php echo htmlspecialchars(substr($model['brand_name'], 0, 10)); ?>
                                    </span>
                                </div>
                            </div>
                            <!-- Card Controls -->
                            <div class="card-controls">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle opacity-75" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#" onclick="editModel(<?php echo $model['id']; ?>)">
                                            <i class="bi bi-pencil me-2"></i>Edit Model</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="viewCompatibility(<?php echo $model['id']; ?>)">
                                            <i class="bi bi-diagram-3 me-2"></i>Compatibility</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="viewAnalytics(<?php echo $model['id']; ?>)">
                                            <i class="bi bi-graph-up me-2"></i>Analytics</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteModel(<?php echo $model['id']; ?>, '<?php echo htmlspecialchars($model['model_name']); ?>')">
                                            <i class="bi bi-trash me-2"></i>Delete Model</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Card Content -->
                        <div class="card-content">
                            <!-- Model Name Box -->
                            <div class="model-name-box mb-3">
                                <div class="model-name-container">
                                    <h4 class="model-name-display mb-2"><?php echo htmlspecialchars($model['model_name']); ?></h4>
                                    <div class="brand-model-alignment">
                                        <div class="brand-name-aligned">
                                            <i class="bi bi-tags-fill me-1 text-primary"></i>
                                            <span class="brand-text"><?php echo htmlspecialchars($model['brand_name'] ?: 'No Brand'); ?></span>
                                        </div>
                                        <div class="model-years-aligned">
                                            <i class="bi bi-calendar-event me-1 text-info"></i>
                                            <span><?php echo $model['year_from'] ?: '?'; ?> - <?php echo $model['year_to'] ?: 'Present'; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Technical Specs Preview -->
                            <div class="specs-preview mb-3">
                                <?php if (!empty($model['engine_types']) && $model['engine_types'] !== '[]'): ?>
                                    <div class="spec-item">
                                        <small class="text-muted">Engine:</small>
                                        <div class="spec-value"><?php echo formatArrayField($model['engine_types']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($model['fuel_types']) && $model['fuel_types'] !== '[]'): ?>
                                    <div class="spec-item">
                                        <small class="text-muted">Fuel:</small>
                                        <div class="spec-value"><?php echo formatArrayField($model['fuel_types']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($model['transmission_types']) && $model['transmission_types'] !== '[]'): ?>
                                    <div class="spec-item">
                                        <small class="text-muted">Transmission:</small>
                                        <div class="spec-value"><?php echo formatArrayField($model['transmission_types']); ?></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($model['body_types']) && $model['body_types'] !== '[]'): ?>
                                    <div class="spec-item">
                                        <small class="text-muted">Body Type:</small>
                                        <div class="spec-value"><?php echo formatArrayField($model['body_types']); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Analytics Grid -->
                            <div class="analytics-grid mb-3">
                                <div class="analytics-item">
                                    <div class="analytics-value text-primary fw-bold"><?php echo $model['active_product_count']; ?></div>
                                    <div class="analytics-label small text-muted">Products</div>
                                </div>
                                <div class="analytics-item">
                                    <div class="analytics-value text-success fw-bold"><?php echo $model['product_count']; ?></div>
                                    <div class="analytics-label small text-muted">Total</div>
                                </div>
                                <div class="analytics-item">
                                    <div class="analytics-value text-info fw-bold"><?php echo number_format($model['total_sales']); ?></div>
                                    <div class="analytics-label small text-muted">Sales</div>
                                </div>
                            </div>
                            <!-- Status and Actions -->
                            <div class="card-footer-section">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="status-badge <?php echo $model['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <i class="bi bi-circle-fill me-1"></i>
                                        <?php echo $model['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                    <div class="action-buttons">
                                        <a href="/admin/products/enhanced_product_management.php?model=<?php echo $model['id']; ?>"
                                           class="btn btn-primary btn-sm action-btn" title="View Products">
                                            <i class="bi bi-box-seam-fill"></i>
                                        </a>
                                        <a href="view_specs.php?id=<?php echo $model['id']; ?>" class="btn btn-info btn-sm action-btn" title="View Specs">
                                            <i class="bi bi-info-circle-fill"></i>
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
                    <i class="bi bi-car-front display-1 text-muted mb-3"></i>
                    <h4>No Models Found</h4>
                    <p class="text-muted">No models match your current filters or search criteria.</p>
                    <a href="enhanced_model_management.php" class="btn btn-primary">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Clear Filters
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Add Model Modal -->
<div class="modal fade" id="addModelModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add New Model
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand *</label>
                            <select class="form-select" name="brand_id" required>
                                <option value="">Select Brand</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Model Name *</label>
                            <input type="text" class="form-control" name="model_name" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year From</label>
                            <input type="number" class="form-control" name="year_from" min="1900" max="<?php echo date('Y') + 1; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year To</label>
                            <input type="number" class="form-control" name="year_to" min="1900" max="<?php echo date('Y') + 5; ?>" placeholder="Leave empty if current">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Model Image</label>
                            <input type="file" class="form-control" name="model_image" accept="image/*">
                            <small class="text-muted">Recommended: 400x300px, JPG/PNG</small>
                        </div>
                        <!-- Engine Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Engine Types</label>
                            <div class="row g-2">
                                <?php
                                $engine_types = ['petrol', 'diesel', 'electric', 'hybrid'];
                                foreach ($engine_types as $type):
                                ?>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="engine_types[]" value="<?php echo $type; ?>" id="engine_<?php echo $type; ?>">
                                            <label class="form-check-label" for="engine_<?php echo $type; ?>">
                                                <?php echo ucfirst($type); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Fuel Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Fuel Types</label>
                            <div class="row g-2">
                                <?php
                                $fuel_types = ['petrol', 'diesel', 'electric', 'hybrid', 'lpg', 'cng'];
                                foreach ($fuel_types as $type):
                                ?>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fuel_types[]" value="<?php echo $type; ?>" id="fuel_<?php echo $type; ?>">
                                            <label class="form-check-label" for="fuel_<?php echo $type; ?>">
                                                <?php echo ucfirst($type); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Transmission Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Transmission Types</label>
                            <div class="row g-2">
                                <?php
                                $transmission_types = ['manual', 'automatic', 'cvt', 'dct', 'amt'];
                                foreach ($transmission_types as $type):
                                ?>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="transmission_types[]" value="<?php echo $type; ?>" id="trans_<?php echo $type; ?>">
                                            <label class="form-check-label" for="trans_<?php echo $type; ?>">
                                                <?php echo strtoupper($type); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <!-- Body Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Body Types</label>
                            <div class="row g-2">
                                <?php
                                $body_types = ['sedan', 'suv', 'hatchback', 'coupe', 'convertible', 'wagon', 'pickup', 'van', 'crossover'];
                                foreach ($body_types as $type):
                                ?>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="body_types[]" value="<?php echo $type; ?>" id="body_<?php echo $type; ?>">
                                            <label class="form-check-label" for="body_<?php echo $type; ?>">
                                                <?php echo ucfirst($type); ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Compatibility Information</label>
                            <textarea class="form-control" name="compatibility_info" rows="3" placeholder="Special compatibility notes, restrictions, or requirements..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Technical Specifications (JSON)</label>
                            <textarea class="form-control" name="technical_specs" rows="4" placeholder='{"engine_displacement": "2.0L", "horsepower": "150hp", "torque": "200Nm", "fuel_economy": "15km/L"}'></textarea>
                            <small class="text-muted">Enter technical specifications in JSON format</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addModelActive" checked>
                                <label class="form-check-label" for="addModelActive">
                                    Model is active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_model" class="btn btn-primary">Add Model</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Model Modal -->
<div class="modal fade" id="editModelModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Model
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editModelForm">
                <input type="hidden" name="model_id" id="editModelId">
                <div class="modal-body" id="editModelContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_model" class="btn btn-primary">Update Model</button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
.model-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}
.model-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}
.model-logo {
    width: 60px;
    height: 45px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}
.model-logo-placeholder {
    width: 60px;
    height: 45px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
}
.spec-item {
    margin-bottom: 0.5rem;
}
.spec-item .spec-value {
    font-size: 0.875rem;
    color: #495057;
    font-weight: 500;
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
.empty-state {
    padding: 3rem 1rem;
}
.empty-state i {
    opacity: 0.5;
}
.form-check {
    margin-bottom: 0.5rem;
}
</style>
// Pass brands to JavaScript
<script>
// Automatic filtering functionality for models
const availableBrands = <?php echo $brands_json; ?>;

let filterTimeout;
const currentFilters = {
    brand: '<?php echo $brand_filter; ?>',
    status: '<?php echo $status_filter; ?>',
    year: '<?php echo $year_filter; ?>'
};
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filter selects
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', function() {
            const filterType = this.dataset.filter;
            const filterValue = this.value;
            // Update current filters
            currentFilters[filterType] = filterValue;
            // Debounce the filter request
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                applyFilters();
            }, 300);
        });
    });
});
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
    fetch(`../api/get_filtered_models.php?${params}`)
        .then(response => response.json())
        .then(data => {
            hideLoadingState();
            updateModelsGrid(data.models);
            updateStatistics(data.stats);
        })
        .catch(error => {
            console.error('Error filtering models:', error);
            hideLoadingState();
            showToast('Error loading models. Please try again.', 'danger');
        });
}
function updateModelsGrid(models) {
    const container = document.getElementById('models-container');
    const emptyState = container.querySelector('.empty-state');
    if (!models || models.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="empty-state text-center py-5"><i class="bi bi-car-front display-1 text-muted mb-3"></i><h4>No Models Found</h4><p class="text-muted">No models match your current filters.</p><a href="enhanced_model_management.php" class="btn btn-primary"><i class="bi bi-arrow-counterclockwise me-2"></i>Clear Filters</a></div></div>';
        return;
    }
    // Remove empty state if it exists
    const existingEmptyState = container.querySelector('.empty-state');
    if (existingEmptyState) {
        existingEmptyState.remove();
    }
    let html = '';
    models.forEach(model => {
        html += `
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="model-card enhanced-card h-100">
                    <div class="card-image-header">
                        ${model.model_image ?
                            `<img src="${model.model_image}" alt="${model.model_name}" class="model-hero-image">
                             <div class="image-overlay"></div>` :
                            `<div class="model-hero-placeholder">
                                 <i class="bi bi-car-front-fill display-4 text-white opacity-75"></i>
                                 <div class="image-overlay"></div>
                             </div>`
                        }
                        <div class="floating-logo">
                            <div class="brand-badge">
                                <span class="badge bg-primary text-white px-3 py-2">
                                    <i class="bi bi-tags-fill me-1"></i>
                                    ${model.brand_name ? model.brand_name.substring(0, 10) : 'No Brand'}
                                </span>
                            </div>
                        </div>
                        <div class="card-controls">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle opacity-75" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="editModel(${model.id})">
                                        <i class="bi bi-pencil me-2"></i>Edit Model</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="viewCompatibility(${model.id})">
                                        <i class="bi bi-diagram-3 me-2"></i>Compatibility</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="viewAnalytics(${model.id})">
                                        <i class="bi bi-graph-up me-2"></i>Analytics</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteModel(${model.id}, '${model.model_name.replace(/'/g, "\\'")}')">
                                        <i class="bi bi-trash me-2"></i>Delete Model</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <!-- Model Name Box -->
                        <div class="model-name-box mb-3">
                            <div class="model-name-container">
                                <h4 class="model-name-display mb-2">${model.model_name}</h4>
                                <div class="brand-model-alignment">
                                    <div class="brand-name-aligned">
                                        <i class="bi bi-tags-fill me-1 text-primary"></i>
                                        <span class="brand-text">${model.brand_name || 'No Brand'}</span>
                                    </div>
                                    <div class="model-years-aligned">
                                        <i class="bi bi-calendar-event me-1 text-info"></i>
                                        <span>${model.year_from || '?'} - ${model.year_to || 'Present'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="specs-preview mb-3">
                            ${model.engine_types && model.engine_types !== '[]' ? `
                                <div class="spec-item">
                                    <small class="text-muted">Engine:</small>
                                    <div class="spec-value">${formatArrayField(model.engine_types)}</div>
                                </div>
                            ` : ''}
                            ${model.fuel_types && model.fuel_types !== '[]' ? `
                                <div class="spec-item">
                                    <small class="text-muted">Fuel:</small>
                                    <div class="spec-value">${formatArrayField(model.fuel_types)}</div>
                                </div>
                            ` : ''}
                            ${model.transmission_types && model.transmission_types !== '[]' ? `
                                <div class="spec-item">
                                    <small class="text-muted">Transmission:</small>
                                    <div class="spec-value">${formatArrayField(model.transmission_types)}</div>
                                </div>
                            ` : ''}
                            ${model.body_types && model.body_types !== '[]' ? `
                                <div class="spec-item">
                                    <small class="text-muted">Body Type:</small>
                                    <div class="spec-value">${formatArrayField(model.body_types)}</div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="analytics-grid mb-3">
                            <div class="analytics-item">
                                <div class="analytics-value text-primary fw-bold">${model.active_product_count}</div>
                                <div class="analytics-label small text-muted">Products</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-success fw-bold">${model.product_count}</div>
                                <div class="analytics-label small text-muted">Total</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-info fw-bold">${Number(model.total_sales).toLocaleString()}</div>
                                <div class="analytics-label small text-muted">Sales</div>
                            </div>
                        </div>
                        <div class="card-footer-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-badge ${model.is_active ? 'status-active' : 'status-inactive'}">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    ${model.is_active ? 'Active' : 'Inactive'}
                                </span>
                                <div class="action-buttons">
                                    <a href="/admin/products/enhanced_product_management.php?model=${model.id}"
                                       class="btn btn-primary btn-sm action-btn" title="View Products">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </a>
                                    <a href="view_specs.php?id=${model.id}" class="btn btn-info btn-sm action-btn" title="View Specs">
                                        <i class="bi bi-info-circle-fill"></i>
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
        // Update Total Models card
        const totalModelsElement = document.getElementById('totalModels');
        if (totalModelsElement) {
            totalModelsElement.textContent = Number(stats.total_models || 0).toLocaleString();
        }
        // Update Active Brands card
        const totalBrandsElement = document.getElementById('totalBrands');
        if (totalBrandsElement) {
            totalBrandsElement.textContent = Number(stats.total_brands || 0).toLocaleString();
        }
        // Update Linked Products card
        const activeProductsElement = document.getElementById('activeProducts');
        if (activeProductsElement) {
            activeProductsElement.textContent = Number(stats.active_products || 0).toLocaleString();
        }
        // Update Total Sales card
        const totalSalesElement = document.getElementById('totalSales');
        if (totalSalesElement) {
            totalSalesElement.textContent = Number(stats.total_sales || 0).toLocaleString();
        }
    }
}
function formatArrayField(json_string) {
    if (!json_string || json_string === '[]') return 'None specified';
    try {
        const array = JSON.parse(json_string);
        if (!Array.isArray(array) || array.length === 0) return 'None specified';
        return array.map(item => item.charAt(0).toUpperCase() + item.slice(1)).join(', ');
    } catch (e) {
        return 'None specified';
    }
}
function showLoadingState() {
    // Add loading overlay to models container
    const container = document.getElementById('models-container');
    if (container) {
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
        // Add loading spinner
        if (!container.querySelector('.loading-overlay')) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay d-flex justify-content-center align-items-center';
            loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            container.appendChild(loadingOverlay);
        }
    }
}
function hideLoadingState() {
    const container = document.getElementById('models-container');
    if (container) {
        container.style.opacity = '1';
        container.style.pointerEvents = 'auto';
        // Remove loading overlay
        const loadingOverlay = container.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }
}
// Edit model function
function editModel(modelId) {
    fetch(`../../api/get_model.php?id=${modelId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const model = data.model;
                document.getElementById('editModelId').value = model.id;
                // Parse JSON arrays
                const engineTypes = model.engine_types ? JSON.parse(model.engine_types) : [];
                const fuelTypes = model.fuel_types ? JSON.parse(model.fuel_types) : [];
                const transmissionTypes = model.transmission_types ? JSON.parse(model.transmission_types) : [];
                const bodyTypes = model.body_types ? JSON.parse(model.body_types) : [];
                const content = `
                    <input type="hidden" name="ajax" value="1">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand *</label>
                            <select class="form-select" name="brand_id" required>
                                <option value="">Select Brand</option>
                                ${availableBrands.map(brand => `
                                    <option value="${brand.id}" ${model.brand_id == brand.id ? 'selected' : ''}>
                                        ${brand.brand_name.replace(/"/g, '"')}
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Model Name *</label>
                            <input type="text" class="form-control" name="model_name" value="${model.model_name}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year From</label>
                            <input type="number" class="form-control" name="year_from" value="${model.year_from || ''}" min="1900" max="<?php echo date('Y') + 1; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Year To</label>
                            <input type="number" class="form-control" name="year_to" value="${model.year_to || ''}" min="1900" max="<?php echo date('Y') + 5; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Model Image</label>
                            <input type="file" class="form-control" name="model_image" accept="image/*">
                            <input type="hidden" name="existing_model_image" value="${model.model_image || ''}">
                            ${model.model_image ? `<small class="text-muted">Current: ${model.model_image.split('/').pop()}</small>` : ''}
                        </div>
                        <!-- Engine Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Engine Types</label>
                            <div class="row g-2">
                                ${['petrol', 'diesel', 'electric', 'hybrid'].map(type => `
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="engine_types[]" value="${type}" id="edit_engine_${type}" ${engineTypes.includes(type) ? 'checked' : ''}>
                                            <label class="form-check-label" for="edit_engine_${type}">
                                                ${type.charAt(0).toUpperCase() + type.slice(1)}
                                            </label>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <!-- Fuel Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Fuel Types</label>
                            <div class="row g-2">
                                ${['petrol', 'diesel', 'electric', 'hybrid', 'lpg', 'cng'].map(type => `
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="fuel_types[]" value="${type}" id="edit_fuel_${type}" ${fuelTypes.includes(type) ? 'checked' : ''}>
                                            <label class="form-check-label" for="edit_fuel_${type}">
                                                ${type.charAt(0).toUpperCase() + type.slice(1)}
                                            </label>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <!-- Transmission Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Transmission Types</label>
                            <div class="row g-2">
                                ${['manual', 'automatic', 'cvt', 'dct', 'amt'].map(type => `
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="transmission_types[]" value="${type}" id="edit_trans_${type}" ${transmissionTypes.includes(type) ? 'checked' : ''}>
                                            <label class="form-check-label" for="edit_trans_${type}">
                                                ${type.toUpperCase()}
                                            </label>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <!-- Body Types -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Body Types</label>
                            <div class="row g-2">
                                ${['sedan', 'suv', 'hatchback', 'coupe', 'convertible', 'wagon', 'pickup', 'van', 'crossover'].map(type => `
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="body_types[]" value="${type}" id="edit_body_${type}" ${bodyTypes.includes(type) ? 'checked' : ''}>
                                            <label class="form-check-label" for="edit_body_${type}">
                                                ${type.charAt(0).toUpperCase() + type.slice(1)}
                                            </label>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Compatibility Information</label>
                            <textarea class="form-control" name="compatibility_info" rows="3">${model.compatibility_info || ''}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Technical Specifications (JSON)</label>
                            <textarea class="form-control" name="technical_specs" rows="4">${model.technical_specs || ''}</textarea>
                            <small class="text-muted">Enter technical specifications in JSON format</small>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editModelActive" ${model.is_active ? 'checked' : ''}>
                                <label class="form-check-label" for="editModelActive">
                                    Model is active
                                </label>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('editModelContent').innerHTML = content;
                const modal = new bootstrap.Modal(document.getElementById('editModelModal'));
                modal.show();

                // Handle form submission via AJAX
                const form = document.getElementById('editModelForm');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(form);

                    fetch('../../api/update_model.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            modal.hide();
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to update model. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating model:', error);
                        alert('Error updating model. Please try again.');
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error loading model data:', error);
            alert('Error loading model data. Please try again.');
        });
}
// Delete model function
function deleteModel(modelId, modelName) {
    if (confirm(`Are you sure you want to delete "${modelName}"? This action cannot be undone.`)) {
        // Show loading
        const btn = event.target.closest('.dropdown-item');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Deleting...';
        btn.style.pointerEvents = 'none';
        fetch(`?delete=${modelId}`, {
            method: 'GET'
        })
        .then(response => {
            if (response.ok) {
                showToast('Model deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error('Delete failed');
            }
        })
        .catch(error => {
            console.error('Error deleting model:', error);
            showToast('Error deleting model. Please try again.', 'danger');
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';
        });
    }
}
// View specs function
function viewSpecs(modelId) {
    showToast('Technical specifications view coming soon!', 'info');
}
// View compatibility function
function viewCompatibility(modelId) {
    showToast('Compatibility matrix coming soon!', 'info');
}
// View analytics function
function viewAnalytics(modelId) {
    showToast('Model analytics coming soon!', 'info');
}
// Export models function
function exportModels() {
    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = '../api/export_models.php';
    link.download = 'models_export_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Export started! File will download automatically.', 'success');
}
</script>
<?php include '../footer.php'; ?>
