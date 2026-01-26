<?php
include '../includes/auth.php';
include '../header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM vehicle_brands WHERE id = $id");
$brand = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['brand_name'];
    $icon = $_POST['logo_icon'];

    $stmt = $conn->prepare("UPDATE vehicle_brands SET brand_name = ?, logo_icon = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $icon, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Brand updated!'); window.location='list_brands.php';</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Edit Brand</h3>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label>Brand Name:</label>
            <input type="text" name="brand_name" class="form-control" value="<?php echo $brand['brand_name']; ?>" required>
        </div>
        <div class="mb-3">
            <label>Logo Icon:</label>
            <input type="text" name="logo_icon" class="form-control" value="<?php echo $brand['logo_icon']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Brand</button>
        <a href="list_brands.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../footer.php'; ?>