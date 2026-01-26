<?php
// Notification Trigger System for SPARE XPRESS LTD
// Automatically creates notifications based on system events

class NotificationTrigger {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Check if a notification trigger is enabled
     */
    private function isTriggerEnabled($trigger_key) {
        $result = $this->conn->query("SELECT setting_value FROM system_settings WHERE setting_group = 'notification_triggers' AND setting_key = '$trigger_key'");
        if ($result && $row = $result->fetch_assoc()) {
            return $row['setting_value'] === 'true';
        }
        return false;
    }

    /**
     * Get enabled notification channels
     */
    private function getEnabledChannels() {
        $channels = [];

        $channel_settings = [
            'admin_panel' => 'true', // Always enabled for admin panel
            'email' => 'email_enabled',
            'sms' => 'sms_enabled',
            'whatsapp' => 'whatsapp_enabled'
        ];

        foreach ($channel_settings as $channel => $setting_key) {
            if ($setting_key === 'true') {
                $channels[] = $channel;
            } else {
                $result = $this->conn->query("SELECT setting_value FROM system_settings WHERE setting_group = 'notifications' AND setting_key = '$setting_key'");
                if ($result && $row = $result->fetch_assoc() && $row['setting_value'] === 'true') {
                    $channels[] = $channel;
                }
            }
        }

        return $channels;
    }

