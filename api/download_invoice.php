<?php
// Download invoice API endpoint
include '../includes/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if customer is logged in
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['customer_name']) || !isset($_SESSION['customer_email'])) {
    die('Please log in to download invoices');
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id <= 0) {
    die('Invalid order ID');
}

// Verify that the order belongs to the current customer
$order_stmt = $conn->prepare("
    SELECT o.*,
           c.full_name,
           c.email,
           c.phone,
           c.address
    FROM orders_enhanced o
    LEFT JOIN customers_enhanced c ON o.customer_id = c.id
    WHERE o.id = ? AND o.customer_id = ?
");
$order_stmt->bind_param("ii", $order_id, $_SESSION['customer_id']);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    die('Order not found or access denied');
}

$order = $order_result->fetch_assoc();
$order_stmt->close();

// Get order items
$items_stmt = $conn->prepare("
    SELECT oi.*
    FROM order_items oi
    WHERE oi.order_id = ?
    ORDER BY oi.id
");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

$items = [];
$total = 0;
while ($item = $items_result->fetch_assoc()) {
    $subtotal = $item['quantity'] * $item['price'];
    $total += $subtotal;
    $items[] = $item;
}
$items_stmt->close();

// Generate PDF invoice (simple HTML version for now)
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="invoice_' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . '.html"');

// Simple HTML invoice
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?> - SPARE XPRESS LTD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .customer-info {
            clear: both;
            margin-bottom: 30px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-section {
            text-align: right;
            margin-bottom: 30px;
        }
        .total-row {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SPARE XPRESS LTD</h1>
        <p>Kagarama, Kicukiro, Kigali, Rwanda</p>
        <p>Phone: +250 792 865 114 | Email: support@sparexpress.rw</p>
    </div>

    <div class="invoice-info">
        <h2>INVOICE</h2>
        <p><strong>Invoice Number:</strong> #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
        <p><strong>Invoice Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
        <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
    </div>

    <div class="customer-info">
        <h3>Bill To:</h3>
        <p><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
        <p><?php echo htmlspecialchars($order['email']); ?></p>
        <?php if ($order['phone']): ?>
            <p><?php echo htmlspecialchars($order['phone']); ?></p>
        <?php endif; ?>
        <?php if ($order['address']): ?>
            <p><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
        <?php endif; ?>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Brand/Model</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo htmlspecialchars(($item['product_brand'] ?? '') . ' ' . ($item['product_model'] ?? '')); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>RWF <?php echo number_format($item['unit_price'], 0, '.', ','); ?></td>
                    <td>RWF <?php echo number_format($item['subtotal'], 0, '.', ','); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-section">
        <p><strong>Subtotal: RWF <?php echo number_format($total, 0, '.', ','); ?></strong></p>
        <p><strong>Tax: RWF 0.00</strong></p>
        <p class="total-row"><strong>Total: RWF <?php echo number_format($total, 0, '.', ','); ?></strong></p>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Payment Status: <?php echo ucfirst($order['payment_status']); ?> | Order Status: <?php echo ucfirst($order['order_status']); ?></p>
        <p>This invoice was generated on <?php echo date('F j, Y \a\t g:i A'); ?></p>
    </div>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>