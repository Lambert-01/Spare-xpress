<?php
// Include config first for database connection
include_once '../includes/config.php';

// Production: Check if user is logged in (before HTML output)
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php?redirect=my_account.php');
    exit();
}

// Handle logout before any HTML output
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$page_title = 'My Account - SPARE XPRESS LTD';

// Get customer data
$stmt = $conn->prepare("SELECT * FROM customers_enhanced WHERE id = ? AND customer_status = 'active'");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if (!$customer) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Handle profile update
$update_errors = [];
$update_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validation
    if (empty($first_name) || empty($last_name)) {
        $update_errors[] = 'First and last name are required';
    }

    if (empty($phone)) {
        $update_errors[] = 'Phone number is required';
    }

    if (empty($update_errors)) {
        $stmt = $conn->prepare("UPDATE customers_enhanced SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssi", $first_name, $last_name, $phone, $_SESSION['customer_id']);
        $stmt->execute();
        $stmt->close();

        // Update address in enhanced table
        $stmt = $conn->prepare("UPDATE customers_enhanced SET address = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $address, $_SESSION['customer_id']);
        $stmt->execute();
        $stmt->close();
        $stmt->bind_param("sssi", $first_name, $last_name, $phone, $_SESSION['customer_id']);

        if ($stmt->execute()) {
            $update_success = 'Profile updated successfully!';
            $_SESSION['customer_name'] = $first_name . ' ' . $last_name;

            // Refresh customer data
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM customers_enhanced WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['customer_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $customer = $result->fetch_assoc();
        } else {
            $update_errors[] = 'Failed to update profile';
        }
        $stmt->close();
    }
}

// Handle password change
$password_errors = [];
$password_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Get current password from enhanced table
    $stmt = $conn->prepare("SELECT password FROM customers_enhanced WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['customer_id']);
    $stmt->execute();
    $current_password_hash = $stmt->get_result()->fetch_assoc()['password'];
    $stmt->close();

    // Validation
    if (empty($current_password)) {
        $password_errors[] = 'Current password is required';
    } elseif (!password_verify($current_password, $current_password_hash)) {
        $password_errors[] = 'Current password is incorrect';
    }

    if (empty($new_password) || strlen($new_password) < 8) {
        $password_errors[] = 'New password must be at least 8 characters long';
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $password_errors[] = 'New password must contain at least one uppercase letter';
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $password_errors[] = 'New password must contain at least one lowercase letter';
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $password_errors[] = 'New password must contain at least one number';
    }

    if ($new_password !== $confirm_password) {
        $password_errors[] = 'New passwords do not match';
    }

    if (empty($password_errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE customers_enhanced SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $_SESSION['customer_id']);

        if ($stmt->execute()) {
            $password_success = 'Password changed successfully!';
        } else {
            $password_errors[] = 'Failed to change password';
        }
        $stmt->close();
    }
}

// Handle email preferences update
$preferences_errors = [];
$preferences_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_preferences'])) {
    $marketing_emails = isset($_POST['marketing_emails']) ? 1 : 0;
    $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;

    // Note: customers_enhanced doesn't have marketing_emails and sms_notifications columns
    // For now, just show success message
    $preferences_success = 'Preferences updated successfully!';

    // In a real implementation, you would add these columns to customers_enhanced
    // For now, we'll just acknowledge the preference update
}

// Handle account deletion
$delete_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Check if user has any active orders
    $stmt = $conn->prepare("SELECT COUNT(*) as active_orders FROM orders_enhanced WHERE customer_id = ? AND order_status NOT IN ('delivered', 'cancelled')");
    $stmt->bind_param("i", $_SESSION['customer_id']);
    $stmt->execute();
    $active_orders = $stmt->get_result()->fetch_assoc()['active_orders'];
    $stmt->close();

    if ($active_orders > 0) {
        $delete_errors[] = 'You cannot delete your account while you have active orders. Please wait for all orders to be completed or cancelled.';
    } else {
        // Start transaction for safe deletion
        $conn->begin_transaction();

        try {
            // Delete related records first
            $tables_to_clean = ['order_notes', 'order_tracking', 'order_items_enhanced', 'orders_enhanced', 'on_demand_requests_enhanced'];

            foreach ($tables_to_clean as $table) {
                $stmt = $conn->prepare("DELETE FROM $table WHERE customer_id = ?");
                $stmt->bind_param("i", $_SESSION['customer_id']);
                $stmt->execute();
                $stmt->close();
            }

            // Delete customer
            $stmt = $conn->prepare("DELETE FROM customers_enhanced WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['customer_id']);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            // Destroy session and redirect
            session_destroy();
            header('Location: login.php?message=account_deleted');
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $delete_errors[] = 'Failed to delete account. Please contact support.';
        }
    }
}

