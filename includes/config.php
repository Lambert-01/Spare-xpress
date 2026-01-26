<?php
// Site Configuration
if (!defined('SITE_NAME')) define('SITE_NAME', 'SPARE XPRESS LTD');
if (!defined('SITE_URL')) define('SITE_URL', 'https://sparexpress.rw');
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', 'support@sparexpress.rw');
if (!defined('SITE_PHONE')) define('SITE_PHONE', '+250 792 865 114');
if (!defined('SITE_ADDRESS')) define('SITE_ADDRESS', 'Kagarama, Kicukiro, Kigali, Rwanda');

// Database Configuration
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'sparedb');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Email Configuration
if (!defined('SMTP_HOST')) define('SMTP_HOST', 'smtp.gmail.com');
if (!defined('SMTP_PORT')) define('SMTP_PORT', 587);
if (!defined('SMTP_USER')) define('SMTP_USER', 'nlambert833@gmail.com');
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'ytvjsswknjlrnfgf'); // Gmail app password (spaces removed)
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', 'nlambert833@gmail.com');
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', 'SPARE XPRESS LTD');

// Session Configuration - Initialize only once
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters for cross-page persistence
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie
        'path' => '/',
        'domain' => '', // Leave empty for current domain
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true, // Prevent JavaScript access
        'samesite' => 'Lax' // CSRF protection
    ]);
    session_start();
}

// Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch Brands from Database
$brands = [];
try {
    $brands_query = "SELECT brand_name as name, slug, logo_image, brand_image FROM vehicle_brands_enhanced WHERE is_active = 1 ORDER BY display_order, brand_name";
    $brands_result = $conn->query($brands_query);
    if ($brands_result && $brands_result->num_rows > 0) {
        while ($row = $brands_result->fetch_assoc()) {
            $brands[] = $row;
        }
    }
} catch (Exception $e) {
    // Fallback to empty array if database query fails
    $brands = [];
}

// Fetch Categories from Database
$categories = [];
try {
    $categories_query = "SELECT category_name as name, slug, icon_class as icon FROM categories_enhanced WHERE is_active = 1 ORDER BY display_order, category_name";
    $categories_result = $conn->query($categories_query);
    if ($categories_result && $categories_result->num_rows > 0) {
        while ($row = $categories_result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
} catch (Exception $e) {
    // Fallback to empty array if database query fails
    $categories = [];
}

// Navigation Menu
$nav_menu = [
    ['url' => '/index.php', 'text' => 'Home'],
    ['url' => '/pages/brands.php', 'text' => 'Brands'],
    ['url' => '/pages/shop.php', 'text' => 'Stock Catalog'],
    ['url' => '/pages/order_request.php', 'text' => 'Special Orders'],
    ['url' => '/pages/contact.php', 'text' => 'Contact']
];
?>