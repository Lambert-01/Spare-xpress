<?php
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start(['secure' => false]);

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to homepage
header("Location: ../index.php");
exit();
?>
