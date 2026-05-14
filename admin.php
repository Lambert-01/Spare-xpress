<?php
/**
 * Admin Controller - Routes to appropriate admin pages
 */
session_start();

// Include necessary files
require_once __DIR__ . '/includes/session_init.php';
spx_session_start();
require_once __DIR__ . '/admin/includes/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    // Not logged in, redirect to login
    header('Location: /admin/login.php');
    exit;
}

// Logged in, show dashboard
header('Location: /admin/enhanced_dashboard.php');
exit;
?>
