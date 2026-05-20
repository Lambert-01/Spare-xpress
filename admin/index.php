<?php
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start();

if (!isset($_SESSION['admin'])) {
    header('Location: /admin/login.php');
    exit;
}

header('Location: /admin/enhanced_dashboard.php');
exit;
?>
