<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once __DIR__ . '/admin/includes/config.php';
require_once __DIR__ . '/admin/includes/functions.php';
require_once __DIR__ . '/includes/invoice_generator.php';
$id = isset($argv[1]) ? (int)$argv[1] : 8;
try {
    $path = generateOrderInvoice($id);
    echo "OK path=" . $path . " size=" . (file_exists($path) ? filesize($path) : 'N/A') . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
