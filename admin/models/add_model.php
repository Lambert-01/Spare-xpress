<?php
include '../includes/auth.php';
include '../header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_id = $_POST['brand_id'];
    $model_name = $_POST['model_name'];

    $stmt = $conn->prepare("INSERT INTO vehicle_models (brand_id, model_name) VALUES (?, ?)");
    $stmt->bind_param("is", $brand_id, $model_name);
    if ($stmt->execute()) {
        echo "<script>alert('Model added!'); window.location='list_models.php';</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Add Model</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Brand:</label>
            <select name="brand_id" class="form-control" required>
                <option value="">Select Brand</option>
                <?php
                $brands = $conn->query("SELECT * FROM vehicle_brands");
                while ($brand = $brands->fetch_assoc()) {
                    echo "<option value='{$brand['id']}'>{$brand['brand_name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Model Name:</label>
            <input type="text" name="model_name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Model</button>
        <a href="list_models.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../footer.php'; ?>