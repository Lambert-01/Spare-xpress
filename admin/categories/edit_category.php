<?php
include '../includes/auth.php';
include '../header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM categories WHERE id = $id");
$category = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['category_name'];

    $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    if ($stmt->execute()) {
        echo "<script>alert('Category updated!'); window.location='list_categories.php';</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Edit Category</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Category Name:</label>
            <input type="text" name="category_name" class="form-control" value="<?php echo $category['category_name']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Category</button>
        <a href="list_categories.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../footer.php'; ?>