    /**
     * Create a notification
     */
    private function createNotification($type, $title, $message, $recipient_type = 'admin', $recipient_id = null, $priority = 'normal', $related_order_id = null, $related_product_id = null) {
        $channels = json_encode($this->getEnabledChannels());

        $stmt = $this->conn->prepare("INSERT INTO notifications
            (notification_type, title, message, recipient_type, recipient_id, priority, channels, related_order_id, related_product_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssssss", $type, $title, $message, $recipient_type, $recipient_id, $priority, $channels, $related_order_id, $related_product_id);
        $stmt->execute();

        return $this->conn->insert_id;
    }

    /**
     * Trigger: New Order Placed (Admin Notification)
     */
    public function newOrderAdmin($order_id) {
        if (!$this->isTriggerEnabled('new_order_admin')) return;

        $order = $this->conn->query("SELECT o.*, c.first_name, c.last_name FROM orders_enhanced o LEFT JOIN customers_enhanced c ON o.customer_id = c.id WHERE o.id = $order_id")->fetch_assoc();
        if (!$order) return;

        $customer_name = $order['first_name'] . ' ' . $order['last_name'] ?: 'Walk-in Customer';
        $title = "New Order Received - {$order['order_number']}";
        $message = "A new order has been placed by {$customer_name}.\n\nOrder: {$order['order_number']}\nTotal: RWF " . number_format($order['total_amount'], 0) . "\n\nPlease review and process the order.";

        $this->createNotification('order', $title, $message, 'admin', null, 'high', $order_id);
    }

    /**
     * Trigger: New Order Placed (Customer Confirmation)
     */
    public function newOrderCustomer($order_id) {
        if (!$this->isTriggerEnabled('new_order_customer')) return;

        $order = $this->conn->query("SELECT o.*, c.first_name, c.last_name, c.email FROM orders_enhanced o LEFT JOIN customers_enhanced c ON o.customer_id = c.id WHERE o.id = $order_id")->fetch_assoc();
        if (!$order || !$order['email']) return;

        $title = "Order Confirmation - {$order['order_number']}";
        $message = "Thank you for your order! Your order has been received and is being processed.\n\nOrder Number: {$order['order_number']}\nTotal Amount: RWF " . number_format($order['total_amount'], 0) . "\n\nWe will notify you when your order ships.";

        $this->createNotification('order', $title, $message, 'customer', $order['customer_id'], 'normal', $order_id);
    }

    /**
     * Trigger: Order Status Changed
     */
    public function orderStatusChanged($order_id, $old_status, $new_status) {
        if (!$this->isTriggerEnabled('order_status_change')) return;

        $order = $this->conn->query("SELECT o.*, c.first_name, c.last_name, c.email FROM orders_enhanced o LEFT JOIN customers_enhanced c ON o.customer_id = c.id WHERE o.id = $order_id")->fetch_assoc();
        if (!$order) return;

        $customer_name = $order['first_name'] . ' ' . $order['last_name'] ?: 'Customer';

        // Admin notification
        $admin_title = "Order Status Updated - {$order['order_number']}";
        $admin_message = "Order {$order['order_number']} status changed from " . ucfirst($old_status) . " to " . ucfirst($new_status) . ".";
        $this->createNotification('order', $admin_title, $admin_message, 'admin', null, 'normal', $order_id);

        // Customer notification (if email exists)
        if ($order['email']) {
            $customer_title = "Order Update - {$order['order_number']}";
            $customer_message = "Your order status has been updated.\n\nOrder: {$order['order_number']}\nNew Status: " . ucfirst($new_status) . "\n\nThank you for choosing SPARE XPRESS LTD!";
            $this->createNotification('order', $customer_title, $customer_message, 'customer', $order['customer_id'], 'normal', $order_id);
        }
    }

    /**
     * Trigger: Payment Received
     */
    public function paymentReceived($order_id, $amount, $payment_method) {
        if (!$this->isTriggerEnabled('payment_received')) return;

        $order = $this->conn->query("SELECT o.*, c.first_name, c.last_name FROM orders_enhanced o LEFT JOIN customers_enhanced c ON o.customer_id = c.id WHERE o.id = $order_id")->fetch_assoc();
        if (!$order) return;

        $customer_name = $order['first_name'] . ' ' . $order['last_name'] ?: 'Customer';
        $title = "Payment Received - {$order['order_number']}";
        $message = "Payment of RWF " . number_format($amount, 0) . " received for order {$order['order_number']} via " . ucfirst(str_replace('_', ' ', $payment_method)) . ".\n\nCustomer: {$customer_name}";

        $this->createNotification('payment', $title, $message, 'admin', null, 'normal', $order_id);
    }

    /**
     * Trigger: Low Stock Alert
     */
    public function lowStockAlert($product_id, $current_stock) {
        if (!$this->isTriggerEnabled('low_stock_alert')) return;

        $product = $this->conn->query("SELECT p.product_name, b.brand_name FROM products_enhanced p JOIN vehicle_brands_enhanced b ON p.brand_id = b.id WHERE p.id = $product_id")->fetch_assoc();
        if (!$product) return;

        $title = "Low Stock Alert - {$product['product_name']}";
        $message = "Product '{$product['product_name']}' is running low on stock.\n\nBrand: {$product['brand_name']}\nCurrent Stock: {$current_stock} units\n\nPlease restock soon to avoid stockouts.";

        $this->createNotification('inventory', $title, $message, 'admin', null, 'high', null, $product_id);
    }

    /**
     * Trigger: Out of Stock Alert
     */
    public function outOfStockAlert($product_id) {
        if (!$this->isTriggerEnabled('out_of_stock_alert')) return;

        $product = $this->conn->query("SELECT p.product_name, b.brand_name FROM products_enhanced p JOIN vehicle_brands_enhanced b ON p.brand_id = b.id WHERE p.id = $product_id")->fetch_assoc();
        if (!$product) return;

        $title = "Out of Stock - {$product['product_name']}";
        $message = "Product '{$product['product_name']}' has gone out of stock.\n\nBrand: {$product['brand_name']}\n\nImmediate restocking required!";

        $this->createNotification('inventory', $title, $message, 'admin', null, 'urgent', null, $product_id);
    }

    /**
     * Trigger: New Customer Registration
     */
    public function newCustomerRegistration($customer_id) {
        if (!$this->isTriggerEnabled('new_customer_registration')) return;

        $customer = $this->conn->query("SELECT first_name, last_name, email, phone FROM customers_enhanced WHERE id = $customer_id")->fetch_assoc();
        if (!$customer) return;

        $customer_name = $customer['first_name'] . ' ' . $customer['last_name'];
        $title = "New Customer Registration";
        $message = "New customer has registered:\n\nName: {$customer_name}\nEmail: {$customer['email']}\nPhone: {$customer['phone']}\n\nWelcome them to SPARE XPRESS LTD!";

        $this->createNotification('customer', $title, $message, 'admin', null, 'normal');
    }

    /**
     * Trigger: System Backup Completed
     */
    public function systemBackupCompleted($backup_path, $backup_size) {
        if (!$this->isTriggerEnabled('system_backup_completed')) return;

        $title = "System Backup Completed";
        $message = "Automated system backup has been completed successfully.\n\nBackup Location: {$backup_path}\nBackup Size: {$backup_size}\nTimestamp: " . date('Y-m-d H:i:s');

        $this->createNotification('system', $title, $message, 'admin', null, 'normal');
    }

    /**
     * Trigger: Security Alert
     */
    public function securityAlert($alert_type, $description, $ip_address = null) {
        if (!$this->isTriggerEnabled('security_alert')) return;

        $title = "Security Alert - " . ucfirst($alert_type);
        $message = "Security event detected:\n\nType: " . ucfirst($alert_type) . "\nDescription: {$description}\nIP Address: " . ($ip_address ?: 'Unknown') . "\nTimestamp: " . date('Y-m-d H:i:s') . "\n\nPlease review system security logs.";

        $this->createNotification('system', $title, $message, 'admin', null, 'urgent');
    }

    /**
     * Trigger: On-Demand Request Status Changed
     */
    public function onDemandStatusChanged($request_id, $old_status, $new_status) {
        $request = $this->conn->query("SELECT r.*, c.first_name, c.last_name FROM on_demand_requests_enhanced r LEFT JOIN customers_enhanced c ON r.customer_id = c.id WHERE r.id = $request_id")->fetch_assoc();
        if (!$request) return;

        $customer_name = $request['first_name'] . ' ' . $request['last_name'] ?: 'Customer';
        $title = "On-Demand Request Updated - {$request['request_number']}";
        $message = "On-demand request {$request['request_number']} status changed from " . ucfirst($old_status) . " to " . ucfirst($new_status) . ".\n\nCustomer: {$customer_name}\nPart: {$request['part_name']}";

        $this->createNotification('order', $title, $message, 'admin', null, 'normal');
    }

    /**
     * Generic notification creator for custom events
     */
    public function createCustomNotification($type, $title, $message, $priority = 'normal', $recipient_type = 'admin', $recipient_id = null) {
        return $this->createNotification($type, $title, $message, $recipient_type, $recipient_id, $priority);
    }

    /**
     * Send notification via enabled channels (to be implemented with actual services)
     */
    public function sendNotification($notification_id) {
        $notification = $this->conn->query("SELECT * FROM notifications WHERE id = $notification_id")->fetch_assoc();
        if (!$notification) return false;

        $channels = json_decode($notification['channels'], true) ?: [];
        $delivery_status = [];

        foreach ($channels as $channel) {
            switch ($channel) {
                case 'email':
                    $delivery_status[$channel] = $this->sendEmail($notification) ? 'sent' : 'failed';
                    break;
                case 'sms':
                    $delivery_status[$channel] = $this->sendSMS($notification) ? 'sent' : 'failed';
                    break;
                case 'whatsapp':
                    $delivery_status[$channel] = $this->sendWhatsApp($notification) ? 'sent' : 'failed';
                    break;
                case 'admin_panel':
                    $delivery_status[$channel] = 'sent'; // Already in panel
                    break;
                default:
                    $delivery_status[$channel] = 'unknown';
            }
        }

        // Update delivery status
        $status_json = json_encode($delivery_status);
        $this->conn->query("UPDATE notifications SET delivery_status = '$status_json', sent_at = NOW() WHERE id = $notification_id");

        return true;
    }

    /**
     * Send email notification (placeholder - implement with actual email service)
     */
    private function sendEmail($notification) {
        // TODO: Implement with PHPMailer or similar
        // For now, just log that email would be sent
        error_log("Email notification would be sent: " . $notification['title']);
        return true;
    }

    /**
     * Send SMS notification (placeholder - implement with SMS service)
     */
    private function sendSMS($notification) {
        // TODO: Implement with AfricasTalking, Twilio, etc.
        // For now, just log that SMS would be sent
        error_log("SMS notification would be sent: " . $notification['title']);
        return true;
    }

    /**
     * Send WhatsApp notification (placeholder - implement with WhatsApp Business API)
     */
    private function sendWhatsApp($notification) {
        // TODO: Implement with WhatsApp Business API
        // For now, just log that WhatsApp message would be sent
        error_log("WhatsApp notification would be sent: " . $notification['title']);
        return true;
    }
}

// Usage example:
/*
// Initialize notification trigger
$notification_trigger = new NotificationTrigger($conn);

// Trigger notifications for various events
$notification_trigger->newOrderAdmin($order_id);
$notification_trigger->orderStatusChanged($order_id, 'pending', 'processing');
$notification_trigger->lowStockAlert($product_id, 3);
$notification_trigger->paymentReceived($order_id, 25000, 'momo');
*/
?>