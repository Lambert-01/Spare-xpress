<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>