<?php
// Handle all session and redirect logic BEFORE any HTML output
include_once '../includes/config.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

// Check if already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}

// Initialize variables
$errors = [];
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    // If no validation errors, attempt login
    if (empty($errors)) {
        include '../includes/config.php';

        $stmt = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, phone, password FROM customers_enhanced WHERE email = ? AND customer_status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $customer = $result->fetch_assoc();

            if (password_verify($password, $customer['password'])) {
                // Secure session management
                session_regenerate_id(true); // Prevent session fixation

                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_name'] = $customer['full_name'];
                $_SESSION['customer_email'] = $customer['email'];
                $_SESSION['customer_phone'] = $customer['phone'];
                $_SESSION['login_time'] = time();
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                // Update last login
                $update_stmt = $conn->prepare("UPDATE customers_enhanced SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $customer['id']);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect to homepage or intended page
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
        $conn->close();
    }
}

// Now include HTML files after all session logic is complete
$page_title = 'Login - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';
?>

<!-- Login Section Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Customer Login
                        </h3>
                        <p class="mb-0 mt-2">Access your SPARE XPRESS LTD account</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger d-none" id="loginErrors">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <script>document.addEventListener('DOMContentLoaded', function() { showErrorToast('Please check your login credentials', 'Login Failed'); });</script>
                        <?php endif; ?>

                        <form method="POST" action="" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                <a href="password_reset.php" class="text-decoration-none">Forgot password?</a>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login to Account
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <p class="mb-0">Don't have an account?
                            <a href="register.php" class="text-primary fw-bold">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Login Section End -->

<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
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
</style>

<?php include '../includes/footer.php'; ?>