<?php
// Start session check at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if already logged in (before HTML output)
if (isset($_SESSION['customer_id'])) {
    header('Location: ../index.php');
    exit();
}

$page_title = 'Register - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';

// Initialize variables
$errors = [];
$success = '';
$full_name = $email = $phone = $address = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Production Validation
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    } elseif (strlen($full_name) < 2) {
        $errors[] = 'Full name must be at least 2 characters long';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^\+250[0-9]{9}$/', $phone)) {
        $errors[] = 'Please enter a valid Rwandan phone number (+250XXXXXXXXX)';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM customers_enhanced WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'Email address already exists';
        }
        $stmt->close();
    }

    // If no errors, create account
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Split full name into first and last name
        $name_parts = explode(' ', $full_name, 2);
        $first_name = $name_parts[0] ?? '';
        $last_name = $name_parts[1] ?? '';

        $stmt = $conn->prepare("INSERT INTO customers_enhanced (customer_number, first_name, last_name, email, phone, password, address_line1, customer_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
        $customer_number = 'CUST-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $stmt->bind_param("sssssss", $customer_number, $first_name, $last_name, $email, $phone, $hashed_password, $address);

        if ($stmt->execute()) {
            $customer_id = $conn->insert_id; // Get the newly created customer ID

            // Create notification for admin
            $notification_stmt = $conn->prepare("INSERT INTO notifications (user_id, type, reference_id, is_read) VALUES (?, 'system', ?, 0)");
            $notification_stmt->bind_param("ii", $customer_id, $customer_id);
            $notification_stmt->execute();
            $notification_stmt->close();

            $success = 'Account created successfully! You can now login.';
            // Clear form
            $full_name = $email = $phone = $address = '';
        } else {
            $errors[] = 'Failed to create account. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!-- Registration Section Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-lg border-0 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>
                            Create Your Account
                        </h3>
                        <p class="mb-0 mt-2">Join SPARE XPRESS LTD for easy auto parts ordering</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger d-none" id="registerErrors">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <script>document.addEventListener('DOMContentLoaded', function() { showErrorToast('Please check the form errors above', 'Registration Failed'); });</script>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <script>document.addEventListener('DOMContentLoaded', function() { showSuccessToast('<?php echo addslashes($success); ?>', 'Account Created'); });</script>
                        <?php endif; ?>

                        <form method="POST" action="" novalidate>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="full_name" class="form-label">Full Name *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                        <input type="text" class="form-control" id="full_name" name="full_name"
                                               value="<?php echo htmlspecialchars($full_name); ?>" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="phone" class="form-label">Phone Number *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                               value="<?php echo htmlspecialchars($phone); ?>" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                        <textarea class="form-control" id="address" name="address" rows="3"
                                                  placeholder="Your delivery address"><?php echo htmlspecialchars($address); ?></textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="password" class="form-label">Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <small class="text-muted">Minimum 8 characters with uppercase, lowercase, and number</small>
                                        <span id="password-strength" class="badge bg-secondary small"></span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="confirm_password" class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-3">
                                        <i class="fas fa-user-plus me-2"></i>
                                        Create Account
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <p class="mb-0">Already have an account?
                            <a href="login.php" class="text-primary fw-bold">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Registration Section End -->

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
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
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