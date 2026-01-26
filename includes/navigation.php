<?php
include_once 'config.php';
?>

<!-- Header Start -->
<header class="bg-white shadow-sm">
    <div class="container-fluid px-5">
        <!-- Top Header -->
        <div class="d-none d-lg-block py-2 border-bottom">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <span class="text-muted">Call Us: </span>
                        <a href="tel:<?php echo SITE_PHONE; ?>" class="text-decoration-none ms-1 fw-bold"><?php echo SITE_PHONE; ?></a>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <span class="text-muted small"></span>
                </div>
                <div class="col-lg-4 text-end">
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <span class="text-muted me-2">Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
                        <span class="text-muted">|</span>
                        <a href="/pages/logout.php" class="text-decoration-none ms-2">Logout</a>
                    <?php else: ?>
                        <a href="/pages/login.php" class="text-decoration-none me-3">Login</a>
                        <span class="text-muted">|</span>
                        <a href="/pages/register.php" class="text-decoration-none ms-3">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="py-3">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="/index.php" class="d-flex align-items-center text-decoration-none">
                        <img src="/img/logo/logox.jpg" alt="<?php echo SITE_NAME; ?> Logo" class="me-3" style="height: 50px;">
                        <div>
                            <h5 class="mb-0 text-dark fw-bold"><?php echo SITE_NAME; ?></h5>
                            <small class="text-muted">LTD</small>
                        </div>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="col-lg-6 col-md-8 d-none d-md-block">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for spare parts, tools, accessories..." aria-label="Search">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Cart & Mobile Menu -->
                <div class="col-lg-3 col-md-12 col-6 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <!-- Cart Button -->
                        <button class="btn btn-outline-primary me-3 d-none d-lg-inline-flex align-items-center position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span id="cartSummary">0 Items - RWF 0.00</span>
                            <span id="cartBadge" class="badge bg-danger position-absolute top-0 start-100 translate-middle d-none">0</span>
                        </button>

                        <!-- Mobile Cart Button -->
                        <button class="btn btn-outline-primary me-2 d-lg-none position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                            <i class="fas fa-shopping-cart"></i>
                            <span id="cartBadgeMobile" class="badge bg-danger position-absolute top-0 start-100 translate-middle d-none">0</span>
                        </button>

                        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Search -->
            <div class="d-md-none mt-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search parts..." aria-label="Search">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="navbar navbar-expand-lg navbar-light bg-primary rounded mb-3">
            <div class="container-fluid px-0">
                <div class="collapse navbar-collapse" id="mobileNav">
                    <div class="navbar-nav mx-auto">
                        <?php foreach ($nav_menu as $item): ?>
                            <a href="<?php echo $item['url']; ?>" class="nav-link text-white fw-bold me-4 <?php echo (basename($_SERVER['PHP_SELF']) == $item['url']) ? 'active' : ''; ?>">
                                <?php echo $item['text']; ?>
                            </a>
                        <?php endforeach; ?>

                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <a href="/pages/my_account.php" class="nav-link text-white fw-bold me-4 <?php echo (basename($_SERVER['PHP_SELF']) == 'my_account.php') ? 'active' : ''; ?>">My Account</a>
                            <a href="/pages/order_history.php" class="nav-link text-white fw-bold me-4 <?php echo (basename($_SERVER['PHP_SELF']) == 'order_history.php') ? 'active' : ''; ?>">Order History</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
<!-- Header End -->

<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="cartModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="cartContent">
                    <!-- Cart content will be loaded here -->
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Your cart is empty</h5>
                        <p class="text-muted">Add some products to get started!</p>
                        <button class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='/pages/shop.php'">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-none" id="cartFooter">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-5">Total: <span id="cartTotal" class="text-primary">RWF 0.00</span></span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">
                            <i class="fas fa-shopping-bag me-1"></i>Continue Shopping
                        </button>
                        <a href="/pages/cart.php" class="btn btn-primary flex-fill">
                            <i class="fas fa-eye me-1"></i>View Cart
                        </a>
                        <a href="/pages/checkout.php" class="btn btn-success flex-fill">
                            <i class="fas fa-credit-card me-1"></i>Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cart JavaScript -->
<script>
// Global cart functions
function updateCartDisplay() {
    fetch('/api/get_cart.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart summary in header
                const cartSummary = document.getElementById('cartSummary');
                const cartBadge = document.getElementById('cartBadge');
                const cartBadgeMobile = document.getElementById('cartBadgeMobile');

                if (cartSummary) {
                    cartSummary.textContent = `${data.cart_count} Items - ${data.formatted_total}`;
                }

                // Update badges
                [cartBadge, cartBadgeMobile].forEach(badge => {
                    if (badge) {
                        if (data.cart_count > 0) {
                            badge.textContent = data.cart_count > 99 ? '99+' : data.cart_count;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                });

                // Update cart modal content
                updateCartModal(data);
            }
        })
        .catch(error => console.error('Error updating cart:', error));
}

function updateCartModal(data) {
    const cartContent = document.getElementById('cartContent');
    const cartFooter = document.getElementById('cartFooter');
    const cartTotal = document.getElementById('cartTotal');

    if (data.cart.length === 0) {
        cartContent.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Your cart is empty</h5>
                <p class="text-muted">Add some products to get started!</p>
                <button class="btn btn-primary" data-bs-dismiss="modal" onclick="window.location.href='/pages/shop.php'">
                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                </button>
            </div>
        `;
        cartFooter.classList.add('d-none');
    } else {
        let cartHtml = '<div class="cart-items">';
        data.cart.forEach((item, index) => {
            cartHtml += `
                <div class="cart-item d-flex align-items-center p-3 border-bottom">
                    <img src="${item.image}" alt="${item.name}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: contain;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${item.name}</h6>
                        <small class="text-muted d-block">${item.brand || ''} ${item.model || ''}</small>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <div class="quantity-controls d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="mx-2 fw-bold">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <span class="fw-bold text-primary">RWF ${item.subtotal.toLocaleString()}</span>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
        });
        cartHtml += '</div>';

        cartContent.innerHTML = cartHtml;
        cartFooter.classList.remove('d-none');
        if (cartTotal) {
            cartTotal.textContent = data.formatted_total;
        }
    }
}

function updateCartQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', newQuantity);

    fetch('/api/update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay();
            showToast('Cart updated successfully!', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to update cart', 'error');
    });
}

function removeFromCart(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 0);

    fetch('/api/update_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartDisplay();
            showToast('Item removed from cart!', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to remove item', 'error');
    });
}

function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed"
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', toastHtml);

    setTimeout(() => {
        const alert = document.querySelector('.alert:last-child');
        if (alert) alert.remove();
    }, 3000);
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
});
</script>