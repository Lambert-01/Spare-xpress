<?php
// SPARE XPRESS LTD - Invoice Generator
// Generates PDF invoices for orders

include '../includes/auth.php';
include '../includes/functions.php';
include '../../includes/invoice_generator.php';

// Get order ID
$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    die('Invalid order ID');
}

try {
    // Generate the invoice PDF
    $pdf_path = generateOrderInvoice($order_id);

    // Output the PDF inline (opens in browser)
    if (file_exists($pdf_path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($pdf_path) . '"');
        header('Content-Length: ' . filesize($pdf_path));
        readfile($pdf_path);

        // Clean up the temporary file
        unlink($pdf_path);
        exit;
    } else {
        die('Failed to generate invoice PDF');
    }
} catch (Exception $e) {
    die('Error generating invoice: ' . $e->getMessage());
}
?>