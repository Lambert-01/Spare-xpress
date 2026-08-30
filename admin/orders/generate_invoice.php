<?php
// SPARE XPRESS LTD - Invoice Generator
// Generates PDF invoices for orders and previews the order if generation fails

include '../includes/auth.php';
include '../includes/functions.php';
include '../../includes/invoice_generator.php';

// Get order ID
$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    die('Invalid order ID');
}

// Load order for preview / error page
$order = null;
$order_query = $conn->query("SELECT * FROM orders_enhanced WHERE id = " . intval($order_id));
if ($order_query && $order_query->num_rows) {
    $order = $order_query->fetch_assoc();
}

$error = null;
try {
    // Generate the invoice PDF
    $pdf_path = generateOrderInvoice($order_id);

    // Output the PDF inline (opens in browser)
    if (file_exists($pdf_path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($pdf_path) . '"');
        header('Content-Length: ' . filesize($pdf_path));
        readfile($pdf_path);
        unlink($pdf_path);
        exit;
    } else {
        $error = 'The PDF file was not created. Please check server permissions and that TCPDF is installed.';
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Generation - SPARE XPRESS LTD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 760px;">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Invoice Could Not Be Generated</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">An error occurred while generating the PDF invoice.</p>

                <?php if ($order): ?>
                    <h6 class="fw-bold">Order: <?php echo htmlspecialchars($order['order_number']); ?></h6>
                    <p class="mb-1">
                        Status: <span class="badge bg-<?php echo match($order['order_status']) {
                            'pending' => 'warning', 'processing' => 'info', 'shipped' => 'primary',
                            'delivered' => 'success', 'cancelled' => 'danger', default => 'secondary'
                        }; ?> text-capitalize"><?php echo htmlspecialchars($order['order_status']); ?></span>
                        &nbsp;•&nbsp; Total: <strong>RWF <?php echo number_format($order['total_amount'] ?? 0, 0); ?></strong>
                    </p>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-secondary mt-3 small">
                        <strong>Details:</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                    <p class="small text-muted">
                        If this persists, verify that the <code>tecnickcom/tcpdf</code> package is installed
                        (<code>vendor/tecnickcom/tcpdf/tcpdf.php</code>) and that the server has write
                        permission to its temp directory.
                    </p>
                <?php endif; ?>

                <?php if ($order): ?>
                    <h6 class="fw-bold mt-4">Order Items</h6>
                    <?php
                    $items = $conn->query("SELECT * FROM order_items_enhanced WHERE order_id = " . intval($order_id));
                    if ($items && $items->num_rows): ?>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Subtotal</th></tr>
                            </thead>
                            <tbody>
                            <?php while ($it = $items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($it['product_name']); ?></td>
                                    <td class="text-center"><?php echo (int)$it['quantity']; ?></td>
                                    <td class="text-end">RWF <?php echo number_format($it['unit_price'] ?? 0, 0); ?></td>
                                    <td class="text-end">RWF <?php echo number_format($it['subtotal'] ?? 0, 0); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted small">No items found for this order.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">Order #<?php echo $order_id; ?> was not found in the system.</div>
                <?php endif; ?>

                <div class="d-flex gap-2 mt-4">
                    <a href="view_order.php?id=<?php echo $order_id; ?>" class="btn btn-primary">
                        <i class="bi bi-eye me-1"></i>View Order
                    </a>
                    <a href="enhanced_order_management.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Orders
                    </a>
                    <button class="btn btn-outline-danger" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Retry
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
