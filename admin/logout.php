<?php
require_once __DIR__ . '/../includes/session_init.php';
spx_session_start();
session_destroy();
header("Location: login.php");
exit;
?>
