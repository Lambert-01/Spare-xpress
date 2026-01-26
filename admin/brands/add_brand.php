<?php
include '../includes/auth.php';
include '../header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['brand_name'];
    $icon = $_POST['logo_icon'];

    $stmt = $conn->prepare("INSERT INTO vehicle_brands (brand_name, logo_icon) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $icon);
    if ($stmt->execute()) {
        echo "<script>alert('Brand added successfully!'); window.location='list_brands.php';</script>";
    } else {
        echo "<script>alert('Error adding brand!');</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Add Brand</h3>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Brand Name:</label>
            <input type="text" name="brand_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Logo Icon (SimpleIcons name, e.g. toyota):</label>
            <input type="text" name="logo_icon" class="form-control" placeholder="toyota">
        </div>
        <button type="submit" class="btn btn-primary">Save Brand</button>
        <a href="list_brands.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../footer.php'; ?>