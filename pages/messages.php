<?php
include '../includes/config.php';

if (!isset($_SESSION['customer_id'])) { header('Location: login.php?redirect=messages.php'); exit(); }

$page_title = 'My Messages - SPARE XPRESS LTD';
$customer_id = $_SESSION['customer_id'];

$customer_stmt = $conn->prepare("SELECT * FROM customers_enhanced WHERE id = ?");
$customer_stmt->bind_param("i", $customer_id);
$customer_stmt->execute();
$customer = $customer_stmt->get_result()->fetch_assoc();
$customer_stmt->close();

if (!$customer) { session_destroy(); header('Location: login.php'); exit(); }

$conversations_query = "
    SELECT conv.*, COUNT(m.id) as message_count,
        COUNT(CASE WHEN m.sender_type = 'admin' AND m.created_at > COALESCE(cmr.last_read, '2000-01-01') THEN 1 END) as unread_count
    FROM conversations conv
    LEFT JOIN messages m ON conv.id = m.conversation_id
    LEFT JOIN (SELECT conversation_id, MAX(created_at) as last_read FROM messages WHERE sender_type = 'client' GROUP BY conversation_id) cmr ON conv.id = cmr.conversation_id
    WHERE conv.client_id = ?
    GROUP BY conv.id ORDER BY conv.updated_at DESC";
$conv_stmt = $conn->prepare($conversations_query);
$conv_stmt->bind_param("i", $customer_id);
$conv_stmt->execute();
$conversations = $conv_stmt->get_result();

$conversation_id = $_GET['conversation'] ?? null;
$conversation = null;
$messages = [];

if ($conversation_id) {
    $check_stmt = $conn->prepare("SELECT conv.*, c.first_name, c.last_name FROM conversations conv JOIN customers_enhanced c ON conv.client_id = c.id WHERE conv.id = ? AND conv.client_id = ?");
    $check_stmt->bind_param("ii", $conversation_id, $customer_id);
    $check_stmt->execute();
    $conversation = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();

    if ($conversation) {
        $msg_stmt = $conn->prepare("SELECT m.* FROM messages m WHERE m.conversation_id = ? ORDER BY m.created_at ASC");
        $msg_stmt->bind_param("i", $conversation_id);
        $msg_stmt->execute();
        $messages = $msg_stmt->get_result();
        $msg_stmt->close();
    }
}

include '../includes/header.php';
include '../includes/navigation.php';
?>

<!-- Page Hero -->
<div class="spx-page-hero">
    <div class="container position-relative">
        <h1 class="fw-bold mb-2"><i class="fas fa-comments me-2"></i>My Messages</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="my_account.php">My Account</a></li>
                <li class="breadcrumb-item active">Messages</li>
            </ol>
        </nav>
    </div>
</div>

