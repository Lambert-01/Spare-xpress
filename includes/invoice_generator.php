<?php
// SPARE XPRESS LTD - Invoice Generator Function
// Can be called programmatically to generate PDF invoices

$composerTcpdf = __DIR__ . '/../vendor/tecnickcom/tcpdf/tcpdf.php';
if (is_readable($composerTcpdf)) {
    require_once $composerTcpdf;
} else {
    require_once __DIR__ . '/../lib/tcpdf/tcpdf.php';
}

function generateOrderInvoice($order_id) {
    global $conn;

    // Fetch order details
    $order_query = "SELECT o.*, c.first_name, c.last_name, c.email as customer_email, c.phone as customer_phone
                     FROM orders_enhanced o
                     LEFT JOIN customers_enhanced c ON o.customer_id = c.id
                     WHERE o.id = ?";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Fetch order items
    $items_query = "SELECT oi.*, p.product_name as original_name
                    FROM order_items_enhanced oi
                    LEFT JOIN products_enhanced p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                    ORDER BY oi.id";
    $items_stmt = $conn->prepare($items_query);
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $order_items = $items_stmt->get_result();

    // Create PDF
    $pdf = new InvoicePDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->order_number = $order['order_number'];

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SPARE XPRESS LTD');
    $pdf->SetTitle('Invoice - ' . $order['order_number']);
    $pdf->SetSubject('Order Invoice');

    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins (top margin below the branded header band so body starts cleanly under it)
    $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add page
    $pdf->AddPage();

    // Customer details
    $pdf->CustomerDetails($order);

    // Order items
    $pdf->OrderItems($order_items);

    // Order summary
    $pdf->OrderSummary($order);

    // Payment info
    $pdf->PaymentInfo($order);

    // Generate unique filename
    $filename = 'invoice_' . $order['order_number'] . '_' . time() . '.pdf';
    $filepath = sys_get_temp_dir() . '/' . $filename;

    // Output PDF to file
    $pdf->Output($filepath, 'F');

    return $filepath;
}

function generateOrderRequestPDF($order_request_id) {
    global $conn;

    // Fetch order request details
    $order_query = "SELECT * FROM order_requests WHERE id = ?";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("i", $order_request_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        throw new Exception('Order request not found');
    }

    // Create PDF
    $pdf = new OrderRequestPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->order_request_id = $order['id'];

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SPARE XPRESS LTD');
    $pdf->SetTitle('Order Request Confirmation - ' . $order['id']);
    $pdf->SetSubject('Special Order Request Confirmation');

    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins (top margin below the branded header band so body starts cleanly under it)
    $pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add page
    $pdf->AddPage();

    // Customer details
    $pdf->CustomerDetails($order);

    // Vehicle details
    $pdf->VehicleDetails($order);

    // Part details
    $pdf->PartDetails($order);

    // Order request info
    $pdf->OrderRequestInfo($order);

    // Generate unique filename
    $filename = 'order_request_' . $order['id'] . '_' . time() . '.pdf';
    $filepath = sys_get_temp_dir() . '/' . $filename;

    // Output PDF to file
    $pdf->Output($filepath, 'F');

    return $filepath;
}

// PDF Class for Order Requests using TCPDF
class OrderRequestPDF extends TCPDF {
    public $order_request_id = '';

