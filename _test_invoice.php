<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once __DIR__ . '/admin/includes/config.php';
require_once __DIR__ . '/admin/includes/functions.php';
require_once __DIR__ . '/includes/invoice_generator.php';
try {
    $path = generateOrderInvoice(8);
    $out = sys_get_temp_dir() . '/structured_invoice_test.pdf';
    copy($path, $out);
    echo "OK path=" . $path . " size=" . filesize($path) . " copied=" . $out . "\n";
} catch (Throwable $e) {
    echo "ERROR: " . get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
