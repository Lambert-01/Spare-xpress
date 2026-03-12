<?php
require_once __DIR__ . '/../../includes/session_init.php';
spx_session_start();
require_once __DIR__ . '/../../includes/logger.php';
include 'config.php';
if (!isset($_SESSION['admin'])) {
    spx_log('admin_auth_failed', [
        'path' => $_SERVER['REQUEST_URI'] ?? null,
        'has_admin' => false,
        'session_id' => session_id(),
    ]);
    header("Location: login.php");
    exit;
}
?>
