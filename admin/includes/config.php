<?php
require_once __DIR__ . '/../../includes/env.php';

// Database Configuration for Admin
$db_config = spx_database_config();
if (!defined('DB_HOST')) {
    define('DB_HOST', $db_config['host']);
}
if (!defined('DB_PORT')) {
    define('DB_PORT', $db_config['port']);
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $db_config['name']);
}
if (!defined('DB_USER')) {
    define('DB_USER', $db_config['user']);
}
if (!defined('DB_PASS')) {
    define('DB_PASS', $db_config['pass']);
}

// Database Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT ?: null);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
