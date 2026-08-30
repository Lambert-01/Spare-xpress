<?php
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start();
$_SESSION['admin'] = 'admin';
$_SESSION['admin_id'] = 1;
$_SESSION['admin_role'] = 'super_admin';
$_SESSION['admin_name'] = 'System Administrator';
include __DIR__ . '/orders/analytics.php';
