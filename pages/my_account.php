<?php
include_once '../includes/config.php';

if (!isset($_SESSION['customer_id'])) { header('Location: login.php?redirect=my_account.php'); exit(); }
if (isset($_GET['logout'])) { session_destroy(); header('Location: login.php'); exit(); }

$page_title = 'My Account - SPARE XPRESS LTD';

$stmt = $conn->prepare("SELECT * FROM customers_enhanced WHERE id = ? AND customer_status = 'active'");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$customer) { session_destroy(); header('Location: login.php'); exit(); }

$update_success = ''; $update_errors = [];
$password_success = ''; $password_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    if (empty($first_name) || empty($last_name)) $update_errors[] = 'First and last name are required';
    if (empty($phone)) $update_errors[] = 'Phone number is required';
    if (empty($update_errors)) {
        $stmt = $conn->prepare("UPDATE customers_enhanced SET first_name=?, last_name=?, phone=?, address=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssssi", $first_name, $last_name, $phone, $address, $_SESSION['customer_id']);
        $stmt->execute(); $stmt->close();
        $_SESSION['customer_name'] = $first_name . ' ' . $last_name;
        $update_success = 'Profile updated successfully!';
        $stmt = $conn->prepare("SELECT * FROM customers_enhanced WHERE id=?");
        $stmt->bind_param("i", $_SESSION['customer_id']);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $cur = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $con = $_POST['confirm_password'] ?? '';
    $stmt = $conn->prepare("SELECT password FROM customers_enhanced WHERE id=?");
    $stmt->bind_param("i", $_SESSION['customer_id']);
    $stmt->execute();
    $hash = $stmt->get_result()->fetch_assoc()['password'];
    $stmt->close();
    if (!password_verify($cur, $hash)) $password_errors[] = 'Current password is incorrect';
    if (strlen($new) < 8) $password_errors[] = 'New password must be at least 8 characters';
    if ($new !== $con) $password_errors[] = 'New passwords do not match';
    if (empty($password_errors)) {
        $h = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE customers_enhanced SET password=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("si", $h, $_SESSION['customer_id']);
        $stmt->execute(); $stmt->close();
        $password_success = 'Password changed successfully!';
    }
}

// Stats
$stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders_enhanced WHERE customer_id=?");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$order_count = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as c FROM orders_enhanced WHERE customer_id=? AND order_status NOT IN ('delivered','cancelled')");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$active_orders = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

// Profile completeness
$fields = ['first_name','last_name','email','phone','address'];
$filled = array_filter($fields, fn($f) => !empty($customer[$f]));
$completeness = round(count($filled) / count($fields) * 100);

