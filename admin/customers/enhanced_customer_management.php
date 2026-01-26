<?php
// Enhanced Customer Management - Admin Panel
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Get filter and search parameters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query based on filter
$where_conditions = [];
$params = [];
$types = '';

if ($filter === 'active') {
    $where_conditions[] = "c.status = 'active'";
} elseif ($filter === 'unread') {
    $where_conditions[] = "EXISTS (SELECT 1 FROM notifications n WHERE n.user_id = c.id AND n.type = 'message' AND n.is_read = 0)";
} elseif ($filter === 'recent') {
    $where_conditions[] = "c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}

if (!empty($search)) {
    $where_conditions[] = "(c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)";
    $search_param = "%$search%";
    $params = array_fill(0, 4, $search_param);
    $types = 'ssss';
}

// Get customers with conversation and notification data
$query = "
    SELECT
        c.*,
        COUNT(DISTINCT conv.id) as total_conversations,
        COUNT(m.id) as total_messages,
        COUNT(CASE WHEN n.is_read = 0 AND n.type = 'message' THEN 1 END) as unread_messages,
        MAX(m.created_at) as last_message_time,
        MAX(n.created_at) as last_notification_time
    FROM customers_enhanced c
    LEFT JOIN conversations conv ON c.id = conv.client_id
    LEFT JOIN messages m ON conv.id = m.conversation_id
    LEFT JOIN notifications n ON c.id = n.user_id AND n.type = 'message'
    " . (!empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "") . "
    GROUP BY c.id
    ORDER BY
        CASE WHEN COUNT(CASE WHEN n.is_read = 0 AND n.type = 'message' THEN 1 END) > 0 THEN 0 ELSE 1 END,
        GREATEST(COALESCE(MAX(m.created_at), '1970-01-01'), COALESCE(MAX(n.created_at), '1970-01-01'), c.created_at) DESC
";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$customers = $stmt->get_result();

// Get customer details if one is selected for modal
$customer_id = $_GET['customer_id'] ?? null;
$customer_details = null;

if ($customer_id) {
    $detail_query = "
        SELECT
            c.*,
            COUNT(DISTINCT conv.id) as total_conversations,
            COUNT(m.id) as total_messages,
            COUNT(CASE WHEN n.is_read = 0 AND n.type = 'message' THEN 1 END) as unread_messages,
            MAX(m.created_at) as last_activity,
            MIN(c.created_at) as registration_date
        FROM customers_enhanced c
        LEFT JOIN conversations conv ON c.id = conv.client_id
        LEFT JOIN messages m ON conv.id = m.conversation_id
        LEFT JOIN notifications n ON c.id = n.user_id AND n.type = 'message'
        WHERE c.id = ?
        GROUP BY c.id
    ";
    $detail_stmt = $conn->prepare($detail_query);
    $detail_stmt->bind_param("i", $customer_id);
    $detail_stmt->execute();
    $customer_details = $detail_stmt->get_result()->fetch_assoc();
}
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-people-fill text-primary me-3"></i>
                Customer Management
            </h1>
            <p class="text-muted mb-0 fs-5">Manage and communicate with your customers</p>
        </div>
        <div class="text-end">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-primary" onclick="refreshCustomers()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
                <div class="text-center">
                    <div class="fw-bold text-info fs-4"><?php echo $customers->num_rows; ?></div>
                    <small class="text-muted">Total Customers</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="form-card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </label>
                    <select class="form-select" id="filter-select">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Customers</option>
                        <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active Only</option>
                        <option value="unread" <?php echo $filter === 'unread' ? 'selected' : ''; ?>>With Unread Messages</option>
                        <option value="recent" <?php echo $filter === 'recent' ? 'selected' : ''; ?>>Recently Contacted</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-search me-1"></i>Global Search
                    </label>
                    <input type="text" class="form-control" id="search-input" placeholder="Search by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-search me-1"></i>Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="form-card">
        <div class="card-header bg-light border-bottom-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-people-fill text-primary me-2"></i>
                    Customers (<?php echo $customers->num_rows; ?>)
                </h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm" id="live-search" placeholder="Search customers..." style="width: 250px;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearSearch()">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="customers-table">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">#</th>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Phone</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Messages</th>
                            <th class="border-0">Last Activity</th>
                            <th class="border-0 pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customers-tbody">
                        <?php if ($customers->num_rows > 0): ?>
                            <?php while ($customer = $customers->fetch_assoc()): ?>
                                <tr class="customer-row">
                                    <td class="ps-4 fw-bold text-primary"><?php echo $customer['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="customer-avatar me-3">
                                                <i class="bi bi-person-circle text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></div>
                                                <small class="text-muted">ID: <?php echo $customer['id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($customer['email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo htmlspecialchars($customer['phone']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($customer['phone']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $customer['customer_status'] === 'active' ? 'success' : 'secondary'; ?> badge-sm">
                                            <i class="bi bi-circle-fill me-1"></i><?php echo ucfirst($customer['customer_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-primary"><?php echo $customer['total_messages']; ?> total</span>
                                            <?php if ($customer['unread_messages'] > 0): ?>
                                                <span class="badge bg-danger"><?php echo $customer['unread_messages']; ?> unread</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php
                                            $last_activity = max($customer['last_message_time'], $customer['last_notification_time'], $customer['created_at']);
                                            echo $last_activity ? date('M d, H:i', strtotime($last_activity)) : 'Never';
                                            ?>
                                        </small>
                                    </td>
                                    <td class="pe-4">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewCustomer(<?php echo $customer['id']; ?>)" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="messageCustomer(<?php echo $customer['id']; ?>)" title="Send Message">
                                                <i class="bi bi-chat-dots"></i>
                                                <?php if ($customer['unread_messages'] > 0): ?>
                                                    <span class="badge bg-danger badge-xs position-absolute top-0 start-100 translate-middle"><?php echo $customer['unread_messages']; ?></span>
                                                <?php endif; ?>
                                            </button>
                                            <a href="/admin/customers/view_customer.php?id=<?php echo $customer['id']; ?>" class="btn btn-sm btn-outline-info" title="Customer Portal">
                                                <i class="bi bi-person-badge"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr id="no-results-row">
                                <td colspan="8" class="text-center py-5">
                                    <i class="bi bi-people text-muted fs-1 mb-3"></i>
                                    <h6 class="text-muted">No customers found</h6>
                                    <p class="text-muted small">Try adjusting your search or filter criteria</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Detail Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">
                        <i class="bi bi-person-circle me-2"></i>Customer Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="customerModalBody">
                    <!-- Customer details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="messageBtn" onclick="messageCustomerFromModal()">
                        <i class="bi bi-chat-dots me-1"></i>Send Message
                    </button>
                    <a href="#" id="portalLink" class="btn btn-info">
                        <i class="bi bi-person-badge me-1"></i>Open Portal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.customer-avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    font-size: 1.2rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
    background: #f8f9fa;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
    margin-right: 2px;
    position: relative;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.badge-xs {
    font-size: 0.65rem;
    padding: 0.2em 0.4em;
}

.customer-row {
    transition: all 0.2s ease;
}

.customer-row:hover {
    transform: translateX(2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#live-search {
    border-radius: 20px;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

#live-search:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize live search
    const liveSearch = document.getElementById('live-search');
    const customerRows = document.querySelectorAll('.customer-row');
    const noResultsRow = document.getElementById('no-results-row');

    liveSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

        customerRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Show/hide no results row
        if (visibleCount === 0 && customerRows.length > 0) {
            noResultsRow.style.display = '';
        } else {
            noResultsRow.style.display = 'none';
        }
    });
});

function applyFilters() {
    const filter = document.getElementById('filter-select').value;
    const search = document.getElementById('search-input').value;

    const params = new URLSearchParams(window.location.search);
    params.set('filter', filter);
    params.set('search', search);

    window.location.search = params.toString();
}

function clearSearch() {
    document.getElementById('live-search').value = '';
    document.getElementById('live-search').dispatchEvent(new Event('input'));
}

function refreshCustomers() {
    window.location.reload();
}

function viewCustomer(customerId) {
    // Load customer details via AJAX
    fetch(`/admin/api/get_customer_details.php?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const customer = data.customer;
                const modalBody = document.getElementById('customerModalBody');
                const messageBtn = document.getElementById('messageBtn');
                const portalLink = document.getElementById('portalLink');

                let content = `
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <div class="customer-avatar mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #007bff, #0056b3);">
                                <i class="bi bi-person-circle fs-1 text-white"></i>
                            </div>
                            <h5 class="text-primary">${customer.first_name} ${customer.last_name}</h5>
                            <span class="badge bg-${customer.customer_status === 'active' ? 'success' : 'secondary'}">${ucfirst(customer.customer_status)}</span>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:${customer.email}" class="text-decoration-none">${customer.email}</a>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Phone:</strong><br>
                                    <a href="tel:${customer.phone}" class="text-decoration-none">${customer.phone}</a>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Registration Date:</strong><br>
                                    ${new Date(customer.created_at).toLocaleDateString()}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Last Activity:</strong><br>
                                    ${customer.last_activity ? new Date(customer.last_activity).toLocaleString() : 'Never'}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Total Conversations:</strong><br>
                                    ${customer.total_conversations}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Total Messages:</strong><br>
                                    ${customer.total_messages}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                modalBody.innerHTML = content;
                messageBtn.setAttribute('data-customer-id', customerId);
                portalLink.href = `/admin/customers/view_customer.php?id=${customerId}`;

                const modal = new bootstrap.Modal(document.getElementById('customerModal'));
                modal.show();
            } else {
                alert('Error loading customer details');
            }
        })
        .catch(error => {
            console.error('Error loading customer details:', error);
            alert('Error loading customer details');
        });
}

function messageCustomer(customerId) {
    // Redirect to notifications page with customer conversation
    window.location.href = `/admin/notifications/notification_manager.php?customer_id=${customerId}`;
}

function messageCustomerFromModal() {
    const customerId = document.getElementById('messageBtn').getAttribute('data-customer-id');
    if (customerId) {
        messageCustomer(customerId);
    }
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>

<?php include '../footer.php'; ?>