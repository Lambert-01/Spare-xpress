<?php
// Client Messages Portal - SPARE XPRESS LTD
include '../includes/config.php';

// Check if customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php?redirect=messages.php');
    exit();
}

$page_title = 'My Messages - SPARE XPRESS LTD';

// Get customer data
$customer_id = $_SESSION['customer_id'];
$customer_query = "SELECT * FROM customers_enhanced WHERE id = ?";
$customer_stmt = $conn->prepare($customer_query);
$customer_stmt->bind_param("i", $customer_id);
$customer_stmt->execute();
$customer = $customer_stmt->get_result()->fetch_assoc();

if (!$customer) {
    // Fallback to customers_enhanced if not in enhanced
    $customer_query = "SELECT * FROM customers_enhanced WHERE id = ?";
    $customer_stmt = $conn->prepare($customer_query);
    $customer_stmt->bind_param("i", $customer_id);
    $customer_stmt->execute();
    $customer = $customer_stmt->get_result()->fetch_assoc();

    if (!$customer) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

// Get conversations for this customer
$conversations_query = "
    SELECT
        conv.*,
        COUNT(m.id) as message_count,
        COUNT(CASE WHEN m.sender_type = 'admin' AND m.created_at > COALESCE(cmr.last_read, '2000-01-01') THEN 1 END) as unread_count
    FROM conversations conv
    LEFT JOIN messages m ON conv.id = m.conversation_id
    LEFT JOIN (
        SELECT conversation_id, MAX(created_at) as last_read
        FROM messages
        WHERE sender_type = 'client'
        GROUP BY conversation_id
    ) cmr ON conv.id = cmr.conversation_id
    WHERE conv.client_id = ?
    GROUP BY conv.id
    ORDER BY conv.updated_at DESC
";

$conv_stmt = $conn->prepare($conversations_query);
$conv_stmt->bind_param("i", $customer_id);
$conv_stmt->execute();
$conversations = $conv_stmt->get_result();

// Get selected conversation
$conversation_id = $_GET['conversation'] ?? null;
$conversation = null;
$messages = [];

if ($conversation_id) {
    // Verify conversation belongs to customer
    $conv_check = "SELECT conv.*, c.first_name, c.last_name, c.email, c.phone
                   FROM conversations conv
                   JOIN customers_enhanced c ON conv.client_id = c.id
                   WHERE conv.id = ? AND conv.client_id = ?";
    $check_stmt = $conn->prepare($conv_check);
    $check_stmt->bind_param("ii", $conversation_id, $customer_id);
    $check_stmt->execute();
    $conversation = $check_stmt->get_result()->fetch_assoc();

    if ($conversation) {
        // Get messages
        $msg_query = "
            SELECT m.*, c.first_name, c.last_name
            FROM messages m
            LEFT JOIN customers_enhanced c ON m.sender_type = 'client'
            WHERE m.conversation_id = ?
            ORDER BY m.created_at ASC
        ";
        $msg_stmt = $conn->prepare($msg_query);
        $msg_stmt->bind_param("i", $conversation_id);
        $msg_stmt->execute();
        $messages = $msg_stmt->get_result();
    }
}

include '../includes/header.php';
include '../includes/navigation.php';
?>

<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="mb-0">
                            <i class="bi bi-chat-dots-fill me-2"></i>My Messages
                        </h3>
                        <p class="mb-0 mt-2">Communicate with SPARE XPRESS support team</p>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0 h-100">
                            <!-- Conversations List -->
                            <div class="col-md-4 border-end">
                                <div class="p-3 border-bottom bg-light">
                                    <h5 class="mb-0">
                                        <i class="bi bi-list-ul me-2"></i>Conversations
                                    </h5>
                                </div>
                                <div class="conversations-list" style="max-height: 600px; overflow-y: auto;">
                                    <?php if ($conversations->num_rows > 0): ?>
                                        <?php while ($conv = $conversations->fetch_assoc()): ?>
                                            <div class="conversation-item p-3 border-bottom <?php echo ($conversation_id == $conv['id']) ? 'active bg-primary bg-opacity-10' : ''; ?>"
                                                 onclick="loadConversation(<?php echo $conv['id']; ?>)">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="fw-semibold">
                                                        Conversation #<?php echo $conv['id']; ?>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?php echo date('M d, H:i', strtotime($conv['updated_at'])); ?>
                                                    </small>
                                                </div>
                                                <div class="text-muted small mb-1">
                                                    <?php echo htmlspecialchars(substr($conv['last_message'], 0, 40)); ?>...
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <?php echo $conv['message_count']; ?> messages
                                                    </small>
                                                    <?php if ($conv['unread_count'] > 0): ?>
                                                        <span class="badge bg-danger"><?php echo $conv['unread_count']; ?> new</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-chat-dots text-muted fs-1 mb-3"></i>
                                            <h6 class="text-muted">No conversations yet</h6>
                                            <p class="text-muted small">Send us a message from the contact page</p>
                                            <a href="contact.php" class="btn btn-primary btn-sm">Contact Us</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div class="col-md-8">
                                <?php if ($conversation): ?>
                                    <!-- Conversation Header -->
                                    <div class="p-3 border-bottom bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-0">
                                                    <i class="bi bi-person-circle me-2"></i>
                                                    Conversation with Support
                                                </h5>
                                                <small class="text-muted">
                                                    Started <?php echo date('M d, Y', strtotime($conversation['created_at'])); ?>
                                                </small>
                                            </div>
                                            <a href="contact.php" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-plus-circle me-1"></i>New Message
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Messages -->
                                    <div class="messages-container p-3" id="messages-container" style="height: 400px; overflow-y: auto;">
                                        <?php while ($message = $messages->fetch_assoc()): ?>
                                            <div class="message <?php echo $message['sender_type'] === 'client' ? 'client-message' : 'admin-message'; ?> mb-3">
                                                <div class="message-content">
                                                    <div class="message-text">
                                                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                    </div>
                                                    <div class="message-time">
                                                        <?php echo date('M d, H:i', strtotime($message['created_at'])); ?>
                                                        <span class="message-sender">
                                                            <?php echo $message['sender_type'] === 'client' ? 'You' : 'Support'; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <!-- Reply Form -->
                                    <div class="p-3 border-top">
                                        <form id="reply-form" onsubmit="sendReply(event)">
                                            <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                                            <div class="input-group">
                                                <textarea class="form-control" name="message" rows="3" placeholder="Type your reply..." required></textarea>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-send-fill"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <!-- Empty State -->
                                    <div class="text-center py-5 h-100 d-flex align-items-center justify-content-center">
                                        <div>
                                            <i class="bi bi-chat-dots text-muted fs-1 mb-3"></i>
                                            <h6 class="text-muted">Select a conversation</h6>
                                            <p class="text-muted small">Choose a conversation from the list to view messages</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.conversation-item {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.conversation-item:hover {
    background-color: rgba(0,123,255,0.05);
}

.conversation-item.active {
    background-color: rgba(0,123,255,0.1);
    border-left: 3px solid #007bff;
}

.messages-container {
    background: #f8f9fa;
}

.message {
    display: flex;
    margin-bottom: 1rem;
}

.client-message {
    justify-content: flex-end;
}

.admin-message {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 0.75rem;
    border-radius: 15px;
    position: relative;
}

.client-message .message-content {
    background: #007bff;
    color: white;
    border-bottom-right-radius: 5px;
}

.admin-message .message-content {
    background: white;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.message-time {
    font-size: 0.75rem;
    opacity: 0.7;
    margin-top: 0.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.message-sender {
    font-weight: 500;
}

#reply-form textarea {
    resize: none;
    border-right: none;
}

#reply-form textarea:focus {
    border-color: #007bff;
    box-shadow: none;
}

@media (max-width: 768px) {
    .conversations-list {
        max-height: 300px;
    }

    .messages-container {
        height: 300px;
    }
}
</style>

<script>
function loadConversation(conversationId) {
    window.location.search = `?conversation=${conversationId}`;
}

function sendReply(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

    fetch('/api/send_client_reply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page to show new message
            location.reload();
        } else {
            alert('Error sending reply: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending reply');
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>

<?php include '../includes/footer.php'; ?>