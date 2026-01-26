<?php
include '../includes/auth.php';
include '../header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['category_name'];

    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $name);
    if ($stmt->execute()) {
        echo "<script>alert('Category added!'); window.location='list_categories.php';</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Add Category</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Category Name:</label>
            <input type="text" name="category_name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Category</button>
        <a href="list_categories.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../footer.php'; ?>