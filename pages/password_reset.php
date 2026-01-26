<?php
// Start session check at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if already logged in
if (isset($_SESSION['customer_id'])) {
    header('Location: my_account.php');
    exit();
}

$page_title = 'Password Reset - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';

// Initialize variables
$errors = [];
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Validation
    if (empty($email)) {
        $errors[] = 'Email address is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($errors)) {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id, first_name FROM customers_enhanced WHERE email = ? AND customer_status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $customer = $result->fetch_assoc();

            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store reset token
            $update_stmt = $conn->prepare("UPDATE customers_enhanced SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $reset_token, $reset_expires, $customer['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Send reset email (simplified - in production, use proper email service)
            $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/pages/reset_password.php?token=" . $reset_token;

            // For demo purposes, show the reset link
            $success = "Password reset link has been sent to your email. For demo purposes, use this link: <a href='$reset_link'>Reset Password</a>";
        } else {
            $errors[] = 'No account found with this email address';
        }
        $stmt->close();
    }
}
?>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-key me-2"></i>
                            Reset Password
                        </h3>
                        <p class="mb-0 mt-2">Enter your email to receive reset instructions</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger d-none" id="resetErrors">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <script>document.addEventListener('DOMContentLoaded', function() { showErrorToast('Please check your email address', 'Reset Failed'); });</script>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                <small class="text-muted">Enter the email address associated with your account</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                <i class="fas fa-paper-plane me-2"></i>
                                Send Reset Instructions
                            </button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <p class="mb-0">Remember your password?
                            <a href="login.php" class="text-primary fw-bold">Login here</a>
                        </p>
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