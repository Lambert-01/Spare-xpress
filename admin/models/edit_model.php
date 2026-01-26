<?php
include '../includes/auth.php';
include '../header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM vehicle_models_enhanced WHERE id = $id");
$model = $result->fetch_assoc();

// Get brands for dropdown
$brands_query = $conn->query("SELECT id, brand_name FROM vehicle_brands_enhanced WHERE is_active = 1 ORDER BY brand_name");
$brands = [];
while ($brand = $brands_query->fetch_assoc()) {
    $brands[] = $brand;
}

// Parse JSON arrays
$engine_types_selected = json_decode($model['engine_types'], true) ?: [];
$fuel_types_selected = json_decode($model['fuel_types'], true) ?: [];
$transmission_types_selected = json_decode($model['transmission_types'], true) ?: [];
$body_types_selected = json_decode($model['body_types'], true) ?: [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $technical_specs = trim($_POST['technical_specs']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $model_image = $model['model_image'];
    if (isset($_FILES['model_image']) && $_FILES['model_image']['error'] === UPLOAD_ERR_OK) {
        error_log("Uploading model image for edit");
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
            // Set error message
            echo "<script>alert('Failed to upload image.');</script>";
        }
    }

    $stmt = $conn->prepare("UPDATE vehicle_models_enhanced SET
        brand_id = ?, model_name = ?, slug = ?, model_image = ?, year_from = ?, year_to = ?,
        engine_types = ?, fuel_types = ?, transmission_types = ?, body_types = ?,
        compatibility_info = ?, technical_specs = ?, is_active = ?
        WHERE id = ?");
    $stmt->bind_param("issssssssssssi",
        $brand_id, $model_name, $slug, $model_image, $year_from, $year_to,
        $engine_types, $fuel_types, $transmission_types, $body_types,
        $compatibility_info, $technical_specs, $is_active, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Model updated!'); window.location='enhanced_model_management.php';</script>";
    } else {
        echo "<script>alert('Failed to update model: " . $conn->error . "');</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Edit Model</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Brand *</label>
                <select class="form-select" name="brand_id" required>
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand['id']; ?>" <?php echo $brand['id'] == $model['brand_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Model Name *</label>
                <input type="text" class="form-control" name="model_name" value="<?php echo htmlspecialchars($model['model_name']); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Year From</label>
                <input type="number" class="form-control" name="year_from" value="<?php echo $model['year_from'] ?: ''; ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Year To</label>
                <input type="number" class="form-control" name="year_to" value="<?php echo $model['year_to'] ?: ''; ?>" min="1900" max="<?php echo date('Y') + 5; ?>" placeholder="Leave empty if current">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Model Image</label>
                <input type="file" class="form-control" name="model_image" accept="image/*">
                <?php if ($model['model_image']): ?>
                    <small class="text-muted">Current: <?php echo basename($model['model_image']); ?></small>
                <?php endif; ?>
            </div>
            <!-- Engine Types -->
            <div class="col-12">
                <label class="form-label fw-semibold">Engine Types</label>
                <div class="row g-2">
                    <?php
                    $engine_types_options = ['petrol', 'diesel', 'electric', 'hybrid'];
                    foreach ($engine_types_options as $type):
                    ?>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="engine_types[]" value="<?php echo $type; ?>" id="engine_<?php echo $type; ?>" <?php echo in_array($type, $engine_types_selected) ? 'checked' : ''; ?>>
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
                    $fuel_types_options = ['petrol', 'diesel', 'electric', 'hybrid', 'lpg', 'cng'];
                    foreach ($fuel_types_options as $type):
                    ?>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fuel_types[]" value="<?php echo $type; ?>" id="fuel_<?php echo $type; ?>" <?php echo in_array($type, $fuel_types_selected) ? 'checked' : ''; ?>>
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
                    $transmission_types_options = ['manual', 'automatic', 'cvt', 'dct', 'amt'];
                    foreach ($transmission_types_options as $type):
                    ?>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="transmission_types[]" value="<?php echo $type; ?>" id="trans_<?php echo $type; ?>" <?php echo in_array($type, $transmission_types_selected) ? 'checked' : ''; ?>>
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
                    $body_types_options = ['sedan', 'suv', 'hatchback', 'coupe', 'convertible', 'wagon', 'pickup', 'van', 'crossover'];
                    foreach ($body_types_options as $type):
                    ?>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="body_types[]" value="<?php echo $type; ?>" id="body_<?php echo $type; ?>" <?php echo in_array($type, $body_types_selected) ? 'checked' : ''; ?>>
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
                <textarea class="form-control" name="compatibility_info" rows="3"><?php echo htmlspecialchars($model['compatibility_info'] ?: ''); ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Technical Specifications (JSON)</label>
                <textarea class="form-control" name="technical_specs" rows="4"><?php echo htmlspecialchars($model['technical_specs'] ?: ''); ?></textarea>
                <small class="text-muted">Enter technical specifications in JSON format</small>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="editModelActive" <?php echo $model['is_active'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="editModelActive">
                        Model is active
                    </label>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Update Model</button>
            <a href="enhanced_model_management.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include '../footer.php'; ?>