<div class="spx-portal-wrap py-5">
    <div class="container">
        <div class="row g-4">

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="spx-sidebar">
                    <div class="spx-sidebar-header">
                        <div class="spx-sidebar-avatar"><i class="fas fa-user"></i></div>
                        <div class="spx-sidebar-name"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></div>
                        <div class="spx-sidebar-email"><?php echo htmlspecialchars($customer['email']); ?></div>
                    </div>
                    <nav class="spx-sidebar-nav">
                        <a href="my_account.php"><i class="fas fa-th-large"></i>Overview</a>
                        <a href="order_history.php"><i class="fas fa-shopping-bag"></i>Order History</a>
                        <a href="messages.php" class="active"><i class="fas fa-comments"></i>Messages</a>
                        <div class="spx-sidebar-divider"></div>
                        <a href="my_account.php?tab=profile"><i class="fas fa-user-edit"></i>Edit Profile</a>
                        <a href="my_account.php?logout=1" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i>Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Messages Panel -->
            <div class="col-lg-9">
                <div class="spx-panel" style="overflow:hidden;">
                    <div class="spx-panel-header">
                        <h5 class="spx-panel-title"><i class="fas fa-comments me-2 text-primary"></i>Support Conversations</h5>
                        <a href="contact.php" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>New Message</a>
                    </div>
                    <div class="row g-0" style="min-height:520px;">
                        <!-- Conversations List -->
                        <div class="col-md-4 spx-chat-sidebar border-end">
                            <?php if ($conversations->num_rows > 0): ?>
                                <?php while ($conv = $conversations->fetch_assoc()): ?>
                                    <div class="spx-chat-item <?php echo ($conversation_id == $conv['id']) ? 'active' : ''; ?>"
                                         onclick="window.location='?conversation=<?php echo $conv['id']; ?>'">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="spx-chat-item-title">Conversation #<?php echo $conv['id']; ?></div>
                                            <?php if ($conv['unread_count'] > 0): ?>
                                                <span class="badge bg-danger"><?php echo $conv['unread_count']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="spx-chat-item-preview"><?php echo htmlspecialchars(substr($conv['last_message'] ?? 'No messages yet', 0, 45)); ?></div>
                                        <small class="text-muted"><?php echo date('M d, H:i', strtotime($conv['updated_at'])); ?></small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5 px-3">
                                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                    <p class="text-muted small mb-3">No conversations yet. Send us a message from the contact page.</p>
                                    <a href="contact.php" class="btn btn-primary btn-sm">Contact Us</a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Messages Area -->
                        <div class="col-md-8 d-flex flex-column">
                            <?php if ($conversation): ?>
                                <!-- Chat Header -->
                                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-700" style="font-weight:700">Support Team</div>
                                        <small class="text-muted">Started <?php echo date('M d, Y', strtotime($conversation['created_at'])); ?></small>
                                    </div>
                                    <span class="badge bg-success">Active</span>
                                </div>
                                <!-- Messages -->
                                <div class="spx-chat-messages flex-grow-1" id="messages-container">
                                    <?php while ($msg = $messages->fetch_assoc()): ?>
                                        <div class="spx-msg <?php echo $msg['sender_type'] === 'client' ? 'client' : 'admin'; ?>">
                                            <div class="spx-msg-bubble">
                                                <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                                <?php if (!empty($msg['attachment'])): ?>
                                                    <div class="mt-2">
                                                        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $msg['attachment'])): ?>
                                                            <img src="/<?php echo htmlspecialchars($msg['attachment']); ?>" class="img-fluid rounded" style="max-width:180px;">
                                                        <?php else: ?>
                                                            <a href="/<?php echo htmlspecialchars($msg['attachment']); ?>" target="_blank" class="btn btn-sm btn-outline-light">
                                                                <i class="fas fa-paperclip me-1"></i>Attachment
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="spx-msg-time"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?> &middot; <?php echo $msg['sender_type'] === 'client' ? 'You' : 'Support'; ?></span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                                <!-- Reply Form -->
                                <div class="spx-chat-input-area">
                                    <form id="reply-form" onsubmit="sendReply(event)" enctype="multipart/form-data">
                                        <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                                        <div class="d-flex gap-2">
                                            <textarea class="form-control" name="message" rows="2" placeholder="Type your reply..." required style="resize:none;"></textarea>
                                            <div class="d-flex flex-column gap-1">
                                                <label class="btn btn-outline-secondary btn-sm" title="Attach file">
                                                    <i class="fas fa-paperclip"></i>
                                                    <input type="file" name="attachment" accept="image/*,.pdf,.doc,.docx" class="d-none">
                                                </label>
                                                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-4">
                                    <div>
                                        <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Select a conversation</h5>
                                        <p class="text-muted small">Choose from the list to view messages</p>
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

<script>
function sendReply(e) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    fetch('/api/send_client_reply.php', { method: 'POST', body: new FormData(form) })
        .then(r => r.json())
        .then(data => { if (data.success) location.reload(); else alert('Error: ' + data.message); })
        .catch(() => alert('Error sending reply'))
        .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i>'; });
}
document.addEventListener('DOMContentLoaded', function() {
    const c = document.getElementById('messages-container');
    if (c) c.scrollTop = c.scrollHeight;
});
</script>

<?php include '../includes/footer.php'; ?>
