<?php
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start(['secure' => false]);
require_once __DIR__ . '/../includes/google_auth.php';

if (isset($_SESSION['customer_id'])) { header('Location: ../index.php'); exit(); }

$page_title = 'Register - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/toast_notifications.php';

$errors = [];
$success = '';
$full_name = $email = $phone = $address = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || strlen($full_name) < 2) $errors[] = 'Full name is required (min 2 characters)';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email address is required';
    if (empty($phone) || !preg_match('/^\+250[0-9]{9}$/', $phone)) $errors[] = 'Valid Rwandan phone required (+250XXXXXXXXX)';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters';
    elseif (!preg_match('/[A-Z]/', $password)) $errors[] = 'Password needs an uppercase letter';
    elseif (!preg_match('/[0-9]/', $password)) $errors[] = 'Password needs a number';
    if ($password !== $confirm) $errors[] = 'Passwords do not match';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM customers_enhanced WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) $errors[] = 'Email address already registered';
        $stmt->close();
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $parts = explode(' ', $full_name, 2);
        $first = $parts[0]; $last = $parts[1] ?? '';
        $cnum = 'CUST-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("INSERT INTO customers_enhanced (customer_number, first_name, last_name, email, phone, password, address_line1, customer_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param("sssssss", $cnum, $first, $last, $email, $phone, $hashed, $address);
        if ($stmt->execute()) {
            $success = 'Account created! You can now sign in.';
            $full_name = $email = $phone = $address = '';
        } else {
            $errors[] = 'Failed to create account. Please try again.';
        }
        $stmt->close();
    }
}
?>

<div class="spx-auth-wrap">
    <!-- Brand Side -->
    <div class="spx-auth-brand d-none d-lg-flex" style="width:42%;flex-shrink:0;">
        <div class="position-relative text-center">
            <img src="/img/logo/logox.jpg" alt="SPARE XPRESS" class="spx-auth-brand-logo">
            <h2>Join SPARE XPRESS</h2>
            <p>Create your free account and start ordering genuine auto parts today</p>
            <ul class="spx-auth-brand-features text-start">
                <li><i class="fas fa-history"></i>Track all your orders in one place</li>
                <li><i class="fas fa-comments"></i>Direct messaging with our team</li>
                <li><i class="fas fa-bell"></i>Get price quotes &amp; order updates</li>
                <li><i class="fas fa-tag"></i>Exclusive member pricing</li>
                <li><i class="fas fa-shield-alt"></i>Secure &amp; private account</li>
            </ul>
        </div>
    </div>

    <!-- Form Side -->
    <div class="spx-auth-form-side" style="overflow-y:auto;">
        <div class="spx-auth-form-inner" style="max-width:460px;">
            <div class="d-lg-none text-center mb-4">
                <img src="/img/logo/logox.jpg" alt="Logo" style="height:56px;border-radius:.75rem;">
            </div>
            <h3>Create your account</h3>
            <p class="subtitle">Free registration — takes less than a minute</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger py-2">
                    <?php foreach ($errors as $e): ?><div><i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?> <a href="login.php" class="fw-bold">Sign in now &rarr;</a></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="spx-input-group">
                    <label>Full Name *</label>
                    <i class="fas fa-user spx-input-icon"></i>
                    <input type="text" name="full_name" placeholder="Your full name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>
                <div class="spx-input-group">
                    <label>Email Address *</label>
                    <i class="fas fa-envelope spx-input-icon"></i>
                    <input type="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="spx-input-group">
                    <label>Phone Number * <small class="text-muted fw-normal">(+250XXXXXXXXX)</small></label>
                    <i class="fas fa-phone spx-input-icon"></i>
                    <input type="tel" name="phone" placeholder="+250790123456" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                <div class="spx-input-group no-icon">
                    <label>Delivery Address <small class="text-muted fw-normal">(optional)</small></label>
                    <textarea name="address" rows="2" placeholder="Street, district, landmark..."><?php echo htmlspecialchars($address); ?></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="spx-input-group">
                            <label>Password *</label>
                            <i class="fas fa-lock spx-input-icon"></i>
                            <input type="password" id="reg_password" name="password" placeholder="Min 8 chars" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="spx-input-group">
                            <label>Confirm Password *</label>
                            <i class="fas fa-lock spx-input-icon"></i>
                            <input type="password" name="confirm_password" placeholder="Repeat password" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Password strength</small>
                        <small id="strength-label" class="fw-600"></small>
                    </div>
                    <div style="height:4px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0;transition:width .3s,background .3s;border-radius:999px;"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>

            <?php if (spx_google_enabled()): ?>
                <div class="spx-divider">or</div>
                <a href="<?php echo htmlspecialchars(spx_google_auth_url('register', '../index.php')); ?>" class="btn btn-outline-danger w-100 py-3">
                    <i class="fab fa-google me-2"></i>Sign up with Google
                </a>
            <?php endif; ?>

            <p class="text-center text-muted mt-4 mb-0" style="font-size:.875rem;">
                Already have an account? <a href="login.php" class="text-primary fw-600">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script>
const pw = document.getElementById('reg_password');
const bar = document.getElementById('strength-bar');
const lbl = document.getElementById('strength-label');
if (pw) pw.addEventListener('input', function() {
    const v = this.value;
    let s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[a-z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
    const labels = ['Very Weak','Weak','Fair','Good','Strong'];
    bar.style.width = (s * 20) + '%';
    bar.style.background = colors[s-1] || '#e5e7eb';
    lbl.textContent = labels[s-1] || '';
    lbl.style.color = colors[s-1] || '#9ca3af';
});
</script>

<?php include '../includes/footer.php'; ?>
