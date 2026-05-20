<?php
include_once 'config.php';
?>

<!-- Header Start -->
<header class="bg-white shadow-sm">
    <div class="container-fluid px-4 px-lg-5">
        <!-- Top Bar -->
        <div class="spx-topbar d-none d-lg-block">
            <div class="container-fluid px-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <a href="tel:<?php echo SITE_PHONE; ?>"><i class="fas fa-phone me-1"></i><?php echo SITE_PHONE; ?></a>
                        <span class="divider">|</span>
                        <a href="mailto:info@sparexpressltd.com"><i class="fas fa-envelope me-1"></i>info@sparexpressltd.com</a>
                        <span class="divider">|</span>
                        <span style="color:rgba(255,255,255,.75)"><i class="fas fa-clock me-1"></i>Mon–Sat: 8:00 AM – 6:00 PM</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="https://wa.me/250792865114" target="_blank"><i class="fab fa-whatsapp me-1"></i>WhatsApp</a>
                        <span class="divider">|</span>
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <span style="color:rgba(255,255,255,.75)">Hi, <?php echo htmlspecialchars(explode(' ', $_SESSION['customer_name'])[0]); ?></span>
                            <span class="divider">|</span>
                            <a href="/pages/my_account.php">My Account</a>
                            <span class="divider">|</span>
                            <a href="/pages/logout.php">Logout</a>
                        <?php else: ?>
                            <a href="/pages/login.php">Login</a>
                            <span class="divider">|</span>
                            <a href="/pages/register.php">Register</a>
                        <?php endif; ?>
                    </div>
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
                    <form class="input-group spx-header-search" action="/pages/shop.php" method="GET">
                        <input type="text" class="form-control" name="search" placeholder="Search for spare parts, tools, accessories..." aria-label="Search">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
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
                <form class="input-group spx-header-search" action="/pages/shop.php" method="GET">
                    <input type="text" class="form-control" name="search" placeholder="Search parts..." aria-label="Search">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="navbar navbar-expand-lg spx-nav-bar mb-3">
            <div class="container-fluid px-2">
                <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNav">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="mobileNav">
                    <div class="navbar-nav mx-auto">
                        <?php foreach ($nav_menu as $item): ?>
                            <a href="<?php echo $item['url']; ?>" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == basename($item['url'])) ? 'active' : ''; ?>">
                                <?php echo $item['text']; ?>
                            </a>
                        <?php endforeach; ?>
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <a href="/pages/my_account.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'my_account.php') ? 'active' : ''; ?>">My Account</a>
                            <a href="/pages/order_history.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'order_history.php') ? 'active' : ''; ?>">Orders</a>
                        <?php endif; ?>
                    </div>
                    <!-- Mobile: auth links -->
                    <div class="d-lg-none mt-2 pb-2 border-top border-white border-opacity-25 pt-2">
                        <?php if (isset($_SESSION['customer_id'])): ?>
                            <a href="/pages/logout.php" class="nav-link text-white-50"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                        <?php else: ?>
                            <a href="/pages/login.php" class="nav-link text-white-50"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                            <a href="/pages/register.php" class="nav-link text-white-50"><i class="fas fa-user-plus me-1"></i>Register</a>
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
