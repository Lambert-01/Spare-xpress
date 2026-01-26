<?php
include '../includes/auth.php';
include '../header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_id = $_POST['brand_id'];
    $model_id = $_POST['model_id'];
    $category_id = $_POST['category_id'];
    $name = $_POST['product_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $year_from = $_POST['year_from'];
    $year_to = $_POST['year_to'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/product_images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = basename($_FILES["image"]["name"]);
    }

    $stmt = $conn->prepare("INSERT INTO products (brand_id, model_id, category_id, product_name, description, price, stock, year_from, year_to, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiissiiis", $brand_id, $model_id, $category_id, $name, $desc, $price, $stock, $year_from, $year_to, $image);
    if ($stmt->execute()) {
        echo "<script>alert('Product added!'); window.location='list_products.php';</script>";
    }
}
?>

<div class="container mt-4">
    <h3>Add Product</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Brand:</label>
                    <select name="brand_id" class="form-control" required onchange="loadModels()">
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
                    <label>Model:</label>
                    <select name="model_id" class="form-control" required id="model_select">
                        <option value="">Select Brand First</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Category:</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php
                        $categories = $conn->query("SELECT * FROM categories");
                        while ($cat = $categories->fetch_assoc()) {
                            echo "<option value='{$cat['id']}'>{$cat['category_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Product Name:</label>
                    <input type="text" name="product_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Description:</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label>Price (RWF):</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Stock:</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Year From:</label>
                    <input type="number" name="year_from" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Year To:</label>
                    <input type="number" name="year_to" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Product Image:</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Product</button>
        <a href="list_products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
function loadModels() {
    var brandId = document.querySelector('[name=brand_id]').value;
    var modelSelect = document.getElementById('model_select');
    modelSelect.innerHTML = '<option>Loading...</option>';

    fetch('get_models.php?brand_id=' + brandId)
        .then(response => response.json())
        .then(data => {
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            data.forEach(model => {
                modelSelect.innerHTML += `<option value="${model.id}">${model.model_name}</option>`;
            });
        });
}
</script>

<?php include '../footer.php'; ?>