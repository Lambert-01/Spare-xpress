<?php
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Get request ID
$request_id = (int)($_GET['id'] ?? 0);
if (!$request_id) {
    header('Location: order_demand_list.php');
    exit;
}

// Fetch request details
$request_query = "SELECT * FROM order_requests WHERE id = ?";
$stmt = $conn->prepare($request_query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    $_SESSION['error'] = 'Request not found';
    header('Location: order_demand_list.php');
    exit;
}

// Fetch request timeline
$timeline_query = "SELECT * FROM request_timeline WHERE request_id = ? ORDER BY created_at DESC";
$timeline_stmt = $conn->prepare($timeline_query);
$timeline_stmt->bind_param("i", $request_id);
$timeline_stmt->execute();
$timeline = $timeline_stmt->get_result();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['request_status'];
    $quoted_price = isset($_POST['quoted_price']) ? (float)$_POST['quoted_price'] : null;
    $status_notes = trim($_POST['status_notes'] ?? '');

    $update_fields = ["status = ?"];
    $params = [$status];
    $types = "s";

    if ($quoted_price !== null) {
        $update_fields[] = "estimated_cost = ?";
        $params[] = $quoted_price;
        $types .= "d";
    }

    $update_fields[] = "updated_at = NOW()";
    $params[] = $request_id;
    $types .= "i";

    $query = "UPDATE order_requests SET " . implode(", ", $update_fields) . " WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        // Add timeline entry
        $timeline_stmt = $conn->prepare("INSERT INTO request_timeline
            (request_id, status, status_description, created_by)
            VALUES (?, ?, ?, ?)");

        $description = $status_notes ?: "Request status changed to " . ucfirst($status);
        $timeline_stmt->bind_param("issi", $request_id, $status, $description, 1); // admin_id = 1
        $timeline_stmt->execute();

        $_SESSION['success'] = 'Request status updated successfully';
        header("Location: view_request.php?id=$request_id");
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update request status';
    }
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="bi bi-star-fill text-warning me-2"></i>
                Request Details
            </h2>
            <p class="text-muted mb-0">Request #<?php echo $request_id; ?></p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" onclick="changeRequestStatus(<?php echo $request_id; ?>, '<?php echo $request['status']; ?>')">
            <i class="bi bi-arrow-repeat me-1"></i>Update Status
            </button>
            <a href="order_demand_list.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Requests
            </a>
        </div>
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

    <!-- Request Status Banner -->
    <div class="alert alert-<?php
        echo match($request['status']) {
            'pending' => 'warning',
            'sourcing' => 'info',
            'quoted' => 'primary',
            'approved' => 'success',
            'shipped' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'failed' => 'danger',
            default => 'secondary'
        };
    ?> mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle me-2 fs-5"></i>
            <div>
                <strong>Request Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                <?php if ($request['estimated_cost']): ?>
                <br><small>Quoted Price: RWF <?php echo number_format($request['estimated_cost'], 0); ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Customer Information -->
        <div class="col-lg-4">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-circle text-primary fs-4 me-3"></i>
                    <h5 class="mb-0">Customer Information</h5>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Name</label>
                    <p class="mb-2"><?php echo htmlspecialchars($request['customer_name']); ?></p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Phone</label>
                    <p class="mb-2">
                        <a href="tel:<?php echo htmlspecialchars($request['phone_number']); ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($request['phone_number']); ?>
                        </a>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Email</label>
                    <p class="mb-2">
                        <?php if ($request['email']): ?>
                        <a href="mailto:<?php echo htmlspecialchars($request['email']); ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($request['email']); ?>
                        </a>
                        <?php else: ?>
                        N/A
                        <?php endif; ?>
                    </p>
                </div>

                <div class="border-top pt-3">
                    <small class="text-muted">
                        <i class="bi bi-calendar-event me-1"></i>
                        Requested on <?php echo date('M d, Y \a\t H:i', strtotime($request['created_at'])); ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Part Request Details -->
        <div class="col-lg-8">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-gear text-success fs-4 me-3"></i>
                    <h5 class="mb-0">Part Request Details</h5>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Vehicle Brand</label>
                        <p class="mb-2"><?php echo htmlspecialchars($request['vehicle_brand']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Vehicle Model</label>
                        <p class="mb-2"><?php echo htmlspecialchars($request['vehicle_model']); ?></p>
                    </div>
                    <?php if ($request['year']): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Year</label>
                        <p class="mb-2"><?php echo htmlspecialchars($request['year']); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Request Type</label>
                        <span class="badge bg-<?php echo $request['order_type'] === 'urgent' ? 'danger' : 'secondary'; ?>">
                            <?php echo ucfirst($request['order_type']); ?>
                        </span>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-semibold small text-muted">Part Name</label>
                    <p class="mb-2 fw-semibold"><?php echo htmlspecialchars($request['part_name']); ?></p>
                </div>

                <?php if ($request['part_description']): ?>
                <div class="mt-3">
                    <label class="form-label fw-semibold small text-muted">Part Description</label>
                    <p class="mb-2"><?php echo htmlspecialchars($request['part_description']); ?></p>
                </div>
                <?php endif; ?>

                <!-- Images -->
                <?php $images = json_decode($request['images'] ?? '[]', true); ?>
                <?php if (!empty($images)): ?>
                <div class="mt-4">
                <label class="form-label fw-semibold small text-muted">Reference Images</label>
                <div class="row g-2">
                <?php for ($i = 0; $i < count($images); $i++): ?>
                <div class="col-md-4">
                <img src="<?php echo htmlspecialchars($images[$i]); ?>"
                alt="Reference Image <?php echo $i + 1; ?>"
                class="img-fluid rounded cursor-pointer"
                style="max-height: 150px; object-fit: cover; cursor: pointer;"
                onclick="openImageModal('<?php echo htmlspecialchars($images[$i]); ?>')">
                </div>
                <?php endfor; ?>
                </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Request Timeline -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="form-card">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-clock-history text-info fs-4 me-3"></i>
                    <h5 class="mb-0">Request Timeline</h5>
                </div>

                <?php if ($timeline->num_rows > 0): ?>
                <div class="timeline">
                    <?php while ($entry = $timeline->fetch_assoc()): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-<?php
                            echo match($entry['status']) {
                                'pending' => 'warning',
                                'sourcing' => 'info',
                                'quoted' => 'primary',
                                'approved' => 'success',
                                'shipped' => 'info',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                'failed' => 'danger',
                                default => 'secondary'
                            };
                        ?>"></div>
                        <div class="timeline-content">
                            <div class="fw-semibold"><?php echo ucfirst(str_replace('_', ' ', $entry['status'])); ?></div>
                            <div class="small text-muted"><?php echo date('M d, Y H:i', strtotime($entry['created_at'])); ?></div>
                            <?php if ($entry['status_description']): ?>
                            <div class="small"><?php echo htmlspecialchars($entry['status_description']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-clock-history text-muted fs-1 mb-2"></i>
                    <p class="text-muted mb-0">No timeline entries yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-arrow-repeat me-2"></i>Update Request Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="requestId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Request Status</label>
                            <select class="form-select" name="request_status" id="requestStatus" required>
                                <option value="pending">Pending</option>
                                <option value="sourcing">Sourcing</option>
                                <option value="quoted">Quoted</option>
                                <option value="approved">Approved</option>
                                <option value="shipped">Shipped</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="quoteFields" style="display: none;">
                            <label class="form-label fw-semibold">Quoted Price (RWF)</label>
                            <input type="number" class="form-control" name="quoted_price" placeholder="Enter your quote" step="0.01">
                            <div class="form-text">Leave empty if not quoting yet</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Status Notes (Optional)</label>
                            <textarea class="form-control" name="status_notes" rows="3" placeholder="Add notes about this status change..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img id="modalImage" src="" alt="Reference Image" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 6px;
    border-left: 3px solid #dee2f2;
}
</style>

<script>
// Request management functions
function changeRequestStatus(requestId, currentStatus) {
    document.getElementById('requestId').value = requestId;
    document.getElementById('requestStatus').value = currentStatus;

    // Show quote fields for quoted status
    const quoteFields = document.getElementById('quoteFields');
    const statusSelect = document.getElementById('requestStatus');

    function toggleQuoteFields() {
        const showQuote = ['quoted', 'approved'].includes(statusSelect.value);
        quoteFields.style.display = showQuote ? 'block' : 'none';
    }

    statusSelect.addEventListener('change', toggleQuoteFields);
    toggleQuoteFields();

    new bootstrap.Modal(document.getElementById('statusModal')).show();
}

function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

<?php include '../footer.php'; ?>