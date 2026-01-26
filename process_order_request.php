<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: pages/order_request.php');
    exit();
}

// Validate required fields
$required_fields = ['vehicle_brand', 'vehicle_model', 'part_name', 'part_category', 'full_name', 'phone_number', 'email'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
    }
}

// Validate email
if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address';
}

// Validate phone number (Rwandan format)
if (!empty($_POST['phone_number'])) {
    $phone = $_POST['phone_number'];
    if (!preg_match('/^\+?250[0-9]{9}$/', $phone)) {
        $errors[] = 'Please enter a valid Rwandan phone number (e.g., +250790123456)';
    }
}

// Check if terms are agreed
if (empty($_POST['terms_agree'])) {
    $errors[] = 'You must agree to the terms and conditions';
}

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: pages/order_request.php');
    exit();
}

// Sanitize input data
$vehicle_brand = $conn->real_escape_string($_POST['vehicle_brand']);
$vehicle_model = $conn->real_escape_string($_POST['vehicle_model']);
$year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
$chassis_number = !empty($_POST['chassis_number']) ? $conn->real_escape_string($_POST['chassis_number']) : null;
$vehicle_plate = !empty($_POST['vehicle_plate']) ? $conn->real_escape_string($_POST['vehicle_plate']) : null;
$part_name = $conn->real_escape_string($_POST['part_name']);
$part_category = $conn->real_escape_string($_POST['part_category']);
$part_description = !empty($_POST['part_description']) ? $conn->real_escape_string($_POST['part_description']) : null;
$full_name = $conn->real_escape_string($_POST['full_name']);
$phone_number = $conn->real_escape_string($_POST['phone_number']);
$email = $conn->real_escape_string($_POST['email']);
$province_district = !empty($_POST['province_district']) ? $conn->real_escape_string($_POST['province_district']) : null;
$delivery_address = !empty($_POST['delivery_address']) ? $conn->real_escape_string($_POST['delivery_address']) : null;
$order_type = $conn->real_escape_string($_POST['order_type']);

// Handle image uploads
$uploaded_images = [];
$upload_dir = 'uploads/order_images/';

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

for ($i = 1; $i <= 4; $i++) {
    if (!empty($_FILES["image_$i"]['name'])) {
        $image = $_FILES["image_$i"];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (in_array($image['type'], $allowed_types) && $image['size'] <= $max_size) {
            $filename = 'order_' . time() . "_$i." . pathinfo($image['name'], PATHINFO_EXTENSION);
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($image['tmp_name'], $filepath)) {
                $uploaded_images[] = $filename;
            }
        }
    }
}

// Insert order request into database
try {
    $query = "INSERT INTO order_requests (
        vehicle_brand, vehicle_model, year, chassis_number, vehicle_plate,
        part_name, part_category, part_description, images,
        customer_name, phone_number, email, province_district, delivery_address,
        order_type, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($query);
    $images_json = json_encode($uploaded_images);
    
    $stmt->bind_param(
        "ssissssssssssss",
        $vehicle_brand, $vehicle_model, $year, $chassis_number, $vehicle_plate,
        $part_name, $part_category, $part_description, $images_json,
        $full_name, $phone_number, $email, $province_district, $delivery_address,
        $order_type
    );
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        
        // Generate PDF confirmation
        try {
            require_once 'includes/invoice_generator.php';
            $pdfPath = generateOrderRequestPDF($order_id);
            
            // Send confirmation email with PDF
            require_once 'includes/email.php';
            $emailService = new EmailService();
            $emailSent = $emailService->sendOrderRequestConfirmation($email, $full_name, $order_id, $pdfPath);
            
            // Clean up PDF file after sending
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
            
            $emailStatus = $emailSent ? ' and confirmation email sent' : ' (email sending failed)';
        } catch (Exception $e) {
            error_log('PDF/Email generation failed: ' . $e->getMessage());
            $emailStatus = ' (PDF/email generation failed)';
        }
        
        $_SESSION['success_message'] = "Your order request has been submitted successfully! Order ID: $order_id$emailStatus. We will contact you soon.";
        header('Location: pages/order_request.php?success=1');
        exit();
    } else {
        throw new Exception("Failed to submit order request");
    }
    
} catch (Exception $e) {
    $_SESSION['form_errors'] = ['An error occurred while processing your request. Please try again.'];
    $_SESSION['form_data'] = $_POST;
    header('Location: pages/order_request.php');
    exit();
}
?>