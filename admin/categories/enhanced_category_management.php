<?php
ob_start();
// Enhanced Category Management System for SPARE XPRESS LTD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents('../logs/category_management.log', date('Y-m-d H:i:s') . " - POST received\n", FILE_APPEND);
}

include '../includes/auth.php';
include '../includes/functions.php';
include '../logs/error_log.php';
include '../header.php';

// Custom logging for category management
function logCategoryAction($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "$timestamp - $message" . PHP_EOL;
    file_put_contents('../logs/category_management.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        // Add new category
        $category_name = trim($_POST['category_name']);
        $slug = generateSlug($category_name);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = trim($_POST['description']);
        $icon_class = trim($_POST['icon_class']) ?: 'bi-grid';
        $display_priority = $_POST['display_priority'] ?? 'medium';
        $seo_title = trim($_POST['seo_title']);
        $seo_description = trim($_POST['seo_description']);
        $meta_keywords = trim($_POST['meta_keywords']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle category image upload
        $category_image = '';
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/categories/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_extension = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_category.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_path)) {
                $category_image = 'uploads/categories/' . $file_name;
            }
        }

        $stmt = $conn->prepare("INSERT INTO categories_enhanced
            (category_name, slug, parent_id, category_image, icon_class, description,
             display_priority, seo_title, seo_description, meta_keywords, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssisssssssi",
            $category_name, $slug, $parent_id, $category_image, $icon_class, $description,
            $display_priority, $seo_title, $seo_description, $meta_keywords, $is_active);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Category added successfully!';
            header('Location: enhanced_category_management.php');
            exit;
        } else {
            $_SESSION['error'] = 'Failed to add category: ' . $conn->error;
        }
    }

    if (isset($_POST['update_category'])) {
        // Update existing category
        logCategoryAction("Update category form submitted");
        ErrorLogger::logSuccess("Update category API called", ['post_data' => $_POST]);

        $id = (int)$_POST['category_id'];
        $category_name = trim($_POST['category_name']);
        $slug = generateSlug($category_name);
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        $description = trim($_POST['description']);
        $icon_class = trim($_POST['icon_class']) ?: 'bi-grid';
        $display_priority = $_POST['display_priority'] ?? 'medium';
        $seo_title = trim($_POST['seo_title']);
        $seo_description = trim($_POST['seo_description']);
        $meta_keywords = trim($_POST['meta_keywords']);
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        logCategoryAction("Category data prepared for ID: $id, Name: $category_name");
        ErrorLogger::logSuccess("Category data prepared", [
            'id' => $id,
            'category_name' => $category_name,
            'slug' => $slug,
            'parent_id' => $parent_id,
            'is_active' => $is_active
        ]);

        // Handle category image upload
        $category_image = $_POST['existing_category_image'] ?? '';
        ErrorLogger::logSuccess("Existing image: $category_image");

        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            ErrorLogger::logSuccess("New image upload detected", ['file' => $_FILES['category_image']['name']]);

            $upload_dir = '../uploads/categories/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_extension = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
            $file_name = $slug . '_category.' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['category_image']['tmp_name'], $target_path)) {
                $category_image = 'uploads/categories/' . $file_name;
                ErrorLogger::logSuccess("Image uploaded successfully: $category_image");
            } else {
                ErrorLogger::logError("Failed to upload image to $target_path");
            }
        }

        $update_query = "UPDATE categories_enhanced SET
            category_name = ?, slug = ?, parent_id = ?, category_image = ?, icon_class = ?,
            description = ?, display_priority = ?, seo_title = ?, seo_description = ?,
            meta_keywords = ?, is_active = ?
            WHERE id = ?";

        ErrorLogger::logSuccess("Update query prepared", ['query' => $update_query]);

        $stmt = $conn->prepare($update_query);

        $stmt->bind_param("ssissssssssi",
            $category_name, $slug, $parent_id, $category_image, $icon_class, $description,
            $display_priority, $seo_title, $seo_description, $meta_keywords, $is_active, $id);

        ErrorLogger::logQuery($update_query, "ssissssssssi", [
            $category_name, $slug, $parent_id, $category_image, $icon_class, $description,
            $display_priority, $seo_title, $seo_description, $meta_keywords, $is_active, $id
        ]);

        if ($stmt->execute()) {
            logCategoryAction("Category updated successfully for ID: $id");
            ErrorLogger::logSuccess("Category updated successfully for ID: $id");
            $_SESSION['success'] = 'Category updated successfully!';
            header('Location: enhanced_category_management.php');
            exit;
        } else {
            logCategoryAction("Failed to update category for ID: $id - Error: " . $conn->error);
            ErrorLogger::logError("Failed to update category for ID: $id", ['error' => $conn->error]);
            $_SESSION['error'] = 'Failed to update category: ' . $conn->error;
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if category has subcategories or products
    $check_subcategories = $conn->query("SELECT COUNT(*) as count FROM categories_enhanced WHERE parent_id = $id")->fetch_assoc()['count'];
    $check_products = $conn->query("SELECT COUNT(*) as count FROM products_enhanced WHERE category_id = $id")->fetch_assoc()['count'];

    if ($check_subcategories > 0 || $check_products > 0) {
        $_SESSION['error'] = 'Cannot delete category with subcategories or associated products. Please reassign them first.';
    } else {
        if ($conn->query("DELETE FROM categories_enhanced WHERE id = $id")) {
            $_SESSION['success'] = 'Category deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete category.';
        }
    }

    header('Location: enhanced_category_management.php');
    exit;
}

