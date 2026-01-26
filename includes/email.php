<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require_once __DIR__ . '/../lib/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../lib/phpmailer/src/Exception.php';

class EmailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

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

    public function sendMessageNotification($customerEmail, $customerName, $messagePreview, $portalLink) {
        try {
            // Recipients
            $this->mailer->addAddress($customerEmail, $customerName);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'New Message from SPARE XPRESS Support';
            $this->mailer->Body = $this->getMessageNotificationTemplate($customerName, $messagePreview, $portalLink);
            $this->mailer->AltBody = strip_tags($this->getMessageNotificationTemplate($customerName, $messagePreview, $portalLink));

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

    public function sendOrderInvoice($customerEmail, $customerName, $orderId, $pdfPath) {
        try {
            // Recipients
            $this->mailer->addAddress($customerEmail, $customerName);

            // Attachments
            if (file_exists($pdfPath)) {
                $this->mailer->addAttachment($pdfPath, 'Invoice_' . $orderId . '.pdf');
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Your Order Invoice - ' . SITE_NAME;
            $this->mailer->Body = $this->getOrderEmailTemplate($customerName, $orderId);
            $this->mailer->AltBody = strip_tags($this->getOrderEmailTemplate($customerName, $orderId));

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendTestEmail($toEmail, $toName, $subject, $body) {
        try {
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
            // Recipients
            $this->mailer->addAddress($customerEmail, $customerName);

            // Attachments
            if (file_exists($pdfPath)) {
                $this->mailer->addAttachment($pdfPath, 'Order_Request_' . $orderRequestId . '.pdf');
            }

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Your Special Order Request - ' . SITE_NAME;
            $this->mailer->Body = $this->getOrderRequestEmailTemplate($customerName, $orderRequestId);
            $this->mailer->AltBody = strip_tags($this->getOrderRequestEmailTemplate($customerName, $orderRequestId));

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Order request email sending failed: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    private function getOrderEmailTemplate($customerName, $orderId) {
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
                </div>

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