// Recent orders
$stmt = $conn->prepare("SELECT id, order_number, order_status, payment_status, total_amount, created_at FROM orders_enhanced WHERE customer_id=? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $_SESSION['customer_id']);
$stmt->execute();
$res = $stmt->get_result();
$recent_orders = [];
while ($row = $res->fetch_assoc()) $recent_orders[] = $row;
$stmt->close();

$active_tab = $_GET['tab'] ?? 'overview';

include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';
?>

<!-- Page Hero -->
<div class="spx-page-hero">
    <div class="container position-relative">
        <h1 class="fw-bold mb-2"><i class="fas fa-user-circle me-2"></i>My Account</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item active">My Account</li>
            </ol>
        </nav>
    </div>
</div>

<div class="spx-portal-wrap py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="spx-sidebar">
                    <div class="spx-sidebar-header">
                        <div class="spx-sidebar-avatar"><i class="fas fa-user"></i></div>
                        <div class="spx-sidebar-name"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></div>
                        <div class="spx-sidebar-email"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>
                    <!-- Profile completeness -->
                    <div class="px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">Profile complete</small>
                            <small class="fw-600 text-primary"><?php echo $completeness; ?>%</small>
                        </div>
                        <div class="spx-profile-progress-bar-wrap">
                            <div class="spx-profile-progress-bar" style="width:<?php echo $completeness; ?>%"></div>
                        </div>
                    </div>
                    <nav class="spx-sidebar-nav">
                        <a href="?tab=overview" class="<?php echo $active_tab==='overview'?'active':''; ?>"><i class="fas fa-th-large"></i>Overview</a>
                        <a href="?tab=profile" class="<?php echo $active_tab==='profile'?'active':''; ?>"><i class="fas fa-user-edit"></i>Edit Profile</a>
                        <a href="?tab=password" class="<?php echo $active_tab==='password'?'active':''; ?>"><i class="fas fa-lock"></i>Change Password</a>
                        <div class="spx-sidebar-divider"></div>
                        <a href="order_history.php"><i class="fas fa-shopping-bag"></i>Order History</a>
                        <a href="messages.php"><i class="fas fa-comments"></i>Messages</a>
                        <div class="spx-sidebar-divider"></div>
                        <a href="?logout=1" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i>Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">

                <?php if ($active_tab === 'overview'): ?>
                <!-- Overview Tab -->
                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="spx-stat-card">
                            <div class="spx-stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
                            <div><div class="spx-stat-value"><?php echo $order_count; ?></div><div class="spx-stat-label">Total Orders</div></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spx-stat-card">
                            <div class="spx-stat-icon orange"><i class="fas fa-clock"></i></div>
                            <div><div class="spx-stat-value"><?php echo $active_orders; ?></div><div class="spx-stat-label">Active Orders</div></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spx-stat-card">
                            <div class="spx-stat-icon green"><i class="fas fa-user-check"></i></div>
                            <div><div class="spx-stat-value"><?php echo $completeness; ?>%</div><div class="spx-stat-label">Profile Done</div></div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spx-stat-card">
                            <div class="spx-stat-icon purple"><i class="fas fa-calendar-alt"></i></div>
                            <div><div class="spx-stat-value"><?php echo date('Y', strtotime($customer['created_at'])); ?></div><div class="spx-stat-label">Member Since</div></div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="spx-panel mb-4">
                    <div class="spx-panel-header"><h5 class="spx-panel-title">Quick Actions</h5></div>
                    <div class="spx-panel-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <a href="/pages/shop.php" class="spx-quick-action"><i class="fas fa-store"></i>Browse Parts</a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="/pages/order_request.php" class="spx-quick-action"><i class="fas fa-search"></i>Request Part</a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="order_history.php" class="spx-quick-action"><i class="fas fa-list-alt"></i>My Orders</a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="messages.php" class="spx-quick-action"><i class="fas fa-comments"></i>Messages</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="spx-panel">
                    <div class="spx-panel-header">
                        <h5 class="spx-panel-title">Recent Orders</h5>
                        <a href="order_history.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="spx-panel-body">
                        <?php if (empty($recent_orders)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-3">No orders yet.</p>
                                <a href="/pages/shop.php" class="btn btn-primary">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="spx-order-card mb-3" onclick="window.location='order_history.php'">
                                    <div class="row align-items-center g-2">
                                        <div class="col-md-3">
                                            <div class="spx-order-number">#<?php echo htmlspecialchars($order['order_number']); ?></div>
                                            <div class="spx-order-date"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                        </div>
                                        <div class="col-md-3">
                                            <?php
                                            $s = strtolower($order['order_status']);
                                            $icons = ['pending'=>'clock','processing'=>'cog','shipped'=>'truck','delivered'=>'check-circle','cancelled'=>'times-circle'];
                                            $icon = $icons[$s] ?? 'circle';
                                            ?>
                                            <span class="spx-status-badge <?php echo $s; ?>"><i class="fas fa-<?php echo $icon; ?>"></i><?php echo ucfirst($s); ?></span>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="spx-status-badge <?php echo strtolower($order['payment_status']); ?>"><?php echo ucfirst($order['payment_status']); ?></span>
                                        </div>
                                        <div class="col-md-3 text-md-end">
                                            <strong class="text-primary">RWF <?php echo number_format($order['total_amount'], 0); ?></strong>
                                        </div>
                                    </div>
                                    <!-- Status Timeline -->
                                    <div class="mt-3">
                                        <?php
                                        $steps = ['pending','processing','shipped','delivered'];
                                        $cur_idx = array_search($s, $steps);
                                        ?>
                                        <div class="spx-timeline">
                                            <?php foreach ($steps as $i => $step): ?>
                                                <div class="spx-timeline-step <?php echo $i < $cur_idx ? 'done' : ($i == $cur_idx ? 'active' : ''); ?>">
                                                    <div class="spx-timeline-dot"><i class="fas fa-<?php echo ['clock','cog','truck','check'][$i]; ?>"></i></div>
                                                    <div class="spx-timeline-label"><?php echo ucfirst($step); ?></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <?php elseif ($active_tab === 'profile'): ?>
                <!-- Profile Tab -->
                <div class="spx-panel">
                    <div class="spx-panel-header"><h5 class="spx-panel-title"><i class="fas fa-user-edit me-2 text-primary"></i>Edit Profile</h5></div>
                    <div class="spx-panel-body">
                        <?php if (!empty($update_errors)): ?>
                            <div class="alert alert-danger"><?php foreach ($update_errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?></div>
                        <?php endif; ?>
                        <?php if ($update_success): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $update_success; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="spx-input-group no-icon">
                                        <label>First Name *</label>
                                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spx-input-group no-icon">
                                        <label>Last Name *</label>
                                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spx-input-group no-icon">
                                        <label>Phone Number *</label>
                                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spx-input-group no-icon">
                                        <label>Email <small class="text-muted">(read-only)</small></label>
                                        <input type="email" value="<?php echo htmlspecialchars($customer['email']); ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="spx-input-group no-icon">
                                        <label>Delivery Address</label>
                                        <input type="text" name="address" value="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>" placeholder="Street, district, landmark">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_profile" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php elseif ($active_tab === 'password'): ?>
                <!-- Password Tab -->
                <div class="spx-panel">
                    <div class="spx-panel-header"><h5 class="spx-panel-title"><i class="fas fa-lock me-2 text-primary"></i>Change Password</h5></div>
                    <div class="spx-panel-body">
                        <?php if (!empty($password_errors)): ?>
                            <div class="alert alert-danger"><?php foreach ($password_errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?></div>
                        <?php endif; ?>
                        <?php if ($password_success): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $password_success; ?></div>
                        <?php endif; ?>
                        <form method="POST" style="max-width:480px;">
                            <div class="spx-input-group no-icon">
                                <label>Current Password *</label>
                                <input type="password" name="current_password" required>
                            </div>
                            <div class="spx-input-group no-icon">
                                <label>New Password *</label>
                                <input type="password" name="new_password" id="new_pw" required>
                                <small class="text-muted">Min 8 characters with uppercase and number</small>
                            </div>
                            <div class="spx-input-group no-icon">
                                <label>Confirm New Password *</label>
                                <input type="password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-warning px-4">
                                <i class="fas fa-key me-2"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /col-lg-9 -->
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