// Get categories with hierarchy and analytics
function buildCategoryTree($parent_id = null, $level = 0) {
    global $conn;
    $categories = [];

    $query = "SELECT c.*,
                     COUNT(DISTINCT p.id) as product_count,
                     COUNT(DISTINCT CASE WHEN p.is_active = 1 THEN p.id END) as active_product_count
              FROM categories_enhanced c
              LEFT JOIN products_enhanced p ON c.id = p.category_id
              WHERE c.parent_id " . ($parent_id === null ? "IS NULL" : "= $parent_id") . "
              GROUP BY c.id
              ORDER BY c.display_order, c.category_name";

    $result = $conn->query($query);
    while ($category = $result->fetch_assoc()) {
        $category['level'] = $level;
        $category['children'] = buildCategoryTree($category['id'], $level + 1);
        $categories[] = $category;
    }

    return $categories;
}

$categories = buildCategoryTree();

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Apply filters
$filtered_categories = [];
foreach ($categories as $category) {
    $include = true;

    if ($status_filter !== 'all') {
        $include = $include && ($category['is_active'] == ($status_filter === 'active' ? 1 : 0));
    }

    if (!empty($search)) {
        $include = $include && (
            stripos($category['category_name'], $search) !== false ||
            stripos($category['description'], $search) !== false
        );
    }

    if ($include) {
        $filtered_categories[] = $category;
    }
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function renderCategoryTree($categories, $parent_id = null) {
    $html = '';
    foreach ($categories as $category) {
        if ($category['parent_id'] == $parent_id) {
            $indent = str_repeat('— ', $category['level']);
            $has_children = !empty($category['children']);

            $html .= "<tr>";
            $html .= "<td>";
            $html .= "<div class='d-flex align-items-center'>";
            if ($category['category_image']) {
                $html .= "<img src='../{$category['category_image']}' alt='{$category['category_name']}' class='category-thumb me-3 rounded'>";
            } else {
                $html .= "<div class='category-thumb-placeholder me-3 rounded d-flex align-items-center justify-content-center'><i class='{$category['icon_class']} text-muted'></i></div>";
            }
            $html .= "<div>";
            $html .= "<strong>{$indent}{$category['category_name']}</strong>";
            if ($category['description']) {
                $html .= "<br><small class='text-muted'>" . htmlspecialchars(substr($category['description'], 0, 50)) . "...</small>";
            }
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</td>";
            $html .= "<td><span class='badge bg-" . ($category['display_priority'] === 'high' ? 'danger' : ($category['display_priority'] === 'medium' ? 'warning' : 'secondary')) . "'>{$category['display_priority']}</span></td>";
            $html .= "<td><span class='badge bg-info'>{$category['active_product_count']}</span></td>";
            $html .= "<td><span class='badge bg-" . ($category['is_active'] ? 'success' : 'secondary') . "'>" . ($category['is_active'] ? 'Active' : 'Inactive') . "</span></td>";
            $html .= "<td>";
            $html .= "<div class='btn-group btn-group-sm'>";
            $html .= "<button class='btn btn-outline-primary btn-sm' onclick='editCategory({$category['id']})' title='Edit'><i class='bi bi-pencil'></i></button>";
            if ($has_children) {
                $html .= "<button class='btn btn-outline-info btn-sm' onclick='toggleChildren(this)' title='Toggle Children'><i class='bi bi-chevron-down'></i></button>";
            }
            $html .= "<button class='btn btn-outline-danger btn-sm' onclick='deleteCategory({$category['id']}, \"{$category['category_name']}\")' title='Delete'><i class='bi bi-trash'></i></button>";
            $html .= "</div>";
            $html .= "</td>";
            $html .= "</tr>";

            // Render children
            if ($has_children) {
                $html .= "<tr class='child-row' style='display: none;'>";
                $html .= "<td colspan='5' class='ps-4'>";
                $html .= renderCategoryTree($category['children'], $category['id']);
                $html .= "</td>";
                $html .= "</tr>";
            }
        }
    }
    return $html;
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-diagram-3-fill text-primary me-3"></i>
                Enhanced Category Management
            </h1>
            <p class="text-muted mb-0 fs-5">Professional tree-based category system with advanced features</p>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-circle-fill me-2"></i>Add New Category
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
                        <i class="bi bi-diagram-3 fs-1"></i>
                    </div>
                    <h3 class="card-value text-primary mb-2"><?php echo count($categories); ?></h3>
                    <p class="card-title mb-0">Total Categories</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-tree fs-1"></i>
                    </div>
                    <h3 class="card-value text-success mb-2">
                        <?php
                        $total_children = 0;
                        foreach ($categories as $cat) {
                            $total_children += count($cat['children']);
                        }
                        echo $total_children;
                        ?>
                    </h3>
                    <p class="card-title mb-0">Subcategories</p>
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
                        $total_products = $conn->query("SELECT COUNT(*) as count FROM products_enhanced WHERE is_active = 1")->fetch_assoc()['count'];
                        echo $total_products;
                        ?>
                    </h3>
                    <p class="card-title mb-0">Categorized Products</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="stats-card">
                <div class="card-body text-center p-4">
                    <div class="card-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                        <i class="bi bi-bar-chart fs-1"></i>
                    </div>
                    <h3 class="card-value text-info mb-2">
                        <?php
                        $active_categories = $conn->query("SELECT COUNT(*) as count FROM categories_enhanced WHERE is_active = 1")->fetch_assoc()['count'];
                        echo $active_categories;
                        ?>
                    </h3>
                    <p class="card-title mb-0">Active Categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="form-card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Search Categories</label>
                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Category name or description...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-5">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Search
                    </button>
                    <a href="enhanced_category_management.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                    <button type="button" class="btn btn-success" onclick="exportCategories()">
                        <i class="bi bi-download me-1"></i>Export
                    </button>
                    <button type="button" class="btn btn-warning" onclick="reorderCategories()">
                        <i class="bi bi-arrow-up-down me-1"></i>Reorder
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Categories Tree Table -->
    <div class="form-card">
        <div class="table-responsive">
            <table class="table table-hover" id="categoriesTable">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-tag me-1"></i>Category</th>
                        <th><i class="bi bi-star me-1"></i>Priority</th>
                        <th><i class="bi bi-box-seam me-1"></i>Products</th>
                        <th><i class="bi bi-toggle-on me-1"></i>Status</th>
                        <th><i class="bi bi-gear me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo renderCategoryTree($filtered_categories); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Add New Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category Name *</label>
                            <input type="text" class="form-control" name="category_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Parent Category</label>
                            <select class="form-select" name="parent_id">
                                <option value="">Root Category</option>
                                <?php
                                function renderCategoryOptions($categories, $level = 0) {
                                    foreach ($categories as $category) {
                                        $indent = str_repeat('— ', $level);
                                        echo "<option value='{$category['id']}'>{$indent}{$category['category_name']}</option>";
                                        if (!empty($category['children'])) {
                                            renderCategoryOptions($category['children'], $level + 1);
                                        }
                                    }
                                }
                                renderCategoryOptions($categories);
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Icon Class</label>
                            <input type="text" class="form-control" name="icon_class" value="bi-grid" placeholder="bi-grid, bi-tools, etc.">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Display Priority</label>
                            <select class="form-select" name="display_priority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Category Image</label>
                            <input type="file" class="form-control" name="category_image" accept="image/*">
                            <small class="text-muted">Recommended: 300x200px, JPG/PNG</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Category description..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Title</label>
                            <input type="text" class="form-control" name="seo_title" placeholder="SEO title for category page">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Description</label>
                            <textarea class="form-control" name="seo_description" rows="2" placeholder="SEO description..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="addCategoryActive" checked>
                                <label class="form-check-label" for="addCategoryActive">
                                    Category is active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>Edit Category
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editCategoryForm" onsubmit="console.log('Form submitting'); return true;">
                <input type="hidden" name="category_id" id="editCategoryId">
                <input type="hidden" name="update_category" value="1">
                <div class="modal-body" id="editCategoryContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" onclick="submitEditForm()" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.category-thumb {
    width: 50px;
    height: 40px;
    object-fit: cover;
    border: 2px solid #e9ecef;
}

.category-thumb-placeholder {
    width: 50px;
    height: 40px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
}

.child-row {
    background: #f8f9fa;
}

.child-row td {
    border-top: none !important;
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
// Submit edit form function
function submitEditForm() {
    console.log('Submitting edit form');
    document.getElementById('editCategoryForm').submit();
}

// Edit category function
function editCategory(categoryId) {
    console.log('Edit category clicked for ID:', categoryId);
    fetch(`../api/get_category.php?id=${categoryId}`)
        .then(response => response.json())
        .then(data => {
            console.log('API response:', data);
            if (data.success) {
                const category = data.category;
                console.log('Category data:', category);
                document.getElementById('editCategoryId').value = category.id;

                const content = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category Name *</label>
                            <input type="text" class="form-control" name="category_name" value="${category.category_name}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Parent Category</label>
                            <select class="form-select" name="parent_id">
                                <option value="">Root Category</option>
                                <?php
                                renderCategoryOptions($categories);
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Icon Class</label>
                            <input type="text" class="form-control" name="icon_class" value="${category.icon_class || 'bi-grid'}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Display Priority</label>
                            <select class="form-select" name="display_priority">
                                <option value="low" ${category.display_priority === 'low' ? 'selected' : ''}>Low</option>
                                <option value="medium" ${category.display_priority === 'medium' ? 'selected' : ''}>Medium</option>
                                <option value="high" ${category.display_priority === 'high' ? 'selected' : ''}>High</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Category Image</label>
                            <input type="file" class="form-control" name="category_image" accept="image/*">
                            <input type="hidden" name="existing_category_image" value="${category.category_image || ''}">
                            ${category.category_image ? `<small class="text-muted">Current: ${category.category_image.split('/').pop()}</small>` : ''}
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3">${category.description || ''}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Title</label>
                            <input type="text" class="form-control" name="seo_title" value="${category.seo_title || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Description</label>
                            <textarea class="form-control" name="seo_description" rows="2">${category.seo_description || ''}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" value="${category.meta_keywords || ''}">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editCategoryActive" ${category.is_active ? 'checked' : ''}>
                                <label class="form-check-label" for="editCategoryActive">
                                    Category is active
                                </label>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('editCategoryContent').innerHTML = content;
                // Set selected values
                document.querySelector('#editCategoryModal select[name="parent_id"]').value = category.parent_id || '';
                new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
            }
        })
        .catch(error => {
            console.error('Error loading category data:', error);
            alert('Error loading category data. Please try again.');
        });
}

// Delete category function
function deleteCategory(categoryId, categoryName) {
    if (confirm(`Are you sure you want to delete "${categoryName}"? This action cannot be undone.`)) {
        window.location.href = `?delete=${categoryId}`;
    }
}

// Toggle children visibility
function toggleChildren(button) {
    const row = button.closest('tr');
    const childRow = row.nextElementSibling;

    if (childRow && childRow.classList.contains('child-row')) {
        const isVisible = childRow.style.display !== 'none';
        childRow.style.display = isVisible ? 'none' : 'table-row';

        const icon = button.querySelector('i');
        icon.className = isVisible ? 'bi bi-chevron-right' : 'bi bi-chevron-down';
    }
}

// Export categories function
function exportCategories() {
    // TODO: Implement export functionality
    alert('Export feature coming soon!');
}

// Reorder categories function
function reorderCategories() {
    // TODO: Implement drag-and-drop reordering
    alert('Reorder feature coming soon!');
}
</script>

<?php
ob_end_flush();
include '../footer.php';
?>