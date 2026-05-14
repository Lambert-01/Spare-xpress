<?php
ob_start();
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

$request_id = (int)($_GET['id'] ?? 0);
if (!$request_id) {
    header('Location: price_requests.php');
    exit;
}

// Ensure table exists
$conn->query("
    CREATE TABLE IF NOT EXISTS price_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NULL,
        product_id INT NULL,
        product_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        customer_name VARCHAR(255) NOT NULL,
        phone_number VARCHAR(32) NOT NULL,
        car_model VARCHAR(255) NOT NULL,
        status ENUM('pending','quoted','approved','deposit_paid','delivered','cancelled') NOT NULL DEFAULT 'pending',
        quoted_price DECIMAL(12,2) NULL,
        currency VARCHAR(10) NOT NULL DEFAULT 'RWF',
        notes TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL,
        INDEX idx_status (status),
        INDEX idx_created_at (created_at),
        INDEX idx_product_id (product_id),
        INDEX idx_customer_id (customer_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$valid_statuses = ['pending', 'quoted', 'approved', 'deposit_paid', 'delivered', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_price_request'])) {
    $status = (string)($_POST['status'] ?? 'pending');
    $currency = trim((string)($_POST['currency'] ?? 'RWF'));
    $quoted_price = isset($_POST['quoted_price']) && $_POST['quoted_price'] !== '' ? (float)$_POST['quoted_price'] : null;
    $notes = trim((string)($_POST['notes'] ?? ''));

    if (!in_array($status, $valid_statuses, true)) {
        $_SESSION['error'] = 'Invalid status selected';
        header("Location: view_price_request.php?id=$request_id");
        exit;
    }

    $stmt = $conn->prepare("UPDATE price_requests SET status = ?, quoted_price = ?, currency = ?, notes = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("sdssi", $status, $quoted_price, $currency, $notes, $request_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Price request updated';
    } else {
        $_SESSION['error'] = 'Failed to update: ' . ($stmt->error ?: 'Unknown error');
    }

    header("Location: view_price_request.php?id=$request_id");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM price_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$req = $stmt->get_result()->fetch_assoc();

if (!$req) {
    $_SESSION['error'] = 'Price request not found';
    header('Location: price_requests.php');
    exit;
}

$deposit = null;
if (!empty($req['quoted_price'])) {
    $deposit = ((float)$req['quoted_price']) * 0.5;
}
?>

<div class="admin-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-currency-exchange me-2 text-info"></i>Price Request PR-<?php echo (int)$req['id']; ?></h2>
            <p class="text-muted mb-0">Created: <?php echo date('M d, Y H:i', strtotime($req['created_at'])); ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="price_requests.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

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

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="form-card">
                <h5 class="mb-3"><i class="bi bi-person me-2"></i>Customer</h5>
                <div class="mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($req['customer_name']); ?></div>
                <div class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($req['phone_number']); ?></div>
                <div class="mb-2"><strong>Car Model:</strong> <?php echo htmlspecialchars($req['car_model']); ?></div>
            </div>

            <div class="form-card mt-4">
                <h5 class="mb-3"><i class="bi bi-box-seam me-2"></i>Requested Part</h5>
                <div class="mb-2"><strong>Part:</strong> <?php echo htmlspecialchars($req['product_name']); ?></div>
                <div class="mb-2"><strong>Qty:</strong> <?php echo (int)$req['quantity']; ?></div>
                <?php if (!empty($req['product_id'])): ?>
                    <div class="mb-2"><strong>Product ID:</strong> <?php echo (int)$req['product_id']; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="form-card">
                <h5 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Quote & Status</h5>
                <form method="POST">
                    <input type="hidden" name="update_price_request" value="1">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" required>
                                <?php foreach ($valid_statuses as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $req['status'] === $s ? 'selected' : ''; ?>>
                                        <?php echo ucfirst(str_replace('_', ' ', $s)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Currency</label>
                            <input type="text" class="form-control" name="currency" value="<?php echo htmlspecialchars($req['currency'] ?? 'RWF'); ?>" placeholder="RWF">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quoted Price (Optional)</label>
                            <input type="number" class="form-control" name="quoted_price" min="0" step="0.01" value="<?php echo $req['quoted_price'] !== null ? htmlspecialchars((string)$req['quoted_price']) : ''; ?>" placeholder="Enter quote">
                            <?php if ($deposit !== null): ?>
                                <div class="form-text">Suggested deposit (50%): <?php echo htmlspecialchars($req['currency'] ?? 'RWF'); ?> <?php echo number_format((float)$deposit, 0); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" name="notes" rows="5" placeholder="Internal notes / quote details..."><?php echo htmlspecialchars($req['notes'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-12 d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Save
                            </button>
                            <a class="btn btn-outline-secondary" href="price_requests.php">
                                Cancel
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

