<?php
// Session check must happen BEFORE any HTML output
include '../includes/client_session_check.php';

$page_title = 'Shopping Cart - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';
include '../includes/toast_notifications.php';
include '../includes/wishlist.php';

// Get cart data
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_total = 0;
$cart_count = 0;
$deposit_required = 0;

foreach ($cart as $item) {
    $cart_total += $item['subtotal'];
    $cart_count += $item['quantity'];

    // Calculate deposit for special order items (50% of total)
    if ($item['stock'] == 0) {
        $deposit_required += $item['subtotal'] * 0.5;
    }
}
?>

<!-- Page Header Start -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="display-4 text-dark fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    Shopping Cart
                </h1>
                <nav aria-label="breadcrumb" class="wow fadeInUp" data-wow-delay="0.3s">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="/pages/shop.php">Shop</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Page Header End -->

<!-- Cart Page Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <?php if (empty($cart)): ?>
            <!-- Empty Cart -->
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h2 class="text-muted mb-3">Your cart is empty</h2>
                <p class="text-muted mb-4">Looks like you haven't added any items to your cart yet.</p>
                <a href="/pages/shop.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                </a>
            </div>
        <?php else: ?>
            <!-- Cart Items -->
            <div class="table-responsive mb-5">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" colspan="2">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                        <?php foreach ($cart as $index => $item): ?>
                            <tr data-product-id="<?php echo $item['id']; ?>" class="cart-item-row">
                                <td style="width: 80px;">
                                    <div class="position-relative">
                                        <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                             class="img-fluid rounded cart-item-image" style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             onerror="this.src='/img/no-image.png'" onclick="viewProduct(<?php echo $item['id']; ?>)">
                                        <div class="image-hover-overlay">
                                            <i class="fas fa-search-plus text-white" onclick="zoomCartImage('<?php echo $item['image']; ?>', '<?php echo htmlspecialchars($item['name']); ?>')"></i>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="product-info">
                                        <h6 class="mb-1 product-name" style="cursor: pointer;" onclick="viewProduct(<?php echo $item['id']; ?>)">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </h6>
                                        <div class="product-meta mb-2">
                                            <?php if ($item['brand']): ?>
                                                <span class="badge bg-primary badge-sm me-1">
                                                    <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($item['brand']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($item['model']): ?>
                                                <span class="badge bg-secondary badge-sm me-1">
                                                    <i class="fas fa-car me-1"></i><?php echo htmlspecialchars($item['model']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($item['category']): ?>
                                                <span class="badge bg-info badge-sm">
                                                    <i class="fas fa-cogs me-1"></i><?php echo htmlspecialchars($item['category']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="stock-status">
                                            <span class="badge <?php echo $item['stock'] > 0 ? 'bg-success' : 'bg-warning text-dark'; ?> badge-sm">
                                                <i class="fas fa-<?php echo $item['stock'] > 0 ? 'check-circle' : 'exclamation-triangle'; ?> me-1"></i>
                                                <?php echo $item['stock'] > 0 ? 'In Stock' : 'Special Order'; ?>
                                            </span>
                                            <?php if ($item['stock'] > 0 && $item['stock'] <= 5): ?>
                                                <small class="text-muted ms-1">(<?php echo $item['stock']; ?> left)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="price-info">
                                        <span class="fw-bold text-primary unit-price">RWF <?php echo number_format($item['price'], 0, '.', ','); ?></span>
                                        <?php if ($item['stock'] == 0): ?>
                                            <div class="small text-warning mt-1">
                                                <i class="fas fa-info-circle me-1"></i>50% deposit required
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="width: 180px;">
                                    <div class="quantity-controls">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button"
                                                    onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>, this)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" class="form-control text-center quantity-input"
                                                   value="<?php echo $item['quantity']; ?>" min="1"
                                                   max="<?php echo $item['stock'] > 0 ? $item['stock'] : 99; ?>"
                                                   onchange="updateQuantity(<?php echo $item['id']; ?>, this.value, this)"
                                                   data-original-value="<?php echo $item['quantity']; ?>">
                                            <button class="btn btn-outline-secondary btn-sm quantity-btn" type="button"
                                                    onclick="updateQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>, this)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="quantity-feedback mt-1" style="display: none;">
                                            <small class="text-success"><i class="fas fa-check me-1"></i>Updated</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="subtotal-info">
                                        <span class="fw-bold subtotal text-primary">RWF <?php echo number_format($item['subtotal'], 0, '.', ','); ?></span>
                                        <?php if ($item['stock'] == 0): ?>
                                            <div class="small text-warning">
                                                Deposit: RWF <?php echo number_format($item['subtotal'] * 0.5, 0, '.', ','); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <div class="btn-group-vertical btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" onclick="viewProduct(<?php echo $item['id']; ?>)"
                                                    title="View Product Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm btn-wishlist"
                                                    data-product-id="<?php echo $item['id']; ?>"
                                                    onclick="moveToWishlist(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>')"
                                                    title="Move to Wishlist">
                                                <i class="far fa-heart"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="removeItem(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>')"
                                                    title="Remove from Cart">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Cart Summary -->
            <div class="row g-4 justify-content-end">
                <div class="col-lg-8">
                    <!-- Continue Shopping -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <a href="/pages/shop.php" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                        </a>
                        <button class="btn btn-outline-secondary" onclick="clearCart()">
                            <i class="fas fa-trash me-2"></i>Clear Cart
                        </button>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="bg-light rounded p-4 shadow-sm">
                        <h4 class="mb-4">Cart Summary</h4>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Subtotal (<?php echo $cart_count; ?> items)</span>
                            <span class="fw-bold" id="cartSubtotal">RWF <?php echo number_format($cart_total, 0, '.', ','); ?></span>
                        </div>

                        <?php if ($deposit_required > 0): ?>
                            <div class="d-flex justify-content-between mb-3 text-warning">
                                <span>Deposit Required (50%)</span>
                                <span class="fw-bold" id="depositAmount">RWF <?php echo number_format($deposit_required, 0, '.', ','); ?></span>
                            </div>
                            <div class="alert alert-warning py-2">
                                <small><i class="fas fa-info-circle me-1"></i>Special order items require 50% upfront deposit</small>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Shipping</span>
                            <span class="text-muted">Calculated at checkout</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0">Total</span>
                            <span class="h5 mb-0 text-primary fw-bold" id="cartTotal">RWF <?php echo number_format($cart_total, 0, '.', ','); ?></span>
                        </div>

                        <a href="/pages/checkout.php" class="btn btn-primary w-100 btn-lg mb-3">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>

                        <div class="text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>Secure checkout • 
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Cart Page End -->

<style>
/* Enhanced Cart Styles */
.cart-item-row {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.cart-item-row:hover {
    background-color: #f8f9fa;
    border-left-color: #007bff;
    transform: translateX(2px);
}

.cart-item-image {
    transition: transform 0.3s ease;
    border: 1px solid #e9ecef;
}

.cart-item-image:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.image-hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 6px;
    cursor: pointer;
}

.cart-item-image:hover + .image-hover-overlay,
.image-hover-overlay:hover {
    opacity: 1;
}

.product-info .product-name {
    color: #2d3748;
    transition: color 0.3s ease;
}

.product-info .product-name:hover {
    color: #007bff;
}

.badge-sm {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.quantity-controls {
    position: relative;
}

.quantity-input {
    max-width: 60px;
    font-weight: 600;
    text-align: center;
    border-left: none;
    border-right: none;
}

.quantity-btn {
    border-radius: 0;
    min-width: 30px;
}

.quantity-btn:first-child {
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
}

.quantity-btn:last-child {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

.quantity-feedback {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 10;
}

.subtotal-info {
    min-width: 100px;
}

.unit-price {
    font-size: 0.95rem;
}

.action-buttons .btn {
    margin-bottom: 2px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
    min-width: 35px;
}

.btn-wishlist.active {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
    color: white !important;
}

.btn-wishlist.active i {
    color: white !important;
}

/* Enhanced Table Header */
.table thead th {
    border-top: none;
    font-weight: 700;
    color: #2d3748;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

/* Enhanced Cart Summary */
.checkout-summary {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border: 1px solid #e9ecef;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.summary-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 1.5rem;
    margin: -1px -1px 0 -1px;
}

.summary-header h4 {
    margin: 0;
    font-weight: 700;
}

.order-totals {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 1rem;
}

.order-totals .d-flex {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.order-totals .d-flex:last-child {
    border-bottom: none;
    font-size: 1.25rem;
    font-weight: 800;
    color: #007bff;
}

/* Enhanced Empty Cart */
.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    border: 2px dashed #dee2e6;
}

.empty-cart .fa-shopping-cart {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.empty-cart h2 {
    color: #2d3748;
    margin-bottom: 1rem;
}

.empty-cart p {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

/* Loading States */
.cart-item-row.updating {
    opacity: 0.7;
    pointer-events: none;
}

.cart-item-row.updating .quantity-controls::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.cart-item-row.updating .quantity-controls::after {
    background: rgba(0,123,255,0.1);
}

/* Success Animations */
@keyframes quantityUpdate {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.quantity-input.updated {
    animation: quantityUpdate 0.4s ease;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

/* Image Zoom Modal */
.cart-image-zoom-modal .modal-dialog {
    max-width: 600px;
}

.cart-image-zoom-modal .modal-body {
    padding: 0;
    text-align: center;
    background: #000;
}

.cart-image-zoom-modal img {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
}

/* Mobile Responsive Enhancements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }

    .cart-item-row {
        padding: 1rem 0.5rem;
    }

    .cart-item-image {
        width: 50px;
        height: 50px;
    }

    .product-info .product-name {
        font-size: 0.9rem;
    }

    .quantity-input {
        font-size: 0.9rem;
        max-width: 50px;
    }

    .action-buttons .btn-group-vertical {
        flex-direction: horizontal;
    }

    .action-buttons .btn {
        margin-bottom: 0;
        margin-right: 2px;
    }

    .checkout-summary {
        margin-top: 2rem;
    }
}

@media (max-width: 576px) {
    .table th,
    .table td {
        padding: 0.5rem 0.25rem;
    }

    .cart-item-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .cart-item-row td {
        border: none;
        padding: 0.25rem 0;
    }

    .cart-item-row td:first-child {
        width: 100%;
        text-align: center;
    }

    .product-info {
        margin-left: 0;
        margin-top: 0.5rem;
    }

    .quantity-controls {
        align-self: center;
        margin: 0.5rem 0;
    }

    .action-buttons {
        align-self: center;
    }
}

/* Progress Indicators */
.cart-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.cart-loading-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<script>
// Enhanced Cart Management System
let cartUpdateTimeout;
let isUpdating = false;

// Enhanced Quantity Update with Inline Feedback
function updateQuantity(productId, newQuantity, buttonElement = null) {
    newQuantity = parseInt(newQuantity);
    if (newQuantity < 1 || isUpdating) return;

    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
    const quantityInput = row.querySelector('.quantity-input');
    const originalValue = parseInt(quantityInput.dataset.originalValue);

    // Prevent updates if value hasn't changed
    if (newQuantity === originalValue) return;

    // Show loading state
    isUpdating = true;
    row.classList.add('updating');

    // Clear any existing timeout
    clearTimeout(cartUpdateTimeout);

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQuantity);
    formData.append('update', 'true');

    fetch('/api/update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the row with new data
            updateCartRow(row, data.cart_item);

            // Update cart summary
            updateCartSummary(data.cart_summary);

            // Update cart count in navbar
            updateCartCount(data.cart_count);

            // Show success feedback
            showQuantityUpdateFeedback(quantityInput, 'success');

            // Update original value
            quantityInput.dataset.originalValue = newQuantity;

            showSuccessToast(`Quantity updated to ${newQuantity}`, 'Cart Updated');
        } else {
            // Revert to original value
            quantityInput.value = originalValue;
            showQuantityUpdateFeedback(quantityInput, 'error');
            showErrorToast(data.message || 'Failed to update quantity');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert to original value
        quantityInput.value = originalValue;
        showQuantityUpdateFeedback(quantityInput, 'error');
        showErrorToast('Failed to update cart. Please try again.');
    })
    .finally(() => {
        isUpdating = false;
        row.classList.remove('updating');
    });
}

// Update cart row with new data
function updateCartRow(row, cartItem) {
    // Update subtotal
    const subtotalElement = row.querySelector('.subtotal');
    if (subtotalElement) {
        subtotalElement.textContent = `RWF ${cartItem.subtotal.toLocaleString()}`;
    }

    // Update quantity input
    const quantityInput = row.querySelector('.quantity-input');
    if (quantityInput) {
        quantityInput.value = cartItem.quantity;
        quantityInput.dataset.originalValue = cartItem.quantity;
    }

    // Add visual feedback
    row.style.transform = 'scale(1.01)';
    setTimeout(() => {
        row.style.transform = 'scale(1)';
    }, 200);
}

// Update cart summary
function updateCartSummary(summary) {
    // Update subtotal
    const subtotalElement = document.getElementById('cartSubtotal');
    if (subtotalElement) {
        subtotalElement.textContent = `RWF ${summary.subtotal.toLocaleString()}`;
    }

    // Update deposit amount if applicable
    const depositElement = document.getElementById('depositAmount');
    if (depositElement && summary.deposit_required > 0) {
        depositElement.textContent = `RWF ${summary.deposit_required.toLocaleString()}`;
    }

    // Update final total
    const finalTotalElement = document.getElementById('cartTotal');
    if (finalTotalElement) {
        finalTotalElement.textContent = `RWF ${summary.final_total.toLocaleString()}`;
    }

    // Update item count
    const itemCountElements = document.querySelectorAll('.item-count');
    itemCountElements.forEach(element => {
        element.textContent = summary.item_count;
    });
}

// Show quantity update feedback
function showQuantityUpdateFeedback(inputElement, type) {
    const feedbackElement = inputElement.closest('.quantity-controls').querySelector('.quantity-feedback');

    if (feedbackElement) {
        feedbackElement.style.display = 'block';

        if (type === 'success') {
            feedbackElement.innerHTML = '<small class="text-success"><i class="fas fa-check me-1"></i>Updated</small>';
            inputElement.classList.add('updated');
        } else {
            feedbackElement.innerHTML = '<small class="text-danger"><i class="fas fa-times me-1"></i>Failed</small>';
            inputElement.classList.add('is-invalid');
        }

        // Hide feedback after 2 seconds
        setTimeout(() => {
            feedbackElement.style.display = 'none';
            inputElement.classList.remove('updated', 'is-invalid');
        }, 2000);
    }
}

// Enhanced Remove Item with Confirmation
function removeItem(productId, productName = '') {
    const confirmMessage = productName
        ? `Are you sure you want to remove "${productName}" from your cart?`
        : 'Are you sure you want to remove this item from your cart?';

    if (!confirm(confirmMessage)) {
        return;
    }

    const row = document.querySelector(`tr[data-product-id="${productId}"]`);

    // Show loading state
    if (row) {
        row.style.opacity = '0.5';
        row.style.pointerEvents = 'none';
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('remove', 'true');

    fetch('/api/update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate row removal
            if (row) {
                row.style.transform = 'translateX(-100%)';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    updateCartSummary(data.cart_summary);
                    updateCartCount(data.cart_count);

                    // Check if cart is empty
                    const remainingRows = document.querySelectorAll('.cart-item-row');
                    if (remainingRows.length === 0) {
                        location.reload(); // Reload to show empty cart state
                    }
                }, 300);
            }

            showSuccessToast('Item removed from cart', 'Removed');
        } else {
            // Restore row state
            if (row) {
                row.style.opacity = '1';
                row.style.pointerEvents = 'auto';
            }
            showErrorToast(data.message || 'Failed to remove item');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Restore row state
        if (row) {
            row.style.opacity = '1';
            row.style.pointerEvents = 'auto';
        }
        showErrorToast('Failed to remove item. Please try again.');
    });
}

// Move Item to Wishlist
function moveToWishlist(productId, productName = '') {
    if (!confirm(`Move "${productName}" to your wishlist?`)) {
        return;
    }

    // First add to wishlist
    if (typeof wishlistManager !== 'undefined') {
        wishlistManager.addItem(productId);
    }

    // Then remove from cart
    removeItem(productId, productName);
}

// Enhanced Clear Cart
function clearCart() {
    const itemCount = document.querySelectorAll('.cart-item-row').length;

    if (itemCount === 0) {
        showInfoToast('Your cart is already empty', 'No Items');
        return;
    }

    const confirmMessage = `Are you sure you want to remove all ${itemCount} item${itemCount > 1 ? 's' : ''} from your cart?`;

    if (!confirm(confirmMessage)) {
        return;
    }

    // Show loading overlay
    showCartLoadingOverlay();

    fetch('/api/clear_cart.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessToast('Cart cleared successfully', 'Cart Cleared');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            hideCartLoadingOverlay();
            showErrorToast(data.message || 'Failed to clear cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        hideCartLoadingOverlay();
        showErrorToast('Failed to clear cart. Please try again.');
    });
}

// Image Zoom for Cart Items
function zoomCartImage(imageSrc, productName) {
    const modalHtml = `
        <div class="modal fade cart-image-zoom-modal" id="cartImageZoomModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${productName}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <img src="${imageSrc}" alt="${productName}" class="img-fluid" onerror="this.src='/img/no-image.png'">
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('cartImageZoomModal'));
    modal.show();

    document.getElementById('cartImageZoomModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// View Product
function viewProduct(productId) {
    window.location.href = `/pages/single.php?id=${productId}`;
}

// Loading Overlay Functions
function showCartLoadingOverlay() {
    const overlay = document.createElement('div');
    overlay.className = 'cart-loading-overlay';
    overlay.id = 'cartLoadingOverlay';
    overlay.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="sr-only">Clearing cart...</span>
            </div>
            <h5>Clearing Cart...</h5>
        </div>
    `;
    document.body.appendChild(overlay);
}

function hideCartLoadingOverlay() {
    const overlay = document.getElementById('cartLoadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Enhanced Cart Count Update
function updateCartCount(count) {
    const cartElements = document.querySelectorAll('.cart-count');
    cartElements.forEach(element => {
        element.textContent = count;

        // Add animation for count changes
        element.style.transform = 'scale(1.2)';
        setTimeout(() => {
            element.style.transform = 'scale(1)';
        }, 200);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load initial cart count
    fetch('/api/get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
            }
        })
        .catch(error => console.error('Error loading cart:', error));

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + Delete to clear cart
        if ((e.ctrlKey || e.metaKey) && e.key === 'Delete') {
            e.preventDefault();
            if (confirm('Clear entire cart?')) {
                clearCart();
            }
        }
    });

    // Add quantity input validation
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            const value = parseInt(this.value);
            const max = parseInt(this.getAttribute('max'));
            const min = parseInt(this.getAttribute('min'));

            if (value > max) {
                this.value = max;
                showWarningToast(`Maximum quantity is ${max}`, 'Quantity Limit');
            } else if (value < min) {
                this.value = min;
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