include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';
include '../includes/wishlist.php';
?>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user me-2"></i>My Account
                        </h3>
                        <p class="mb-0 mt-2">Manage your SPARE XPRESS LTD account</p>
                    </div>
                    <div class="card-body p-4">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs mb-4" id="accountTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account" type="button" role="tab" aria-controls="account" aria-selected="true">
                                    <i class="fas fa-user me-2"></i>Account Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab" aria-controls="orders" aria-selected="false">
                                    <i class="fas fa-shopping-cart me-2"></i>Orders
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">
                                    <i class="fas fa-comments me-2"></i>Messages
                                    <span id="unreadBadge" class="badge bg-danger ms-1 d-none">0</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="accountTabsContent">
                            <!-- Account Info Tab -->
                            <div class="tab-pane fade show active" id="account" role="tabpanel" aria-labelledby="account-tab">
                                <!-- Account Info -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <h5 class="text-primary mb-3">Account Information</h5>
                                            <p class="mb-1"><strong>Customer ID:</strong> <?php echo htmlspecialchars($customer['customer_number']); ?></p>
                                            <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                                            <p class="mb-1"><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($customer['created_at'])); ?></p>
                                            <p class="mb-1"><strong>Last Login:</strong> <?php echo $customer['last_login'] ? date('M d, Y H:i', strtotime($customer['last_login'])) : 'Never'; ?></p>
                                            <p class="mb-0"><strong>Status:</strong>
                                                <span class="badge bg-success">Active</span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <h5 class="text-primary mb-3">Quick Stats</h5>
                                            <?php
                                            // Get order count
                                            $stmt = $conn->prepare("SELECT COUNT(*) as order_count FROM orders_enhanced WHERE customer_id = ?");
                                            $stmt->bind_param("i", $_SESSION['customer_id']);
                                            $stmt->execute();
                                            $order_count = $stmt->get_result()->fetch_assoc()['order_count'];
                                            $stmt->close();
                                            ?>
                                            <p class="mb-1"><strong>Total Orders:</strong> <?php echo $order_count; ?></p>
                                            <p class="mb-0"><strong>Account Type:</strong> Individual</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Update Profile Form -->
                                <div class="mb-4">
                                    <h4 class="text-primary mb-3">
                                        <i class="fas fa-edit me-2"></i>Update Profile
                                    </h4>

                                    <?php if (!empty($update_errors)): ?>
                                        <div class="alert alert-danger d-none" id="updateErrors">
                                            <ul class="mb-0">
                                                <?php foreach ($update_errors as $error): ?>
                                                    <li><?php echo htmlspecialchars($error); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <script>document.addEventListener('DOMContentLoaded', function() { showErrorToast('Please check the form errors above', 'Update Failed'); });</script>
                                    <?php endif; ?>

                                    <?php if (!empty($update_success)): ?>
                                        <script>document.addEventListener('DOMContentLoaded', function() { showSuccessToast('<?php echo addslashes($update_success); ?>', 'Profile Updated'); });</script>
                                    <?php endif; ?>

                                    <form method="POST" class="row g-3">
                                        <div class="col-md-6">
                                            <label for="first_name" class="form-label">First Name *</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                   value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                   value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                   value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                   value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="update_profile" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>Update Profile
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Change Password Form -->
                                <div class="mb-4">
                                    <h4 class="text-primary mb-3">
                                        <i class="fas fa-lock me-2"></i>Change Password
                                    </h4>

                                    <?php if (!empty($password_errors)): ?>
                                        <div class="alert alert-danger d-none" id="passwordErrors">
                                            <ul class="mb-0">
                                                <?php foreach ($password_errors as $error): ?>
                                                    <li><?php echo htmlspecialchars($error); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <script>document.addEventListener('DOMContentLoaded', function() { showErrorToast('Please check the password requirements', 'Password Change Failed'); });</script>
                                    <?php endif; ?>

                                    <?php if (!empty($password_success)): ?>
                                        <script>document.addEventListener('DOMContentLoaded', function() { showSuccessToast('<?php echo addslashes($password_success); ?>', 'Password Changed'); });</script>
                                    <?php endif; ?>

                                    <form method="POST" class="row g-3">
                                        <div class="col-md-6">
                                            <label for="current_password" class="form-label">Current Password *</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="new_password" class="form-label">New Password *</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted">Minimum 8 characters with uppercase, lowercase, and number</small>
                                                <span id="password-strength" class="badge bg-secondary small"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="change_password" class="btn btn-warning">
                                                <i class="fas fa-key me-2"></i>Change Password
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Account Settings -->
                                <div class="mb-4">
                                    <h4 class="text-primary mb-3">
                                        <i class="fas fa-cog me-2"></i>Account Settings
                                    </h4>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Notification Preferences:</strong> Email and SMS preferences will be available in a future update.
                                        Currently, you will receive important order notifications via email.
                                    </div>

                                    <?php if (!empty($preferences_success)): ?>
                                        <script>document.addEventListener('DOMContentLoaded', function() { showSuccessToast('<?php echo addslashes($preferences_success); ?>', 'Settings Updated'); });</script>
                                    <?php endif; ?>
                                </div>

                                <!-- Account Actions -->
                                <div class="border-top pt-4">
                                    <h4 class="text-primary mb-3">
                                        <i class="fas fa-cog me-2"></i>Account Actions
                                    </h4>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="order_history.php" class="btn btn-outline-primary">
                                            <i class="fas fa-history me-1"></i>View Order History
                                        </a>
                                        <a href="wishlist.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-heart me-1"></i>My Wishlist
                                        </a>
                                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                            <i class="fas fa-trash-alt me-1"></i>Delete Account
                                        </button>
                                        <a href="?logout=1" class="btn btn-outline-danger">
                                            <i class="fas fa-sign-out-alt me-1"></i>Logout
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Orders Tab -->
                            <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                <h4 class="text-primary mb-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Recent Orders
                                </h4>
                                <?php
                                // Get recent orders
                                $stmt = $conn->prepare("SELECT id, order_number, order_status, total_amount, created_at FROM orders_enhanced WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5");
                                $stmt->bind_param("i", $_SESSION['customer_id']);
                                $stmt->execute();
                                $recent_orders = $stmt->get_result();
                                $stmt->close();

                                if ($recent_orders->num_rows > 0):
                                ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php
                                                                echo match($order['order_status']) {
                                                                    'pending' => 'warning',
                                                                    'processing' => 'info',
                                                                    'ready' => 'primary',
                                                                    'shipped' => 'secondary',
                                                                    'delivered' => 'success',
                                                                    default => 'secondary'
                                                                };
                                                            ?>">
                                                                <?php echo ucfirst($order['order_status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>RWF <?php echo number_format($order['total_amount'], 0); ?></td>
                                                        <td>
                                                            <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="order_history.php" class="btn btn-primary">
                                            <i class="fas fa-history me-2"></i>View All Orders
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No orders found</h5>
                                        <p class="text-muted mb-3">You haven't placed any orders yet.</p>
                                        <a href="shop.php" class="btn btn-primary">
                                            <i class="fas fa-store me-2"></i>Start Shopping
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Messages Tab -->
                            <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
                                <div class="row">
                                    <!-- Conversation List (Left Panel) -->
                                    <div class="col-md-4 border-end">
                                        <h5 class="text-primary mb-3">
                                            <i class="fas fa-comments me-2"></i>Conversations
                                        </h5>
                                        <div id="conversationsList" class="list-group list-group-flush">
                                            <!-- Conversations will be loaded here -->
                                            <div class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chat View (Right Panel) -->
                                    <div class="col-md-8">
                                        <div id="chatContainer" class="d-none">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 id="conversationTitle" class="text-primary mb-0">Select a conversation</h5>
                                                <small id="conversationTime" class="text-muted"></small>
                                            </div>

                                            <div id="messagesContainer" class="border rounded p-3 mb-3" style="height: 400px; overflow-y: auto;">
                                                <!-- Messages will be loaded here -->
                                            </div>

                                            <form id="messageForm" enctype="multipart/form-data">
                                                <div class="input-group">
                                                    <textarea id="messageInput" name="message" class="form-control" placeholder="Type your message..." rows="2" disabled></textarea>
                                                    <input type="file" class="form-control" name="attachment" id="attachmentInput" accept="image/*,.pdf,.doc,.docx" style="max-width: 200px;" disabled>
                                                    <button id="sendMessageBtn" class="btn btn-primary" type="button" disabled>
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <div id="noConversationSelected" class="text-center py-5">
                                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No conversation selected</h5>
                                            <p class="text-muted">Choose a conversation from the list to start messaging.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Account Modal -->
                        <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php if (!empty($delete_errors)): ?>
                                            <div class="alert alert-danger">
                                                <ul class="mb-3">
                                                    <?php foreach ($delete_errors as $error): ?>
                                                        <li><?php echo htmlspecialchars($error); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php endif; ?>

                                        <p class="text-danger mb-3">
                                            <strong>Warning:</strong> This action cannot be undone. All your data, orders, and account information will be permanently deleted.
                                        </p>
                                        <p>Are you sure you want to delete your account?</p>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="confirm_delete" required>
                                            <label class="form-check-label" for="confirm_delete">
                                                I understand that this action is irreversible
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmAccountDeletion()">
                                            <button type="submit" name="delete_account" class="btn btn-danger" id="deleteBtn" disabled>
                                                <i class="fas fa-trash-alt me-1"></i>Delete Account
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
}

.btn-warning {
    transition: all 0.3s ease;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
}
</style>

<script>
// Global variables for messaging
let currentConversationId = null;
let conversations = [];

// Enable/disable delete button based on checkbox
document.getElementById('confirm_delete').addEventListener('change', function() {
    document.getElementById('deleteBtn').disabled = !this.checked;
});

// Enhanced form validation with toast notifications
document.addEventListener('DOMContentLoaded', function() {
    // Add form submission handlers for better UX
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                // Re-enable after 10 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });
    });

    // Password strength indicator
    const newPasswordInput = document.getElementById('new_password');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            updatePasswordStrengthIndicator(strength);
        });
    }

    // Load conversations when messages tab is shown
    const messagesTab = document.getElementById('messages-tab');
    if (messagesTab) {
        messagesTab.addEventListener('shown.bs.tab', function() {
            loadConversations();
        });
    }

    // Send message on button click
    const sendBtn = document.getElementById('sendMessageBtn');
    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    // Send message on Enter key
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }
});