    // Page header - Clean and minimal design
    public function Header() {
        // Subtle top border
        $this->SetFillColor(248, 250, 252);
        $this->Rect(0, 0, 216, 35, 'F');

        // Thin accent line
        $this->SetFillColor(59, 130, 246);
        $this->Rect(0, 33, 216, 2, 'F');

        // Logo
        $logoPath = __DIR__ . '/../img/logo/logox.jpg';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 8, 25);
        }

        // Company Info - Elegant typography
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor(31, 41, 55);
        $this->SetXY(45, 8);
        $this->Cell(0, 8, 'SPARE XPRESS LTD', 0, 1);

        $this->SetFont('helvetica', 'I', 11);
        $this->SetTextColor(107, 114, 128);
        $this->SetX(45);
        $this->Cell(0, 6, 'Your Trusted Auto Parts Store', 0, 1);

        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(75, 85, 99);
        $this->SetX(45);
        $this->Cell(0, 5, 'Kagarama, Kicukiro, Kigali, Rwanda', 0, 1);
        $this->SetX(45);
        $this->Cell(0, 5, '+250 792 865 114 | support@sparexpress.rw', 0, 1);

        // Clean document title
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(59, 130, 246);
        $this->SetXY(140, 8);
        $this->Cell(60, 8, 'ORDER REQUEST', 0, 1, 'R');

        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(107, 114, 128);
        $this->SetXY(140, 18);
        $this->Cell(60, 5, 'Request #' . $this->order_request_id, 0, 1, 'R');
        $this->SetXY(140, 24);
        $this->Cell(60, 5, date('M d, Y H:i'), 0, 1, 'R');

        $this->Ln(15);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-30);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(107, 114, 128);

        // Clean terms section
        $this->Cell(0, 4, 'Terms & Conditions: Special order request pending verification • 50% deposit required • 2-4 weeks delivery (1-2 weeks urgent)', 0, 1, 'C');

        // Thank you message
        $this->SetY(-15);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColor(59, 130, 246);
        $this->Cell(0, 5, 'Thank you for choosing SPARE XPRESS LTD!', 0, 0, 'C');

        // Page number
        $this->SetY(-10);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 5, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    function CustomerDetails($order) {
        // Clean section header
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 8, 'Customer Information', 0, 1);
        $this->Ln(2);

        // Elegant content layout
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);

        $this->Cell(50, 6, 'Name:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, $order['customer_name'], 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);
        $this->Cell(50, 6, 'Email:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, $order['email'], 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);
        $this->Cell(50, 6, 'Phone:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, $order['phone_number'], 0, 1, 'L');

        if ($order['province_district']) {
            $this->SetFont('helvetica', '', 11);
            $this->SetTextColor(75, 85, 99);
            $this->Cell(50, 6, 'Province/District:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->SetTextColor(31, 41, 55);
            $this->Cell(0, 6, $order['province_district'], 0, 1, 'L');
        }

        if ($order['delivery_address']) {
            $this->SetFont('helvetica', '', 11);
            $this->SetTextColor(75, 85, 99);
            $this->Cell(50, 6, 'Delivery Address:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->SetTextColor(31, 41, 55);
            $this->MultiCell(120, 6, $order['delivery_address'], 0, 'L');
        }

        $this->Ln(8);
    }

    function VehicleDetails($order) {
        // Clean section header
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 8, 'Vehicle Information', 0, 1);
        $this->Ln(2);

        // Elegant content layout
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);

        $this->Cell(50, 6, 'Brand:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, ucfirst($order['vehicle_brand']), 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);
        $this->Cell(50, 6, 'Model:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, $order['vehicle_model'], 0, 1, 'L');

        if ($order['year']) {
            $this->SetFont('helvetica', '', 11);
            $this->SetTextColor(75, 85, 99);
            $this->Cell(50, 6, 'Year:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->SetTextColor(31, 41, 55);
            $this->Cell(0, 6, $order['year'], 0, 1, 'L');
        }

        if ($order['chassis_number']) {
            $this->SetFont('helvetica', '', 11);
            $this->SetTextColor(75, 85, 99);
            $this->Cell(50, 6, 'Chassis Number:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->SetTextColor(31, 41, 55);
            $this->Cell(0, 6, $order['chassis_number'], 0, 1, 'L');
        }

        if ($order['vehicle_plate']) {
            $this->SetFont('helvetica', '', 11);
            $this->SetTextColor(75, 85, 99);
            $this->Cell(50, 6, 'Vehicle Plate:', 0, 0, 'L');
            $this->SetFont('helvetica', 'B', 11);
            $this->SetTextColor(31, 41, 55);
            $this->Cell(0, 6, $order['vehicle_plate'], 0, 1, 'L');
        }

        $this->Ln(8);
    }

    function PartDetails($order) {
        // Clean section header
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 8, 'Part Information', 0, 1);
        $this->Ln(2);

        // Elegant content layout
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);

        $this->Cell(50, 6, 'Part Name:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, $order['part_name'], 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);
        $this->Cell(50, 6, 'Category:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, ucfirst(str_replace('-', ' ', $order['part_category'])), 0, 1, 'L');

        if ($order['part_description']) {
            $this->Ln(3);
            $this->SetFont('helvetica', 'B', 11);
            $this->SetTextColor(31, 41, 55);
            $this->Cell(50, 6, 'Description:', 0, 0, 'L');
            $this->Ln(2);
            $this->SetFont('helvetica', '', 10);
            $this->SetTextColor(75, 85, 99);
            $this->MultiCell(140, 5, $order['part_description'], 0, 'L');
        }

        $this->Ln(8);
    }

    function OrderRequestInfo($order) {
        // Clean section header
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 8, 'Order Request Details', 0, 1);
        $this->Ln(2);

        // Elegant content layout
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);

        $this->Cell(50, 6, 'Order Type:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $orderTypeText = ucfirst($order['order_type']) . ' Order';
        if ($order['order_type'] == 'urgent') {
            $orderTypeText .= ' (Express)';
        }
        $this->Cell(0, 6, $orderTypeText, 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);
        $this->Cell(50, 6, 'Request Status:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(59, 130, 246);
        $this->Cell(0, 6, ucfirst($order['status']), 0, 1, 'L');

        $this->SetFont('helvetica', '', 11);
        $this->SetTextColor(75, 85, 99);
        $this->Cell(50, 6, 'Request Date:', 0, 0, 'L');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(31, 41, 55);
        $this->Cell(0, 6, date('M d, Y H:i', strtotime($order['created_at'])), 0, 1, 'L');

        if ($order['images']) {
            $images = json_decode($order['images'], true);
            if (is_array($images) && count($images) > 0) {
                $this->Ln(2);
                $this->SetFont('helvetica', 'B', 11);
                $this->SetTextColor(31, 41, 55);
                $this->Cell(50, 6, 'Uploaded Images:', 0, 0, 'L');
                $this->SetFont('helvetica', 'B', 11);
                $this->SetTextColor(59, 130, 246);
                $this->Cell(0, 6, count($images) . ' file(s)', 0, 1, 'L');
            }
        }

        $this->Ln(10);

        // Clean important notice
        $this->SetFillColor(248, 250, 252);
        $this->RoundedRect(15, $this->GetY(), 186, 25, 3, 'DF');

        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColor(31, 41, 55);
        $this->SetXY(20, $this->GetY() + 3);
        $this->Cell(0, 6, 'Important Notice', 0, 1, '', 0);
        $this->Ln(1);

        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(75, 85, 99);
        $this->SetXY(20, $this->GetY());
        $this->MultiCell(176, 5, 'This is a special order request. Our team will verify part availability and pricing within 24-48 hours. You will receive a confirmation email with detailed pricing and delivery timeline. A 50% deposit is required upon order confirmation.', 0, 'L');
    }
}

// PDF Class using TCPDF
class InvoicePDF extends TCPDF {
    public $order_number = '';

    public $brand_blue = array(13, 110, 253);
    public $brand_dark = array(26, 35, 126);
    public $light_gray = array(244, 246, 249);
    public $mid_gray  = array(226, 232, 240);
    public $text_color = array(51, 65, 85);

    // Page header
    public function Header() {
        $this->SetFillColor(26, 35, 126);
        $this->Rect(0, 0, 210, 42, 'F');
        $this->SetDrawColor(13, 110, 253);
        $this->SetLineWidth(1.2);
        $this->Line(0, 42, 210, 42);

        // Logo
        if (file_exists(__DIR__ . '/../img/logo/logox.jpg')) {
            $this->Image(__DIR__ . '/../img/logo/logox.jpg', 14, 9, 24, 24, '', '', '', false, 300, '', false, false, 0, false, false, true);
        }

        // Company Info
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 15);
        $this->SetXY(44, 10);
        $this->Cell(0, 6, 'SPARE XPRESS LTD', 0, 1);

        $this->SetFont('helvetica', '', 8.5);
        $this->SetTextColor(200, 214, 245);
        $this->SetX(44);
        $this->Cell(0, 4, 'Your Auto Parts Store', 0, 1);
        $this->SetX(44);
        $this->Cell(0, 4, 'Kagarama, Kicukiro, Kigali, Rwanda', 0, 1);
        $this->SetX(44);
        $this->Cell(0, 4, 'Phone: +250 792 865 114  |  Email: support@sparexpress.rw', 0, 1);

        // Invoice title
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(0, 12);
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 10, 'INVOICE', 0, 1, 'R');
        $this->SetX(PDF_MARGIN_RIGHT);

        // Invoice meta
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(220, 227, 248);
        $this->SetXY(120, 22);
        $this->Cell(75, 5, 'Invoice #:  ' . $this->order_number, 0, 1, 'R');
        $this->SetXY(120, 27);
        $this->Cell(75, 5, 'Order #:  ' . $this->order_number, 0, 1, 'R');
        $this->SetXY(120, 32);
        $this->Cell(75, 5, 'Date:  ' . date('M d, Y'), 0, 1, 'R');

        $this->SetY(50);
        $this->SetTextColor(51, 65, 85);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-30);
        $this->SetFont('helvetica', '', 7.5);
        $this->SetTextColor(120, 130, 150);

        $this->SetDrawColor(226, 232, 240);
        $this->SetLineWidth(0.4);
        $this->Line(PDF_MARGIN_LEFT, $this->GetY(), 210 - PDF_MARGIN_RIGHT, $this->GetY());

        $this->Cell(0, 4, 'SPARE XPRESS LTD - Terms: Payment due within 30 days. Warranty covers manufacturing defects only.', 0, 1, 'C');
        $this->Cell(0, 4, 'Thank you for choosing SPARE XPRESS LTD!', 0, 1, 'C');

        $this->SetY(-14);
        $this->SetFont('helvetica', 'B', 9);
        $this->SetTextColor(13, 110, 253);
        $this->Cell(0, 5, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    function CustomerDetails($customer) {
        // Section label bar
        $this->SetFillColor(244, 246, 249);
        $this->SetFont('helvetica', 'B', 9.5);
        $this->SetTextColor(26, 35, 126);
        $this->Cell(0, 8, 'BILL TO', 0, 1, '', 1);
        $this->SetDrawColor(226, 232, 240);
        $this->Line(PDF_MARGIN_LEFT, $this->GetY(), 210 - PDF_MARGIN_RIGHT, $this->GetY());
        $this->Ln(2);

        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(51, 65, 85);

        $customer_name = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
        $this->SetFont('helvetica', 'B', 10.5);
        $this->Cell(0, 6, $customer_name ?: 'Walk-in Customer', 0, 1);
        $this->SetFont('helvetica', '', 10);
        if (!empty($customer['customer_phone'])) {
            $this->Cell(0, 5.5, 'Phone: ' . $customer['customer_phone'], 0, 1);
        }
        if (!empty($customer['customer_email'])) {
            $this->Cell(0, 5.5, 'Email: ' . $customer['customer_email'], 0, 1);
        }
        $address = $customer['customer_address'] ?? ($customer['shipping_address'] ?? '');
        if ($address) {
            $this->Cell(0, 5.5, $address, 0, 1);
        }

        $this->Ln(7);
    }

    function OrderItems($items) {
        // Height of the asset
        $col_desc = 100;
        $col_qty  = 20;
        $col_unit = 28;
        $col_amt  = 32;

        $this->SetFillColor(26, 35, 126);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell($col_desc, 9, 'ITEM DESCRIPTION', 1, 0, 'L', 1);
        $this->Cell($col_qty, 9, 'QTY', 1, 0, 'C', 1);
        $this->Cell($col_unit, 9, 'UNIT PRICE', 1, 0, 'R', 1);
        $this->Cell($col_amt, 9, 'AMOUNT', 1, 1, 'R', 1);

        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(51, 65, 85);

        $fill = false;
        $row_h = 8;
        while ($item = $items->fetch_assoc()) {
            $desc_lines = 1;
            $item_desc = $item['product_name'];
            if ($item['product_brand'] || $item['product_model']) {
                $meta = trim(($item['product_brand'] ?? '') . ' ' . ($item['product_model'] ?? ''));
                if ($meta) {
                    $item_desc .= "\n" . $meta;
                    $desc_lines = 2;
                }
            }

            // Compute row height based on description width
            $text_w = $this->GetStringWidth($item_desc);
            $est_lines = max(1, (int)ceil($text_w / ($col_desc - 4)));
            $row_h = 8 * $est_lines;

            $this->SetFillColor($fill ? 244 : 255, $fill ? 246 : 255, $fill ? 249 : 255);
            $startY = $this->GetY();

            // Draw description (left)
            $this->SetXY(PDF_MARGIN_LEFT, $startY);
            $this->MultiCell($col_desc, 7, $item_desc, 'LR', 'L', $fill, 0, '', '', true, 0, false, true, 7, 'M', false);

            // Qty
            $this->SetXY(PDF_MARGIN_LEFT + $col_desc, $startY);
            $this->Cell($col_qty, $row_h, (string)$item['quantity'], 0, 0, 'C', $fill, '', 0, false, 'M');

            // Unit price
            $this->Cell($col_unit, $row_h, number_format($item['unit_price'], 0), 0, 0, 'R', $fill, '', 0, false, 'M');

            // Amount
            $this->Cell($col_amt, $row_h, number_format($item['unit_price'] * $item['quantity'], 0), 0, 1, 'R', $fill, '', 0, false, 'M');

            // Bottom border for the row
            $y_end = $startY + $row_h;
            $this->SetDrawColor(226, 232, 240);
            $this->Line(PDF_MARGIN_LEFT, $y_end, PDF_MARGIN_LEFT + $col_desc, $y_end);
            $y = $startY + $row_h;
            $this->Line(PDF_MARGIN_LEFT, $y, 210 - PDF_MARGIN_RIGHT, $y);

            $this->SetY($y_end);
            $fill = !$fill;
        }

        // Bottom border
        $this->SetDrawColor(26, 35, 126);
        $this->SetLineWidth(0.8);
        $this->Line(PDF_MARGIN_LEFT, $this->GetY(), 210 - PDF_MARGIN_RIGHT, $this->GetY());
        $this->SetLineWidth(0.2);

        $this->Ln(8);
    }

    function OrderSummary($order) {
        // Boxed summary on the right
        $box_w = 80;
        $x = 210 - PDF_MARGIN_RIGHT - $box_w;

        $summary_data = array(
            array('Subtotal', 'RWF ' . number_format($order['subtotal'], 0)),
        );
        if ($order['tax_amount'] > 0) {
            $summary_data[] = array('Tax', 'RWF ' . number_format($order['tax_amount'], 0));
        }
        if ($order['shipping_fee'] > 0) {
            $summary_data[] = array('Shipping', 'RWF ' . number_format($order['shipping_fee'], 0));
        }
        if ($order['discount_amount'] > 0) {
            $summary_data[] = array('Discount', '-RWF ' . number_format($order['discount_amount'], 0));
        }

        $startY = $this->GetY();
        $this->SetXY($x, $startY);
        $this->SetFont('helvetica', '', 9.5);
        $this->SetTextColor(51, 65, 85);

        foreach ($summary_data as $row) {
            $this->SetX($x);
            $this->Cell($box_w - 42, 7.5, $row[0], 0, 0, 'R');
            $this->Cell(42, 7.5, $row[1], 0, 1, 'R');
        }

        // Total row (highlighted)
        $this->SetX($x);
        $this->SetFillColor(26, 35, 126);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('helvetica', 'B', 10.5);
        $this->Cell($box_w - 42, 9, 'TOTAL', 0, 0, 'R', 1);
        $this->Cell(42, 9, 'RWF ' . number_format($order['total_amount'], 0), 0, 1, 'R', 1);

        $this->SetTextColor(51, 65, 85);
        $this->Ln(10);
    }

    function PaymentInfo($order) {
        // Section label bar
        $this->SetFillColor(244, 246, 249);
        $this->SetFont('helvetica', 'B', 9.5);
        $this->SetTextColor(26, 35, 126);
        $this->Cell(0, 8, 'PAYMENT INFORMATION', 0, 1, '', 1);
        $this->SetDrawColor(226, 232, 240);
        $this->Line(PDF_MARGIN_LEFT, $this->GetY(), 210 - PDF_MARGIN_RIGHT, $this->GetY());
        $this->Ln(2);

        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(51, 65, 85);

        $this->Cell(90, 6, 'Payment Method:  ' . ucfirst($order['payment_method']), 0, 0);
        $this->Cell(90, 6, 'Payment Status:  ' . ucfirst($order['payment_status']), 0, 1);

        if (!empty($order['transaction_id'])) {
            $this->Cell(90, 6, 'Transaction ID:  ' . $order['transaction_id'], 0, 1);
        }

        $this->Ln(3);
    }
}
?>
