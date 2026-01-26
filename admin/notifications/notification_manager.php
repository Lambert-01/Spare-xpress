<?php
// Notification & Communication Center - Admin Panel
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Get filter parameters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query based on filter
$where_conditions = [];
$params = [];
$types = '';

if ($filter === 'unread') {
    $where_conditions[] = "n.is_read = 0";
} elseif ($filter === 'messages') {
    $where_conditions[] = "n.type = 'message'";
} elseif ($filter === 'system') {
    $where_conditions[] = "n.type = 'system'";
}

if (!empty($search)) {
    $where_conditions[] = "(c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

// Get notifications with conversation details
$query = "
    SELECT
        n.*,
        CASE
            WHEN n.type = 'message' THEN c.first_name
            WHEN n.type = 'system' THEN cs.first_name
        END as first_name,
        CASE
            WHEN n.type = 'message' THEN c.last_name
            WHEN n.type = 'system' THEN cs.last_name
        END as last_name,
        CASE
            WHEN n.type = 'message' THEN c.email
            WHEN n.type = 'system' THEN cs.email
        END as email,
        CASE
            WHEN n.type = 'message' THEN c.phone
            WHEN n.type = 'system' THEN cs.phone
        END as phone,
        conv.last_message, conv.updated_at as conv_updated,
        COUNT(m.id) as message_count
    FROM notifications n
    LEFT JOIN customers_enhanced c ON n.user_id = c.id AND n.type = 'message'
    LEFT JOIN customers_enhanced cs ON n.user_id = cs.id AND n.type = 'system'
    LEFT JOIN conversations conv ON n.reference_id = conv.id AND n.type = 'message'
    LEFT JOIN messages m ON conv.id = m.conversation_id
    " . (!empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "") . "
    GROUP BY n.id
    ORDER BY n.created_at DESC
";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$notifications = $stmt->get_result();

// Get unread count
$unread_count = countRowsWhere('notifications', 'is_read = 0');

// Get conversation details if one is selected
$conversation_id = $_GET['conversation'] ?? null;
$customer_id = $_GET['customer_id'] ?? null;
$conversation = null;
$messages = [];

// If customer_id is provided, find or create conversation
if ($customer_id && !$conversation_id) {
    $conv_check = $conn->prepare("SELECT id FROM conversations WHERE client_id = ? ORDER BY updated_at DESC LIMIT 1");
    $conv_check->bind_param("i", $customer_id);
    $conv_check->execute();
    $conv_result = $conv_check->get_result();
    if ($conv_result->num_rows > 0) {
        $conversation_id = $conv_result->fetch_assoc()['id'];
    }
}

if ($conversation_id) {
    // Get conversation details
    $conv_query = "
        SELECT conv.*, c.first_name, c.last_name, c.email, c.phone, conv.updated_at as created_at, COUNT(m.id) as total_messages
        FROM conversations conv
        JOIN customers_enhanced c ON conv.client_id = c.id
        LEFT JOIN messages m ON conv.id = m.conversation_id
        WHERE conv.id = ?
        GROUP BY conv.id
    ";
    $conv_stmt = $conn->prepare($conv_query);
    $conv_stmt->bind_param("i", $conversation_id);
    $conv_stmt->execute();
    $conversation = $conv_stmt->get_result()->fetch_assoc();

    // Get messages
    $msg_query = "
        SELECT m.*, c.first_name, c.last_name
        FROM messages m
        LEFT JOIN conversations conv ON m.conversation_id = conv.id
        LEFT JOIN customers_enhanced c ON m.sender_type = 'client' AND c.id = conv.client_id
        WHERE m.conversation_id = ?
        ORDER BY m.created_at ASC
    ";
    $msg_stmt = $conn->prepare($msg_query);
    $msg_stmt->bind_param("i", $conversation_id);
    $msg_stmt->execute();
    $messages = $msg_stmt->get_result();
}

// Get all conversations for the conversations list
$conversations_query = "
    SELECT
        conv.*,
        c.first_name, c.last_name, c.email,
        0 as total_messages,
        0 as unread_count
    FROM conversations conv
    JOIN customers_enhanced c ON conv.client_id = c.id
    ORDER BY conv.updated_at DESC
";
$conversations_stmt = $conn->prepare($conversations_query);
$conversations_stmt->execute();
$all_conversations = $conversations_stmt->get_result();
?>

<div class="admin-page">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1 fw-bold">
                <i class="bi bi-chat-dots-fill text-primary me-3"></i>
                Notification & Communication Center
            </h1>
            <p class="text-muted mb-0 fs-5">Professional two-way communication with clients</p>
        </div>
        <div class="text-end">
            <div class="d-flex align-items-center gap-3">
                <div class="text-center">
                    <div class="fw-bold text-danger fs-4" id="unread-badge"><?php echo $unread_count; ?></div>
                    <small class="text-muted">Unread</small>
                </div>
                <div class="vr"></div>
                <div class="text-center">
                    <div class="badge bg-success fs-6 px-3 py-2">
                        <i class="bi bi-circle-fill me-1"></i>Active
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="form-card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </label>
                    <select class="form-select" id="filter-select">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Notifications</option>
                        <option value="unread" <?php echo $filter === 'unread' ? 'selected' : ''; ?>>Unread Only</option>
                        <option value="messages" <?php echo $filter === 'messages' ? 'selected' : ''; ?>>Messages</option>
                        <option value="system" <?php echo $filter === 'system' ? 'selected' : ''; ?>>System Alerts</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-search me-1"></i>Search
                    </label>
                    <input type="text" class="form-control" id="search-input" placeholder="Search by client name or email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-search me-1"></i>Apply
                    </button>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-success flex-fill" onclick="startNewConversation()">
                            <i class="bi bi-plus-circle me-1"></i>New
                        </button>
                        <button class="btn btn-outline-secondary" onclick="markAllAsRead()" title="Mark All as Read">
                            <i class="bi bi-check-all"></i>
                        </button>
                        <button class="btn btn-outline-info" onclick="refreshNotifications()" title="Refresh">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="row g-4">
        <!-- Left Panel - Inbox -->
        <div class="col-xl-5">
            <div class="form-card h-100">
                <div class="card-header bg-white border-bottom-0 pb-0">
                    <h5 class="mb-1">
                        <i class="bi bi-inbox-fill text-primary me-2"></i>
                        Inbox
                    </h5>
                    <p class="text-muted small mb-0">Client messages and notifications</p>
                </div>
                <div class="card-body p-0">
                    <div class="notification-list" id="notification-list" style="max-height: 600px; overflow-y: auto;">
                        <?php if ($notifications->num_rows > 0): ?>
                            <?php while ($notification = $notifications->fetch_assoc()): ?>
                                <div class="notification-item p-2 border-bottom <?php echo !$notification['is_read'] ? 'unread' : ''; ?>"
                                     onclick="openNotificationModal(<?php echo $notification['id']; ?>, '<?php echo addslashes($notification['type']); ?>', <?php echo $notification['reference_id'] ?? 'null'; ?>)"
                                     data-notification-id="<?php echo $notification['id']; ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="notification-icon me-2">
                                            <?php if ($notification['type'] === 'message'): ?>
                                                <i class="bi bi-chat-dots-fill text-primary"></i>
                                            <?php else: ?>
                                                <i class="bi bi-bell-fill text-warning"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <span class="fw-semibold small">
                                                        <?php echo htmlspecialchars(($notification['first_name'] . ' ' . $notification['last_name']) ?: 'Unknown Client'); ?>
                                                    </span>
                                                    <?php if (!$notification['is_read']): ?>
                                                        <span class="badge bg-danger badge-xs ms-1">New</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo date('M d, H:i', strtotime($notification['created_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem; line-height: 1.2; mb-1;">
                                                <?php
                                                if ($notification['type'] === 'system') {
                                                    echo 'New customer registration - ' . htmlspecialchars($notification['first_name'] . ' ' . $notification['last_name']);
                                                } else {
                                                    echo htmlspecialchars(substr($notification['last_message'] ?: 'New message', 0, 60));
                                                }
                                                ?>...
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted me-2" style="font-size: 0.7rem;">
                                                    <?php
                                                    if ($notification['type'] === 'system') {
                                                        echo 'System Alert';
                                                    } else {
                                                        echo $notification['message_count'] . ' messages';
                                                    }
                                                    ?>
                                                </small>
                                                <span class="badge bg-<?php echo $notification['type'] === 'message' ? 'primary' : 'warning'; ?> badge-xs">
                                                    <?php echo ucfirst($notification['type']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted fs-1 mb-3"></i>
                                <h6 class="text-muted">No notifications found</h6>
                                <p class="text-muted small">Messages from clients will appear here</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Conversation Area -->
        <div class="col-xl-7">
            <div class="form-card h-100">
                <?php if ($conversation): ?>
                    <!-- Client Info Card -->
                    <div class="card-header bg-white border-bottom-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="customer-avatar me-3">
                                        <i class="bi bi-person-circle text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1 text-primary">
                                            <?php echo htmlspecialchars($conversation['first_name'] . ' ' . $conversation['last_name']); ?>
                                        </h5>
                                        <div class="d-flex align-items-center gap-3">
                                            <small class="text-muted">
                                                <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($conversation['email']); ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($conversation['phone']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="conversation-stats">
                                    <small class="text-muted">
                                        <i class="bi bi-chat-dots me-1"></i><?php echo $conversation['total_messages'] ?? 0; ?> messages
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-calendar me-1"></i>Started <?php echo date('M d, Y', strtotime($conversation['created_at'] ?? $conversation['updated_at'])); ?>
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-info btn-sm" onclick="exportConversation(<?php echo $conversation_id; ?>)" title="Export Conversation">
                                    <i class="bi bi-download"></i>
                                </button>
                                <a href="/admin/customers/view_customer.php?id=<?php echo $conversation['client_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Area -->
                    <div class="card-body p-0">
                        <!-- Chat Header with Search -->
                        <div class="chat-header border-bottom p-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted fw-semibold">Conversation</small>
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" id="message-search" placeholder="Search messages..." style="width: 150px;">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="clearMessageSearch()" title="Clear Search">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="chat-messages" id="chat-messages" style="height: 350px; overflow-y: auto; padding: 1rem;">
                            <?php while ($message = $messages->fetch_assoc()): ?>
                                <div class="message <?php echo $message['sender_type'] === 'admin' ? 'admin-message' : 'client-message'; ?> message-item"
                                     data-message-id="<?php echo $message['id']; ?>">
                                    <div class="message-content">
                                        <div class="message-header d-flex justify-content-between align-items-center mb-1">
                                            <small class="message-sender fw-semibold">
                                                <?php
                                                if ($message['sender_type'] === 'admin') {
                                                    echo 'You (' . htmlspecialchars(isset($message['admin_name']) ? $message['admin_name'] : 'Admin') . ')';
                                                } else {
                                                    echo htmlspecialchars($message['first_name'] . ' ' . $message['last_name']);
                                                }
                                                ?>
                                            </small>
                                            <small class="message-time text-muted">
                                                <?php echo date('M d, H:i', strtotime($message['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="message-text">
                                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                            <?php if (!empty($message['attachment'])): ?>
                                                <div class="message-attachment mt-2">
                                                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $message['attachment'])): ?>
                                                        <img src="/<?php echo htmlspecialchars($message['attachment']); ?>" alt="Attachment" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">
                                                    <?php else: ?>
                                                        <a href="/<?php echo htmlspecialchars($message['attachment']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-paperclip me-1"></i>View Attachment
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>

                        <!-- Message Input -->
                        <div class="chat-input border-top p-3 bg-white">
                            <form id="message-form" onsubmit="sendMessage(event)" enctype="multipart/form-data">
                                <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                                <div class="input-group">
                                    <textarea class="form-control border-end-0" name="message" rows="2" placeholder="Type your professional reply..." style="resize: none;"></textarea>
                                    <div class="d-flex flex-column gap-1 p-2 bg-light border">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="send_email" id="sendEmailCheck" checked>
                                            <label class="form-check-label small" for="sendEmailCheck">
                                                Email
                                            </label>
                                        </div>
                                        <input type="file" class="form-control form-control-sm mb-1" name="attachment" id="attachment" accept="image/*,.pdf,.doc,.docx">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="bi bi-send-fill"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Messages are sent instantly and logged for quality assurance. You can attach images or documents.
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                   <!-- Conversations List -->
                   <div class="card-header bg-white border-bottom-0 pb-0">
                       <h5 class="mb-1">
                           <i class="bi bi-chat-dots-fill text-primary me-2"></i>
                           All Conversations
                       </h5>
                       <p class="text-muted small mb-0">Click on a conversation to view messages</p>
                   </div>
                   <div class="card-body p-0">
                       <div class="conversations-list" style="max-height: 500px; overflow-y: auto;">
                           <?php if ($all_conversations->num_rows > 0): ?>
                               <?php while ($conv = $all_conversations->fetch_assoc()): ?>
                                   <div class="conversation-item p-3 border-bottom"
                                        onclick="loadConversation(<?php echo $conv['id']; ?>)"
                                        style="cursor: pointer; transition: background-color 0.3s;">
                                       <div class="d-flex justify-content-between align-items-start">
                                           <div class="flex-grow-1">
                                               <div class="d-flex align-items-center mb-1">
                                                   <span class="fw-semibold">
                                                       <?php echo htmlspecialchars($conv['first_name'] . ' ' . $conv['last_name']); ?>
                                                   </span>
                                                   <?php if ($conv['unread_count'] > 0): ?>
                                                       <span class="badge bg-danger badge-xs ms-2"><?php echo $conv['unread_count']; ?> new</span>
                                                   <?php endif; ?>
                                               </div>
                                               <div class="text-muted small mb-1">
                                                   <?php echo htmlspecialchars(substr($conv['last_message'] ?: 'No messages yet', 0, 50)); ?>...
                                               </div>
                                               <div class="d-flex align-items-center">
                                                   <small class="text-muted me-2">
                                                       <?php echo $conv['total_messages']; ?> messages
                                                   </small>
                                                   <small class="text-muted">
                                                       <?php echo date('M d, H:i', strtotime($conv['updated_at'])); ?>
                                                   </small>
                                               </div>
                                           </div>
                                           <div class="text-end">
                                               <a href="?conversation=<?php echo $conv['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                   <i class="bi bi-chat-dots"></i>
                                               </a>
                                           </div>
                                       </div>
                                   </div>
                               <?php endwhile; ?>
                           <?php else: ?>
                               <div class="text-center py-5">
                                   <i class="bi bi-chat-dots text-muted fs-1 mb-3"></i>
                                   <h6 class="text-muted">No conversations yet</h6>
                                   <p class="text-muted small">Conversations will appear here when clients send messages</p>
                               </div>
                           <?php endif; ?>
                       </div>
                   </div>
               <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- New Conversation Modal -->
    <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="newConversationModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Start New Conversation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="newConversationForm">
                        <div class="mb-3">
                            <label for="customerSelect" class="form-label fw-semibold">Select Customer</label>
                            <select class="form-select" id="customerSelect" name="customer_id" required>
                                <option value="">Choose a customer...</option>
                                <?php
                                // Get all customers
                                $customer_query = $conn->query("SELECT id, first_name, last_name, email FROM customers_enhanced ORDER BY first_name, last_name");
                                while ($cust = $customer_query->fetch_assoc()) {
                                    echo "<option value='{$cust['id']}'>{$cust['first_name']} {$cust['last_name']} - {$cust['email']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="messageText" class="form-label fw-semibold">Message</label>
                            <textarea class="form-control" id="messageText" name="message" rows="4" placeholder="Type your message to the customer..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sendEmailCheck" name="send_email" checked>
                                <label class="form-check-label" for="sendEmailCheck">
                                    Send email notification to customer
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="sendNewMessage()">
                        <i class="bi bi-send-fill me-1"></i>Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="bi bi-bell-fill text-warning me-2"></i>Notification Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationModalBody">
                    <!-- Notification details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="startConversationBtn" style="display: none;" onclick="startConversation()">
                        <i class="bi bi-chat-dots me-1"></i>Start Conversation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.customer-avatar {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.conversation-stats {
    margin-top: 0.5rem;
}

.notification-item {
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 12px;
    margin: 3px 0;
    border: 1px solid transparent;
}

.notification-item:hover {
    background: linear-gradient(135deg, rgba(0,123,255,0.05), rgba(0,123,255,0.02));
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border-color: rgba(0,123,255,0.2);
}

.notification-item.unread {
    background: linear-gradient(135deg, rgba(220,53,69,0.08), rgba(220,53,69,0.04));
    border-left: 4px solid #dc3545;
    box-shadow: 0 2px 8px rgba(220,53,69,0.15);
}

.notification-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0,123,255,0.1);
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.notification-item:hover .notification-icon {
    background: rgba(0,123,255,0.2);
    transform: scale(1.1);
}

.badge-xs {
    font-size: 0.65rem;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
}

.chat-messages {
    background: #f8f9fa;
    position: relative;
}

.chat-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid #dee2e6;
}

.message {
    margin-bottom: 1.5rem;
    display: flex;
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.admin-message {
    justify-content: flex-end;
}

.client-message {
    justify-content: flex-start;
}

.message-content {
    max-width: 75%;
    padding: 1rem;
    border-radius: 18px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.admin-message .message-content {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-bottom-right-radius: 5px;
}

.client-message .message-content {
    background: white;
    border-bottom-left-radius: 5px;
    border: 1px solid #e9ecef;
}

.message-header {
    margin-bottom: 0.5rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.admin-message .message-header {
    border-bottom-color: rgba(255,255,255,0.3);
}

.message-sender {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.9);
}

.client-message .message-sender {
    color: #6c757d;
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.8;
}

.message-text {
    line-height: 1.5;
    word-wrap: break-word;
}

.chat-input {
    background: white;
    border-top: 2px solid #e9ecef;
}

.chat-input textarea {
    resize: none;
    border-right: none;
    border-radius: 20px 0 0 20px;
    padding: 0.75rem 1rem;
}

.chat-input textarea:focus {
    box-shadow: none;
    border-color: #007bff;
}

.chat-input .input-group {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.chat-input .d-flex {
    background: #f8f9fa;
    border-left: 1px solid #dee2e6;
}

.form-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 15px;
    overflow: hidden;
}

.highlighted-message {
    background: rgba(255,255,0,0.3) !important;
    border: 2px solid #ffc107;
}

#message-search {
    border-radius: 15px;
    border: 1px solid #dee2e6;
    transition: border-color 0.3s ease;
}

#message-search:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.conversations-list {
    max-height: 500px;
    overflow-y: auto;
}

.conversation-item {
    transition: background-color 0.3s ease;
    border-left: 3px solid transparent;
}

.conversation-item:hover {
    background-color: rgba(0,123,255,0.05);
    border-left-color: rgba(0,123,255,0.3);
}
</style>

<script>
function applyFilters() {
    const filter = document.getElementById('filter-select').value;
    const search = document.getElementById('search-input').value;

    const params = new URLSearchParams(window.location.search);
    params.set('filter', filter);
    params.set('search', search);

    window.location.search = params.toString();
}

function loadConversation(conversationId) {
    const params = new URLSearchParams(window.location.search);
    params.set('conversation', conversationId);

    window.location.search = params.toString();
}

function markAsRead(notificationId) {
    // Mark notification as read via AJAX
    fetch('/admin/api/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI to show notification as read
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                const badge = notificationItem.querySelector('.badge.bg-danger');
                if (badge) badge.remove();
            }
            // Update unread count
            updateUnreadCount();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function updateUnreadCount() {
    fetch('/admin/api/check_notifications.php')
    .then(response => response.json())
    .then(data => {
        const unreadBadge = document.getElementById('unread-badge');
        if (unreadBadge && data.unread_count !== undefined) {
            unreadBadge.textContent = data.unread_count;
        }
    })
    .catch(error => console.error('Error updating unread count:', error));
}

function sendMessage(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    fetch('/admin/api/send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the conversation
            location.reload();
        } else {
            alert('Error sending message: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    });
}

// Auto-scroll to bottom of chat
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});

function openNotificationModal(notificationId, type, referenceId) {
    // Fetch notification details
    fetch('/admin/api/get_notification_details.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ notification_id: notificationId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = data.notification;
            const messages = data.messages || [];
            const modalBody = document.getElementById('notificationModalBody');
            const startBtn = document.getElementById('startConversationBtn');

            let content = `
                <div class="notification-detail">
                    <div class="d-flex align-items-center mb-3">
                        <div class="notification-icon me-3">
                            ${type === 'message' ? '<i class="bi bi-chat-dots-fill text-primary"></i>' : '<i class="bi bi-bell-fill text-warning"></i>'}
                        </div>
                        <div>
                            <h6 class="mb-1">${notification.first_name} ${notification.last_name}</h6>
                            <small class="text-muted">${type === 'message' ? 'Client Message' : 'System Alert'}</small>
                        </div>
                    </div>`;

            if (type === 'message' && messages.length > 0) {
                content += `
                    <div class="mb-3">
                        <strong>Conversation:</strong>
                        <div class="conversation-preview mt-2" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px;">
                `;
                messages.forEach(message => {
                    const senderName = message.sender_type === 'admin' ?
                        (message.admin_name || 'Admin') :
                        `${message.first_name} ${message.last_name}`;
                    const isAdmin = message.sender_type === 'admin';
                    content += `
                        <div class="message-preview mb-2 ${isAdmin ? 'text-end' : ''}">
                            <small class="fw-semibold ${isAdmin ? 'text-primary' : 'text-success'}">${senderName}:</small>
                            <div class="message-text small ${isAdmin ? 'text-end' : ''}" style="margin-top: 2px;">
                                ${message.message.length > 100 ? message.message.substring(0, 100) + '...' : message.message}
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;">${new Date(message.created_at).toLocaleString()}</small>
                        </div>
                    `;
                });
                content += `
                        </div>
                    </div>`;
            } else {
                content += `
                    <div class="mb-3">
                        <strong>Details:</strong><br>
                        <p class="mt-2">${type === 'system' ?
                            `New customer registration - ${notification.first_name} ${notification.last_name}` :
                            notification.last_message || 'New message received'}</p>
                    </div>`;
            }

            content += `
                    <div class="row">
                        <div class="col-sm-6">
                            <small class="text-muted">Email: ${notification.email || 'N/A'}</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted">Phone: ${notification.phone || 'N/A'}</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Received: ${new Date(notification.created_at).toLocaleString()}</small>
                    </div>
                </div>
            `;

            modalBody.innerHTML = content;

            if (type === 'message') {
                startBtn.style.display = 'inline-block';
                startBtn.setAttribute('data-reference-id', referenceId);
            } else {
                startBtn.style.display = 'none';
            }

            // Mark as read
            markAsRead(notificationId);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('notificationModal'));
            modal.show();
        }
    })
    .catch(error => {
        console.error('Error loading notification details:', error);
    });
}

function startNewConversation() {
    const modal = new bootstrap.Modal(document.getElementById('newConversationModal'));
    modal.show();
}

function sendNewMessage() {
    const form = document.getElementById('newConversationForm');
    const formData = new FormData(form);
    const customerId = formData.get('customer_id');
    const message = formData.get('message').trim();


    if (!customerId || customerId === '' || !message) {
        alert('Please select a valid customer and enter a message.');
        return;
    }

    // First, check if conversation already exists
    fetch(`/admin/api/check_conversation.php?customer_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                // Conversation exists, send message to existing conversation
                formData.set('conversation_id', data.conversation_id);
                sendMessageToExistingConversation(formData);
            } else {
                // Create new conversation and send message
                createNewConversationAndSendMessage(formData);
            }
        })
        .catch(error => {
            console.error('Error checking conversation:', error);
            alert('Error checking conversation status');
        });
}

function sendMessageToExistingConversation(formData) {
    fetch('/admin/api/send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('newConversationModal')).hide();
            location.reload();
        } else {
            alert('Error sending message: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    });
}

function createNewConversationAndSendMessage(formData) {
    const customerId = formData.get('customer_id');
    const message = formData.get('message');
    const sendEmail = formData.get('send_email') === 'on';

    // Create conversation first
    const convData = new FormData();
    convData.append('customer_id', customerId);
    convData.append('message', message);
    convData.append('send_email', sendEmail ? 'on' : '');

    fetch('/admin/api/create_conversation.php', {
        method: 'POST',
        body: convData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('newConversationModal')).hide();
            location.reload();
        } else {
            alert('Error creating conversation: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating conversation');
    });
}

function startConversation() {
    const referenceId = document.getElementById('startConversationBtn').getAttribute('data-reference-id');
    if (referenceId) {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('notificationModal')).hide();
        // Load conversation
        loadConversation(referenceId);
    }
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        fetch('/admin/api/mark_all_notifications_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    const badge = item.querySelector('.badge.bg-danger');
                    if (badge) badge.remove();
                });
                updateUnreadCount();
                showToast('All notifications marked as read', 'success');
            } else {
                showToast('Error marking notifications as read', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error marking notifications as read', 'danger');
        });
    }
}

function refreshNotifications() {
    window.location.reload();
}

function exportConversation(conversationId) {
    const link = document.createElement('a');
    link.href = `/admin/api/export_conversation.php?conversation_id=${conversationId}`;
    link.download = `conversation_${conversationId}_${new Date().toISOString().slice(0, 10)}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    showToast('Conversation export started', 'info');
}

// Message search functionality
document.addEventListener('DOMContentLoaded', function() {
    const messageSearch = document.getElementById('message-search');
    if (messageSearch) {
        messageSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const messages = document.querySelectorAll('.message-item');

            messages.forEach(message => {
                const text = message.textContent.toLowerCase();
                if (searchTerm === '' || text.includes(searchTerm)) {
                    message.style.display = '';
                    if (searchTerm !== '') {
                        message.classList.add('highlighted-message');
                    } else {
                        message.classList.remove('highlighted-message');
                    }
                } else {
                    message.style.display = 'none';
                    message.classList.remove('highlighted-message');
                }
            });

            // Scroll to first highlighted message
            if (searchTerm !== '') {
                const firstHighlighted = document.querySelector('.highlighted-message');
                if (firstHighlighted) {
                    firstHighlighted.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // Auto-scroll to bottom of chat
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});

function clearMessageSearch() {
    const messageSearch = document.getElementById('message-search');
    if (messageSearch) {
        messageSearch.value = '';
        messageSearch.dispatchEvent(new Event('input'));
    }
}

function showToast(message, type = 'info') {
    // Create toast notification
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    if (!document.getElementById('toastContainer')) {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }

    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', toastHtml);
    const toast = new bootstrap.Toast(document.getElementById('toastContainer').lastElementChild);
    toast.show();

    setTimeout(() => {
        document.getElementById('toastContainer').lastElementChild.remove();
    }, 3000);
}
</script>

<?php include '../footer.php'; ?>