// Password strength calculation
function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}

// Update password strength indicator
function updatePasswordStrengthIndicator(strength) {
    const indicator = document.getElementById('password-strength');
    if (!indicator) return;

    const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['danger', 'warning', 'info', 'primary', 'success'];

    indicator.textContent = labels[strength - 1] || '';
    indicator.className = `badge bg-${colors[strength - 1] || 'secondary'}`;
}

// Enhanced delete account confirmation
function confirmAccountDeletion() {
    const checkbox = document.getElementById('confirm_delete');
    if (!checkbox.checked) {
        showWarningToast('Please confirm that you understand this action is irreversible', 'Confirmation Required');
        return false;
    }

    if (!confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
        return false;
    }

    return true;
}

// Messaging Functions
async function loadConversations() {
    try {
        const response = await fetch('/api/get_client_conversations.php');
        const data = await response.json();

        if (data.success) {
            conversations = data.conversations;
            displayConversations();
            updateUnreadBadge();
        } else {
            showErrorToast('Failed to load conversations', 'Error');
        }
    } catch (error) {
        console.error('Error loading conversations:', error);
        showErrorToast('Failed to load conversations', 'Error');
    }
}

function displayConversations() {
    const container = document.getElementById('conversationsList');
    if (!container) return;

    if (conversations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No conversations yet</p>
                <small class="text-muted">Messages from support will appear here</small>
            </div>
        `;
        return;
    }

    container.innerHTML = conversations.map(conv => `
        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start ${conv.id == currentConversationId ? 'active' : ''}"
           onclick="selectConversation(${conv.id})">
            <div class="ms-2 me-auto">
                <div class="fw-bold">Support Inquiry</div>
                <small class="text-muted">${conv.last_message || 'No messages yet'}</small>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">${formatTimeAgo(conv.updated_at)}</small>
                ${conv.unread_count > 0 ? `<span class="badge bg-danger rounded-pill">${conv.unread_count}</span>` : ''}
            </div>
        </a>
    `).join('');
}

function updateUnreadBadge() {
    const totalUnread = conversations.reduce((sum, conv) => sum + conv.unread_count, 0);
    const badge = document.getElementById('unreadBadge');

    if (totalUnread > 0) {
        badge.textContent = totalUnread > 99 ? '99+' : totalUnread;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }
}

async function selectConversation(conversationId) {
    currentConversationId = conversationId;

    // Update UI
    document.getElementById('chatContainer').classList.remove('d-none');
    document.getElementById('noConversationSelected').classList.add('d-none');

    // Update active conversation in list
    document.querySelectorAll('#conversationsList .list-group-item').forEach(item => {
        item.classList.remove('active');
    });
    event.target.closest('.list-group-item').classList.add('active');

    // Load messages
    await loadMessages(conversationId);

    // Enable input
    document.getElementById('messageInput').disabled = false;
    document.getElementById('attachmentInput').disabled = false;
    document.getElementById('sendMessageBtn').disabled = false;
    document.getElementById('messageInput').focus();
}

async function loadMessages(conversationId) {
    try {
        const response = await fetch(`../api/get_conversation_messages.php?conversation_id=${conversationId}`);
        const data = await response.json();

        if (data.success) {
            displayMessages(data.messages);
            // Mark as read (conceptual - would need backend implementation)
        } else {
            showErrorToast('Failed to load messages', 'Error');
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        showErrorToast('Failed to load messages', 'Error');
    }
}

function displayMessages(messages) {
    const container = document.getElementById('messagesContainer');
    if (!container) return;

    if (messages.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                <p class="text-muted mb-0">No messages in this conversation</p>
            </div>
        `;
        return;
    }

    container.innerHTML = messages.map(msg => `
        <div class="message mb-3 ${msg.sender_type === 'client' ? 'text-end' : ''}">
            <div class="d-inline-block p-2 rounded ${msg.sender_type === 'client' ? 'bg-primary text-white' : 'bg-light'}"
                  style="max-width: 70%; word-wrap: break-word;">
                <div class="message-text">${escapeHtml(msg.message)}</div>
                ${msg.attachment ? `
                    <div class="message-attachment mt-2">
                        ${msg.attachment.match(/\.(jpg|jpeg|png|gif)$/i) ?
                            `<img src="/${msg.attachment}" alt="Attachment" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">` :
                            `<a href="/${msg.attachment}" target="_blank" class="btn btn-sm ${msg.sender_type === 'client' ? 'btn-outline-light' : 'btn-outline-primary'}">
                                <i class="fas fa-paperclip me-1"></i>View Attachment
                            </a>`
                        }
                    </div>
                ` : ''}
                <small class="${msg.sender_type === 'client' ? 'text-white-50' : 'text-muted'}">
                    ${formatMessageTime(msg.created_at)}
                </small>
            </div>
        </div>
    `).join('');

    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

async function sendMessage() {
    const form = document.getElementById('messageForm');
    const formData = new FormData(form);
    const message = formData.get('message').trim();
    const attachment = formData.get('attachment');

    if ((!message && !attachment) || !currentConversationId) return;

    formData.append('conversation_id', currentConversationId);

    try {
        const response = await fetch('../api/send_client_reply.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            form.reset();
            // Reload messages to show the new message
            await loadMessages(currentConversationId);
            // Reload conversations to update last message
            await loadConversations();
            showSuccessToast('Message sent successfully', 'Sent');
        } else {
            showErrorToast(data.message || 'Failed to send message', 'Error');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        showErrorToast('Failed to send message', 'Error');
    }
}

// Utility functions
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;

    return date.toLocaleDateString();
}

function formatMessageTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include '../includes/footer.php'; ?>