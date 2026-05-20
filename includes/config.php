<?php
require_once __DIR__ . '/env.php';

// Site Configuration
if (!defined('SITE_NAME')) define('SITE_NAME', spx_env('SITE_NAME', 'SPARE XPRESS LTD'));
if (!defined('SITE_URL')) define('SITE_URL', spx_env('SITE_URL', 'https://sparexpress.rw'));
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', spx_env('SITE_EMAIL', 'sparexpressltd@gmail.com'));
if (!defined('SITE_PHONE')) define('SITE_PHONE', spx_env('SITE_PHONE', '+250 792 865 114'));
if (!defined('SITE_ADDRESS')) define('SITE_ADDRESS', spx_env('SITE_ADDRESS', 'Kagarama, Kicukiro, Kigali, Rwanda'));

// Database Configuration
$db_config = spx_database_config();
if (!defined('DB_HOST')) define('DB_HOST', $db_config['host']);
if (!defined('DB_PORT')) define('DB_PORT', $db_config['port']);
if (!defined('DB_NAME')) define('DB_NAME', $db_config['name']);
if (!defined('DB_USER')) define('DB_USER', $db_config['user']);
if (!defined('DB_PASS')) define('DB_PASS', $db_config['pass']);

// Email Configuration
if (!defined('SMTP_HOST')) define('SMTP_HOST', spx_env('SMTP_HOST', 'smtp.gmail.com'));
if (!defined('SMTP_PORT')) define('SMTP_PORT', (int) spx_env('SMTP_PORT', 587));
$smtp_user = spx_env('SMTP_USER', 'sparexpressltd@gmail.com') ?: 'sparexpressltd@gmail.com';
$smtp_from_email = spx_env('SMTP_FROM_EMAIL', $smtp_user) ?: $smtp_user;
if (!defined('SMTP_USER')) define('SMTP_USER', $smtp_user);
if (!defined('SMTP_PASS')) define('SMTP_PASS', spx_env('SMTP_PASS', ''));
if (!defined('SMTP_FROM_EMAIL')) define('SMTP_FROM_EMAIL', $smtp_from_email);
if (!defined('SMTP_FROM_NAME')) define('SMTP_FROM_NAME', spx_env('SMTP_FROM_NAME', SITE_NAME));

// Google OAuth Configuration
if (!defined('GOOGLE_CLIENT_ID')) define('GOOGLE_CLIENT_ID', spx_env('GOOGLE_CLIENT_ID', ''));
if (!defined('GOOGLE_CLIENT_SECRET')) define('GOOGLE_CLIENT_SECRET', spx_env('GOOGLE_CLIENT_SECRET', ''));
if (!defined('GOOGLE_REDIRECT_URI')) define('GOOGLE_REDIRECT_URI', spx_env('GOOGLE_REDIRECT_URI', ''));

// Session Configuration - Initialize only once (avoid emitting warnings into JSON responses)
require_once __DIR__ . '/session_init.php';
spx_session_start([
    'secure' => false, // Set to true if using HTTPS
]);

// Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT ?: null);
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
    ['url' => '/pages/shop.php', 'text' => 'In stock'],
    ['url' => '/pages/order_request.php', 'text' => 'Special Orders'],
    ['url' => '/pages/contact.php', 'text' => 'Contact']
];
?>
