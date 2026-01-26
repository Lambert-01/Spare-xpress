<?php
// Update customer profile API endpoint
header('Content-Type: application/json');
include '../includes/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if customer is logged in
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['customer_name']) || !isset($_SESSION['customer_email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to update your profile'
    ]);
    exit;
}

// Get POST data
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        updateProfile();
        break;

    case 'change_password':
        changePassword();
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}

function updateProfile() {
    global $conn;

    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $errors = [];

    // Validation
    if (empty($full_name)) {
        $errors[] = 'Full name is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    // Check if email is already taken by another user
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $_SESSION['customer_id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'Email address is already in use';
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        return;
    }

    // Update profile
    $stmt = $conn->prepare("UPDATE customers SET full_name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $_SESSION['customer_id']);

    if ($stmt->execute()) {
        // Update session data
        $_SESSION['customer_name'] = $full_name;
        $_SESSION['customer_email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update profile. Please try again.'
        ]);
    }
    $stmt->close();
}

function changePassword() {
    global $conn;

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validation
    if (empty($current_password)) {
        $errors[] = 'Current password is required';
    }

    // Verify current password
    if (!empty($current_password)) {
        $stmt = $conn->prepare("SELECT password FROM customers WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['customer_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $customer = $result->fetch_assoc();
            if (!password_verify($current_password, $customer['password'])) {
                $errors[] = 'Current password is incorrect';
            }
        } else {
            $errors[] = 'Customer not found';
        }
        $stmt->close();
    }

    if (empty($new_password)) {
        $errors[] = 'New password is required';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters long';
    }

    if ($new_password !== $confirm_password) {
        $errors[] = 'New passwords do not match';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        return;
    }

    // Update password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE customers SET password = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $_SESSION['customer_id']);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to change password. Please try again.'
        ]);
    }
    $stmt->close();
}
?>