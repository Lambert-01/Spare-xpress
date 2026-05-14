<?php
ob_start();
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Ensure table exists (in case admin opens before first request is submitted)
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

$search = trim((string)($_GET['search'] ?? ''));
$status = (string)($_GET['status'] ?? 'all');
$valid_statuses = ['all', 'pending', 'quoted', 'approved', 'deposit_paid', 'delivered', 'cancelled'];
if (!in_array($status, $valid_statuses, true)) $status = 'all';

$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $like = '%' . $search . '%';
    $where[] = "(product_name LIKE ? OR customer_name LIKE ? OR phone_number LIKE ? OR car_model LIKE ?)";
    $types .= 'ssss';
    array_push($params, $like, $like, $like, $like);

    if (ctype_digit($search)) {
        $where[] = "id = ?";
        $types .= 'i';
        $params[] = (int)$search;
    }
}

if ($status !== 'all') {
    $where[] = "status = ?";
    $types .= 's';
    $params[] = $status;
}

$where_clause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
$sql = "SELECT * FROM price_requests $where_clause ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($stmt && $params) {
    $stmt->bind_param($types, ...$params);
}
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT 1 AS _empty WHERE 1=0");
    $_SESSION['error'] = 'Failed to load price requests: ' . ($conn->error ?: 'Unknown error');
}

function prStatusBadge($s) {
    $map = [
        'pending' => 'warning',
        'quoted' => 'primary',
        'approved' => 'success',
        'deposit_paid' => 'info',
        'delivered' => 'success',
        'cancelled' => 'danger',
    ];
    $color = $map[$s] ?? 'secondary';
    $label = ucfirst(str_replace('_', ' ', $s));
    return '<span class="badge bg-' . $color . '">' . $label . '</span>';
}
?>

<div class="admin-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="bi bi-currency-exchange me-2 text-info"></i>Price Requests</h2>
            <p class="text-muted mb-0">Special-order requests from the shop (Dubai / price-on-request items)</p>
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

    <div class="form-card mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Search</label>
                <input type="text" class="form-control" name="search" placeholder="PR ID, product, customer, phone..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" name="status">
                    <?php foreach ($valid_statuses as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $status === $s ? 'selected' : ''; ?>>
                            <?php echo $s === 'all' ? 'All Status' : ucfirst(str_replace('_', ' ', $s)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="price_requests.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="form-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>PR ID</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Car Model</th>
                        <th>Status</th>
                        <th>Quoted</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-semibold">PR-<?php echo (int)$row['id']; ?></td>
                        <td>
                            <div class="fw-semibold"><?php echo htmlspecialchars($row['product_name']); ?></div>
                            <?php if (!empty($row['product_id'])): ?>
                                <small class="text-muted">Product ID: <?php echo (int)$row['product_id']; ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo (int)$row['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['car_model']); ?></td>
                        <td><?php echo prStatusBadge((string)$row['status']); ?></td>
                        <td>
                            <?php if (!empty($row['quoted_price'])): ?>
                                <span class="fw-bold text-success"><?php echo htmlspecialchars($row['currency'] ?? 'RWF'); ?> <?php echo number_format((float)$row['quoted_price'], 0); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></small>
                        </td>
                        <td>
                            <a class="btn btn-outline-primary btn-sm" href="view_price_request.php?id=<?php echo (int)$row['id']; ?>">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>

