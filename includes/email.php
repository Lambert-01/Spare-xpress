<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (is_readable($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    require_once __DIR__ . '/../lib/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../lib/phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../lib/phpmailer/src/Exception.php';
}

class EmailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->CharSet = 'UTF-8';

        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USER;
        $this->mailer->Password = SMTP_PASS;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = SMTP_PORT;

        // Default sender
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    }

    private function shouldUseResend() {
        return MAIL_PROVIDER === 'resend' && RESEND_API_KEY !== '';
    }

    private function formatFromAddress() {
        return SMTP_FROM_NAME . ' <' . SMTP_FROM_EMAIL . '>';
    }

    private function sendViaResend($toEmail, $toName, $subject, $htmlBody, $attachments = []) {
        if (!function_exists('curl_init')) {
            error_log('Resend email failed: PHP cURL extension is not enabled.');
            return false;
        }

        $payload = [
            'from' => $this->formatFromAddress(),
            'to' => [$toName ? $toName . ' <' . $toEmail . '>' : $toEmail],
            'subject' => $subject,
            'html' => $htmlBody,
            'text' => trim(strip_tags($htmlBody)),
        ];

        if (MAIL_ADMIN_COPY !== '' && strcasecmp(MAIL_ADMIN_COPY, $toEmail) !== 0) {
            $payload['bcc'] = [MAIL_ADMIN_COPY];
        }

        $encodedAttachments = [];
        foreach ($attachments as $attachment) {
            $path = $attachment['path'] ?? '';
            if ($path && is_readable($path)) {
                $encodedAttachments[] = [
                    'filename' => $attachment['filename'] ?? basename($path),
                    'content' => base64_encode(file_get_contents($path)),
                ];
            }
        }

        if (!empty($encodedAttachments)) {
            $payload['attachments'] = $encodedAttachments;
        }

        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . RESEND_API_KEY,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $status < 200 || $status >= 300) {
            error_log('Resend email failed: ' . ($error ?: $response));
            return false;
        }

        return true;
    }

    private function prepareMessage() {
        $this->mailer->clearAddresses();
        $this->mailer->clearAttachments();
        $this->mailer->clearReplyTos();
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        if (MAIL_ADMIN_COPY !== '') {
            $this->mailer->addBCC(MAIL_ADMIN_COPY);
        }
    }

    public function sendMessageNotification($customerEmail, $customerName, $messagePreview, $portalLink) {
        try {
            $subject = 'New Message from SPARE XPRESS Support';
            $body = $this->getMessageNotificationTemplate($customerName, $messagePreview, $portalLink);
            if ($this->shouldUseResend()) {
                return $this->sendViaResend($customerEmail, $customerName, $subject, $body);
            }

            $this->prepareMessage();
            // Recipients
            $this->mailer->addAddress($customerEmail, $customerName);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Message notification email failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    private function getMessageNotificationTemplate($customerName, $messagePreview, $portalLink) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>New Message from Support</h1>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>You have a new message from " . SITE_NAME . "</p>
            </div>

            <div style='background: white; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #007bff; margin-top: 0;'>Hello {$customerName}!</h2>

                <p>You have received a new message from our support team:</p>

                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #007bff;'>
                    <p style='margin: 0; font-style: italic; color: #495057;'>\"" . htmlspecialchars($messagePreview) . "\"</p>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$portalLink}' style='background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>View Message in Portal</a>
                </div>

                <p style='color: #6c757d; font-size: 14px; text-align: center;'>
                    You can also log in to your account to view all your messages and continue the conversation.
                </p>

                <hr style='border: none; border-top: 1px solid #dee2e6; margin: 30px 0;'>

                <div style='text-align: center; color: #6c757d; font-size: 14px;'>
                    <p style='margin: 5px 0;'><strong>" . SITE_NAME . "</strong></p>
                    <p style='margin: 5px 0;'>" . SITE_ADDRESS . "</p>
                    <p style='margin: 5px 0;'>Phone: " . SITE_PHONE . " | Email: " . SITE_EMAIL . "</p>
                </div>
            </div>
        </div>
        ";
    }

    public function sendOrderInvoice($customerEmail, $customerName, $orderId, $pdfPath, $orderDetails = []) {
        try {
            $subject = 'Your Order Invoice - ' . SITE_NAME;
            $body = $this->getOrderEmailTemplate($customerName, $orderId, $orderDetails);
            $attachments = file_exists($pdfPath) ? [[
                'path' => $pdfPath,
                'filename' => 'Invoice_' . $orderId . '.pdf',
            ]] : [];

            if ($this->shouldUseResend()) {
                return $this->sendViaResend($customerEmail, $customerName, $subject, $body, $attachments);
            }

            $this->prepareMessage();
            // Recipients
            $this->mailer->addAddress($customerEmail, $customerName);

            // Attachments
            if (file_exists($pdfPath)) {
                $this->mailer->addAttachment($pdfPath, 'Invoice_' . $orderId . '.pdf');
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendTestEmail($toEmail, $toName, $subject, $body) {
        try {
            if ($this->shouldUseResend()) {
                return $this->sendViaResend($toEmail, $toName, $subject, $body);
            }

            $this->prepareMessage();
            // Recipients
            $this->mailer->addAddress($toEmail, $toName);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Test email failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendOrderRequestConfirmation($customerEmail, $customerName, $orderRequestId, $pdfPath) {
        try {
            $subject = 'Your Special Order Request - ' . SITE_NAME;
            $body = $this->getOrderRequestEmailTemplate($customerName, $orderRequestId);
            $attachments = file_exists($pdfPath) ? [[
                'path' => $pdfPath,
                'filename' => 'Order_Request_' . $orderRequestId . '.pdf',
            ]] : [];

            if ($this->shouldUseResend()) {
                return $this->sendViaResend($customerEmail, $customerName, $subject, $body, $attachments);
            }

            $this->prepareMessage();
            // Recipients
            $this->mailer->addAddress($customerEmail, $customerName);

            // Attachments
            if (file_exists($pdfPath)) {
                $this->mailer->addAttachment($pdfPath, 'Order_Request_' . $orderRequestId . '.pdf');
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Order request email sending failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendPriceRequestConfirmation($customerEmail, array $request) {
        $customerName = $request['customer_name'] ?? 'Customer';
        $requestId = (int)($request['request_id'] ?? 0);
        $subject = "Today's Price Request Received - " . SITE_NAME;
        $body = $this->getPriceRequestEmailTemplate($request);

        try {
            if ($this->shouldUseResend()) {
                return $this->sendViaResend($customerEmail, $customerName, $subject, $body);
            }

            $this->prepareMessage();
            $this->mailer->addAddress($customerEmail, $customerName);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Price request email failed for PR-$requestId: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendContactNotification(array $contact) {
        $adminEmail = MAIL_ADMIN_COPY ?: SITE_EMAIL;
        $customerName = $contact['name'] ?? 'Customer';
        $customerEmail = $contact['email'] ?? '';
        $subject = 'New Contact Message - ' . ($contact['subject'] ?? SITE_NAME);
        $adminBody = $this->getContactAdminEmailTemplate($contact);
        $customerBody = $this->getContactCustomerEmailTemplate($contact);

        $adminSent = $this->sendTestEmail($adminEmail, 'SPARE XPRESS Admin', $subject, $adminBody);
        $customerSent = false;
        if ($customerEmail !== '' && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $customerSent = $this->sendTestEmail($customerEmail, $customerName, 'We received your message - ' . SITE_NAME, $customerBody);
        }

        return $adminSent || $customerSent;
    }

    private function getOrderEmailTemplate($customerName, $orderId, $orderDetails = []) {
        $itemsHtml = '';
        foreach (($orderDetails['items'] ?? []) as $item) {
            $itemName = htmlspecialchars((string)($item['name'] ?? 'Product'));
            $qty = (int)($item['quantity'] ?? 0);
            $subtotal = number_format((float)($item['subtotal'] ?? 0));
            $itemsHtml .= "<tr><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$itemName}</td><td style='padding: 8px; border-bottom: 1px solid #eee; text-align: center;'>{$qty}</td><td style='padding: 8px; border-bottom: 1px solid #eee; text-align: right;'>RWF {$subtotal}</td></tr>";
        }

        $summaryHtml = '';
        if (!empty($orderDetails)) {
            $paymentMethod = htmlspecialchars((string)($orderDetails['payment_method'] ?? ''));
            $phone = htmlspecialchars((string)($orderDetails['phone'] ?? ''));
            $subtotal = number_format((float)($orderDetails['subtotal'] ?? 0));
            $shipping = number_format((float)($orderDetails['shipping_fee'] ?? 0));
            $total = number_format((float)($orderDetails['total_amount'] ?? 0));
            $summaryHtml = "
                <p style='margin: 5px 0;'><strong>Customer Phone:</strong> {$phone}</p>
                <p style='margin: 5px 0;'><strong>Payment Method:</strong> {$paymentMethod}</p>
                <p style='margin: 5px 0;'><strong>Subtotal:</strong> RWF {$subtotal}</p>
                <p style='margin: 5px 0;'><strong>Shipping:</strong> RWF {$shipping}</p>
                <p style='margin: 5px 0; font-size: 18px;'><strong>Total:</strong> RWF {$total}</p>
            ";
        }

        $itemsSection = $itemsHtml ? "
            <div style='margin: 20px 0;'>
                <h3 style='margin-top: 0; color: #2d3748;'>Items Ordered</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <thead><tr><th style='text-align: left; padding: 8px;'>Product</th><th style='padding: 8px;'>Qty</th><th style='text-align: right; padding: 8px;'>Subtotal</th></tr></thead>
                    <tbody>{$itemsHtml}</tbody>
                </table>
            </div>
        " : '';

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>Order Confirmed!</h1>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>Thank you for choosing " . SITE_NAME . "</p>
            </div>

            <div style='background: white; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #28a745; margin-top: 0;'>Hello {$customerName}!</h2>

                <p>Your order has been successfully placed and confirmed. Please find your invoice attached to this email.</p>

                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0; color: #2d3748;'>Order Details</h3>
                    <p style='margin: 5px 0;'><strong>Order Number:</strong> {$orderId}</p>
                    <p style='margin: 5px 0;'><strong>Order Date:</strong> " . date('M d, Y H:i') . "</p>
                    {$summaryHtml}
                </div>

                {$itemsSection}

                <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h4 style='margin-top: 0; color: #856404;'>Payment Instructions</h4>
                    <p style='margin: 5px 0;'>Please complete your payment using one of the following methods:</p>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li><strong>Bank Transfer:</strong> Bank of Kigali - Account: 00000-000000-00</li>
                        <li><strong>Mobile Money:</strong> +250 792 865 114</li>
                    </ul>
                    <p style='margin: 5px 0;'><strong>Reference:</strong> {$orderId}</p>
                </div>

                <div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h4 style='margin-top: 0; color: #0c5460;'>What's Next?</h4>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Your order will be processed within 24-48 hours</li>
                        <li>You'll receive updates on your order status</li>
                        <li>Delivery typically takes 2-5 business days</li>
                    </ul>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . SITE_URL . "/pages/order_history.php' style='background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Track Your Order</a>
                </div>

                <hr style='border: none; border-top: 1px solid #dee2e6; margin: 30px 0;'>

                <div style='text-align: center; color: #6c757d; font-size: 14px;'>
                    <p style='margin: 5px 0;'><strong>" . SITE_NAME . "</strong></p>
                    <p style='margin: 5px 0;'>" . SITE_ADDRESS . "</p>
                    <p style='margin: 5px 0;'>Phone: " . SITE_PHONE . " | Email: " . SITE_EMAIL . "</p>
                </div>
            </div>
        </div>
        ";
    }

    private function getPriceRequestEmailTemplate(array $request) {
        $requestId = (int)($request['request_id'] ?? 0);
        $customerName = htmlspecialchars((string)($request['customer_name'] ?? 'Customer'));
        $partName = htmlspecialchars((string)($request['part_name'] ?? 'Requested part'));
        $carModel = htmlspecialchars((string)($request['car_model'] ?? ''));
        $phone = htmlspecialchars((string)($request['phone_number'] ?? ''));
        $quantity = (int)($request['quantity'] ?? 1);

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0dcaf0; color: #111; padding: 28px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>Price Request Received</h1>
                <p style='margin: 10px 0 0 0;'>Our team will check today's price and contact you.</p>
            </div>
            <div style='background: white; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;'>
                <h2 style='margin-top: 0;'>Hello {$customerName},</h2>
                <p>Thank you for requesting today's price from " . SITE_NAME . ".</p>
                <div style='background: #f8f9fa; padding: 18px; border-radius: 8px; margin: 20px 0;'>
                    <p><strong>Reference:</strong> PR-{$requestId}</p>
                    <p><strong>Part:</strong> {$partName}</p>
                    <p><strong>Vehicle:</strong> {$carModel}</p>
                    <p><strong>Quantity:</strong> {$quantity}</p>
                    <p><strong>Phone:</strong> {$phone}</p>
                </div>
                <p>We will verify availability and send a quote. Some imported parts require a 50% deposit after quote approval.</p>
                <p style='color: #6c757d; font-size: 14px;'>Need help? Contact " . SITE_EMAIL . " or " . SITE_PHONE . ".</p>
            </div>
        </div>";
    }

    private function getContactAdminEmailTemplate(array $contact) {
        $name = htmlspecialchars((string)($contact['name'] ?? ''));
        $email = htmlspecialchars((string)($contact['email'] ?? ''));
        $phone = htmlspecialchars((string)($contact['phone'] ?? ''));
        $subject = htmlspecialchars((string)($contact['subject'] ?? ''));
        $message = nl2br(htmlspecialchars((string)($contact['message'] ?? '')));

        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2>New Contact Message</h2>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Phone:</strong> {$phone}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <div style='background: #f8f9fa; padding: 18px; border-radius: 8px;'>{$message}</div>
        </div>";
    }

    private function getContactCustomerEmailTemplate(array $contact) {
        $name = htmlspecialchars((string)($contact['name'] ?? 'Customer'));
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0d6efd; color: white; padding: 28px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>Message Received</h1>
            </div>
            <div style='background: white; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;'>
                <h2 style='margin-top: 0;'>Hello {$name},</h2>
                <p>Thank you for contacting " . SITE_NAME . ". We received your message and will get back to you within 24 hours.</p>
                <p style='color: #6c757d; font-size: 14px;'>Phone: " . SITE_PHONE . " | Email: " . SITE_EMAIL . "</p>
            </div>
        </div>";
    }

    private function getOrderRequestEmailTemplate($customerName, $orderRequestId) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #007bff 0%, #6610f2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='margin: 0; font-size: 24px;'>Special Order Request Received!</h1>
                <p style='margin: 10px 0 0 0; opacity: 0.9;'>Thank you for choosing " . SITE_NAME . "</p>
            </div>

            <div style='background: white; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;'>
                <h2 style='color: #007bff; margin-top: 0;'>Hello {$customerName}!</h2>

                <p>Your special order request has been successfully received and is being processed. Please find your order confirmation attached to this email.</p>

                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0; color: #2d3748;'>Order Request Details</h3>
                    <p style='margin: 5px 0;'><strong>Request ID:</strong> {$orderRequestId}</p>
                    <p style='margin: 5px 0;'><strong>Request Date:</strong> " . date('M d, Y H:i') . "</p>
                    <p style='margin: 5px 0;'><strong>Status:</strong> Pending Review</p>
                </div>

                <div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h4 style='margin-top: 0; color: #856404;'>What Happens Next?</h4>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li>Our team will verify the part availability within 24-48 hours</li>
                        <li>You'll receive a quote with pricing and estimated delivery time</li>
                        <li>Upon acceptance, a 50% deposit will be required</li>
                        <li>We'll source the part from our international suppliers</li>
                        <li>Professional delivery across Rwanda</li>
                    </ul>
                </div>

                <div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h4 style='margin-top: 0; color: #0c5460;'>Payment Information</h4>
                    <p style='margin: 5px 0;'>Once your order is confirmed, you can pay the 50% deposit using:</p>
                    <ul style='margin: 10px 0; padding-left: 20px;'>
                        <li><strong>Mobile Money:</strong> MTN Mobile Money, Airtel Money</li>
                        <li><strong>Bank Transfer:</strong> Bank of Kigali</li>
                    </ul>
                    <p style='margin: 5px 0;'><strong>Reference:</strong> {$orderRequestId}</p>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <p style='color: #6c757d; font-size: 14px;'>Need help? Contact us at " . SITE_EMAIL . " or " . SITE_PHONE . "</p>
                </div>

                <hr style='border: none; border-top: 1px solid #dee2e6; margin: 30px 0;'>

                <div style='text-align: center; color: #6c757d; font-size: 14px;'>
                    <p style='margin: 5px 0;'><strong>" . SITE_NAME . "</strong></p>
                    <p style='margin: 5px 0;'>" . SITE_ADDRESS . "</p>
                    <p style='margin: 5px 0;'>Phone: " . SITE_PHONE . " | Email: " . SITE_EMAIL . "</p>
                </div>
            </div>
        </div>
        ";
    }
}

// Simple sendEmail function for backward compatibility
function sendEmail($to, $subject, $body, $isHtml = true) {
    $mailer = new PHPMailer(true);

    try {
        // Server settings
        $mailer->isSMTP();
        $mailer->Host = SMTP_HOST;
        $mailer->SMTPAuth = true;
        $mailer->Username = SMTP_USER;
        $mailer->Password = SMTP_PASS;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->Port = SMTP_PORT;

        // Recipients
        $mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mailer->addAddress($to);

        // Content
        $mailer->isHTML($isHtml);
        $mailer->Subject = $subject;
        $mailer->Body = $body;
        if (!$isHtml) {
            $mailer->AltBody = strip_tags($body);
        }

        return $mailer->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mailer->ErrorInfo);
        return false;
    }
}
?>
