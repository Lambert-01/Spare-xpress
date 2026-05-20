<?php
include_once '../includes/config.php';
require_once __DIR__ . '/../includes/google_auth.php';

if (isset($_GET['logout'])) { session_destroy(); header('Location: login.php'); exit(); }
if (isset($_SESSION['customer_id'])) { header('Location: ../index.php'); exit(); }

$errors = [];
$email = '';

if (!empty($_SESSION['google_auth_error'])) {
    $errors[] = $_SESSION['google_auth_error'];
    unset($_SESSION['google_auth_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) $errors[] = 'Email is required';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address';
    if (empty($password)) $errors[] = 'Password is required';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, phone, password FROM customers_enhanced WHERE email = ? AND customer_status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $customer = $result->fetch_assoc();
            if (password_verify($password, $customer['password'])) {
                session_regenerate_id(true);
                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_name'] = $customer['full_name'];
                $_SESSION['customer_email'] = $customer['email'];
                $_SESSION['customer_phone'] = $customer['phone'];
                $_SESSION['login_time'] = time();
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $update_stmt = $conn->prepare("UPDATE customers_enhanced SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $customer['id']);
                $update_stmt->execute();
                $update_stmt->close();
                $redirect = $_GET['redirect'] ?? '../index.php';
                header("Location: $redirect");
                exit();
            } else {
                $errors[] = 'Invalid email or password';
            }
        } else {
            $errors[] = 'Invalid email or password';
        }
        $stmt->close();
    }
}

$page_title = 'Login - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/toast_notifications.php';
?>

<div class="spx-auth-wrap">
    <!-- Brand Side -->
    <div class="spx-auth-brand d-none d-lg-flex" style="width:42%;flex-shrink:0;">
        <div class="position-relative text-center">
            <img src="/img/logo/logox.jpg" alt="SPARE XPRESS" class="spx-auth-brand-logo">
            <h2>SPARE XPRESS LTD</h2>
            <p>Rwanda's trusted source for genuine vehicle spare parts</p>
            <ul class="spx-auth-brand-features text-start">
                <li><i class="fas fa-check"></i>In-stock parts — buy directly</li>
                <li><i class="fas fa-globe"></i>Global sourcing: Japan, Dubai, Europe, China</li>
                <li><i class="fas fa-truck"></i>Delivery across Rwanda</li>
                <li><i class="fas fa-shield-alt"></i>Genuine parts with warranty</li>
                <li><i class="fas fa-headset"></i>Expert support team</li>
            </ul>
        </div>
    </div>

    <!-- Form Side -->
    <div class="spx-auth-form-side">
        <div class="spx-auth-form-inner">
            <div class="d-lg-none text-center mb-4">
                <img src="/img/logo/logox.jpg" alt="Logo" style="height:56px;border-radius:.75rem;">
            </div>
            <h3>Welcome back</h3>
            <p class="subtitle">Sign in to your SPARE XPRESS account</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger py-2">
                    <?php foreach ($errors as $e): ?><div><i class="fas fa-exclamation-circle me-1"></i><?php echo htmlspecialchars($e); ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="spx-input-group">
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope spx-input-icon"></i>
                    <input type="email" id="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="spx-input-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock spx-input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Your password" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="d-flex align-items-center gap-2 mb-0" style="cursor:pointer;font-size:.85rem;">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="password_reset.php" class="text-primary text-decoration-none" style="font-size:.85rem;">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>

            <?php if (spx_google_enabled()): ?>
                <div class="spx-divider">or</div>
                <a href="<?php echo htmlspecialchars(spx_google_auth_url('login', $_GET['redirect'] ?? '../index.php')); ?>" class="btn btn-outline-danger w-100 py-3">
                    <i class="fab fa-google me-2"></i>Continue with Google
                </a>
            <?php endif; ?>

            <p class="text-center text-muted mt-4 mb-0" style="font-size:.875rem;">
                Don't have an account? <a href="register.php" class="text-primary fw-600">Create one free</a>
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
