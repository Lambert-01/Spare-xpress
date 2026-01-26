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

$page_title = 'Reset Password - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';

// Initialize variables
$errors = [];
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: password_reset.php');
    exit();
}

// Verify token
$stmt = $conn->prepare("SELECT id, first_name, reset_expires FROM customers_enhanced WHERE reset_token = ? AND customer_status = 'active'");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $errors[] = 'Invalid or expired reset token';
} else {
    $customer = $result->fetch_assoc();

    // Check if token is expired
    if (strtotime($customer['reset_expires']) < time()) {
        $errors[] = 'Reset token has expired. Please request a new one.';
    }
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($new_password) || strlen($new_password) < 8) {
        $errors[] = 'New password must be at least 8 characters long';
    } elseif (!preg_match('/[A-Z]/', $new_password)) {
        $errors[] = 'New password must contain at least one uppercase letter';
    } elseif (!preg_match('/[a-z]/', $new_password)) {
        $errors[] = 'New password must contain at least one lowercase letter';
    } elseif (!preg_match('/[0-9]/', $new_password)) {
        $errors[] = 'New password must contain at least one number';
    }

    if ($new_password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password and clear reset token
        $update_stmt = $conn->prepare("UPDATE customers_enhanced SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $customer['id']);

        if ($update_stmt->execute()) {
            $success = 'Password has been reset successfully. You can now login with your new password.';
        } else {
            $errors[] = 'Failed to reset password. Please try again.';
        }
        $update_stmt->close();
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
                            Set New Password
                        </h3>
                        <p class="mb-0 mt-2">Enter your new password below</p>
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
                            <script>document.addEventListener('DOMContentLoaded', function() { showErrorToast('Please check the password requirements', 'Reset Failed'); });</script>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                                <br><br>
                                <a href="login.php" class="btn btn-primary">Login Now</a>
                            </div>
                        <?php elseif (empty($errors)): ?>
                            <form method="POST" action="" novalidate>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">Minimum 8 characters with uppercase, lowercase, and number</small>
                                        <span id="password-strength" class="badge bg-secondary small"></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 mb-3">
                                    <i class="fas fa-save me-2"></i>
                                    Reset Password
                                </button>
                            </form>
                        <?php endif; ?>
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

<script>
// Password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordInput = document.getElementById('new_password');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            updatePasswordStrengthIndicator(strength);
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
</script>

<?php include '../includes/footer.php'; ?>