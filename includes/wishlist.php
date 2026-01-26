<?php
// Wishlist Functionality for SPARE XPRESS LTD
// Include this file in pages that need wishlist functionality
?>

<!-- Wishlist Sidebar (Hidden by default) -->
<div id="wishlistSidebar" class="wishlist-sidebar">
    <div class="wishlist-header">
        <h5 class="mb-0">
            <i class="fas fa-heart text-danger me-2"></i>
            My Wishlist
            <span id="wishlistCount" class="badge bg-danger ms-2">0</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" onclick="toggleWishlist()"></button>
    </div>

    <div class="wishlist-content">
        <div id="wishlistItems" class="wishlist-items">
            <!-- Wishlist items will be loaded here -->
        </div>

        <div id="emptyWishlist" class="empty-wishlist text-center py-5">
            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
            <h6 class="text-muted mb-2">Your wishlist is empty</h6>
            <p class="text-muted small mb-4">Save items you love for later</p>
            <button class="btn btn-primary btn-sm" onclick="toggleWishlist()">
                <i class="fas fa-shopping-bag me-1"></i>Continue Shopping
            </button>
        </div>
    </div>

    <div class="wishlist-footer">
        <button class="btn btn-outline-danger btn-sm w-100" onclick="clearWishlist()">
            <i class="fas fa-trash me-1"></i>Clear All
        </button>
    </div>
</div>

<!-- Wishlist Overlay -->
<div id="wishlistOverlay" class="wishlist-overlay" onclick="toggleWishlist()"></div>

<style>
/* Wishlist Sidebar Styles */
.wishlist-sidebar {
    position: fixed;
    top: 0;
    right: -400px;
    width: 380px;
    height: 100vh;
    background: white;
    box-shadow: -5px 0 20px rgba(0,0,0,0.15);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.wishlist-sidebar.open {
    right: 0;
}

.wishlist-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: between;
    align-items: center;
    border: none;
}

.wishlist-content {
    flex: 1;
    overflow-y: auto;
    padding: 0;
}

.wishlist-items {
    padding: 0;
}

.wishlist-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
    position: relative;
}

.wishlist-item:hover {
    background-color: #f8f9fa;
}

.wishlist-item-image {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 8px;
    margin-right: 1rem;
    border: 1px solid #e9ecef;
}

.wishlist-item-details {
    flex: 1;
    min-width: 0;
}

.wishlist-item-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.wishlist-item-meta {
    font-size: 0.8rem;
    color: #718096;
    margin-bottom: 0.25rem;
}

.wishlist-item-price {
    font-size: 0.95rem;
    font-weight: 700;
    color: #007bff;
}

.wishlist-item-actions {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.wishlist-item-remove {
    background: none;
    border: none;
    color: #dc3545;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;
    font-size: 0.8rem;
}

.wishlist-item-remove:hover {
    background-color: #f8d7da;
    color: #b02a37;
}

.wishlist-item-cart {
    background: #28a745;
    border: none;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    transition: all 0.2s ease;
}

.wishlist-item-cart:hover {
    background-color: #218838;
    transform: scale(1.05);
}

.empty-wishlist {
    display: none;
}

.empty-wishlist.show {
    display: block;
}

.wishlist-footer {
    padding: 1rem;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
}

.wishlist-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1049;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.wishlist-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Wishlist button styles */
.btn-wishlist {
    position: relative;
    transition: all 0.3s ease;
}

.btn-wishlist.active {
    color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.btn-wishlist:hover {
    transform: scale(1.1);
}

.wishlist-pulse {
    animation: wishlistPulse 2s infinite;
}

@keyframes wishlistPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Mobile responsive */
@media (max-width: 576px) {
    .wishlist-sidebar {
        width: 100%;
        right: -100%;
    }

    .wishlist-item {
        padding: 0.75rem;
    }

    .wishlist-item-image {
        width: 50px;
        height: 50px;
    }

    .wishlist-item-title {
        font-size: 0.85rem;
    }
}
</style>

<script>
// Wishlist Management System
class WishlistManager {
    constructor() {
        this.items = this.loadWishlist();
        this.sidebar = document.getElementById('wishlistSidebar');
        this.overlay = document.getElementById('wishlistOverlay');
        this.updateUI();
    }

    loadWishlist() {
        try {
            const stored = localStorage.getItem('sparexpress_wishlist');
            return stored ? JSON.parse(stored) : [];
        } catch (error) {
            console.error('Error loading wishlist:', error);
            return [];
        }
    }

    saveWishlist() {
        try {
            localStorage.setItem('sparexpress_wishlist', JSON.stringify(this.items));
        } catch (error) {
            console.error('Error saving wishlist:', error);
        }
    }

    addItem(productId, productData = null) {
        if (this.hasItem(productId)) {
            showWarningToast('This item is already in your wishlist', 'Already Added');
            return false;
        }

        const item = {
            id: productId,
            addedAt: new Date().toISOString(),
            data: productData
        };

        this.items.push(item);
        this.saveWishlist();
        this.updateUI();

        showSuccessToast('Item added to wishlist!', 'Added to Wishlist');
        return true;
    }

    removeItem(productId) {
        const index = this.items.findIndex(item => item.id == productId);
        if (index > -1) {
            this.items.splice(index, 1);
            this.saveWishlist();
            this.updateUI();
            showInfoToast('Item removed from wishlist', 'Removed');
            return true;
        }
        return false;
    }

    hasItem(productId) {
        return this.items.some(item => item.id == productId);
    }

    toggleItem(productId, productData = null) {
        if (this.hasItem(productId)) {
            return this.removeItem(productId);
        } else {
            return this.addItem(productId, productData);
        }
    }

    clearWishlist() {
        if (this.items.length === 0) return;

        if (confirm('Are you sure you want to clear your entire wishlist?')) {
            this.items = [];
            this.saveWishlist();
            this.updateUI();
            showInfoToast('Wishlist cleared', 'Cleared');
        }
    }

    getItems() {
        return [...this.items];
    }

    getCount() {
        return this.items.length;
    }

    updateUI() {
        this.updateWishlistButtons();
        this.updateWishlistCount();
        this.renderWishlistSidebar();
    }

    updateWishlistButtons() {
        // Update all wishlist buttons on the page
        document.querySelectorAll('.btn-wishlist').forEach(btn => {
            const productId = btn.dataset.productId;
            if (productId) {
                const isInWishlist = this.hasItem(productId);
                btn.classList.toggle('active', isInWishlist);

                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = isInWishlist ? 'fas fa-heart' : 'far fa-heart';
                }
            }
        });
    }

    updateWishlistCount() {
        const countElements = document.querySelectorAll('.wishlist-count');
        countElements.forEach(el => {
            el.textContent = this.getCount();
        });

        // Update sidebar count
        const sidebarCount = document.getElementById('wishlistCount');
        if (sidebarCount) {
            sidebarCount.textContent = this.getCount();
        }
    }

    renderWishlistSidebar() {
        const container = document.getElementById('wishlistItems');
        const emptyState = document.getElementById('emptyWishlist');

        if (!container || !emptyState) return;

        if (this.items.length === 0) {
            container.innerHTML = '';
            emptyState.classList.add('show');
            return;
        }

        emptyState.classList.remove('show');

        // Load product details for wishlist items
        const productIds = this.items.map(item => item.id);
        this.loadProductDetails(productIds).then(products => {
            container.innerHTML = products.map(product => `
                <div class="wishlist-item" data-product-id="${product.id}">
                    <img src="${product.image}" alt="${product.name}"
                         class="wishlist-item-image"
                         onerror="this.src='/img/no-image.png'">
                    <div class="wishlist-item-details">
                        <div class="wishlist-item-title">${product.name}</div>
                        <div class="wishlist-item-meta">
                            ${product.brand ? product.brand : ''} ${product.model ? '• ' + product.model : ''}
                        </div>
                        <div class="wishlist-item-price">RWF ${product.price.toLocaleString()}</div>
                    </div>
                    <div class="wishlist-item-actions">
                        <button class="btn btn-sm wishlist-item-cart"
                                onclick="wishlistManager.addToCart(${product.id})">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                        <button class="btn btn-sm wishlist-item-remove"
                                onclick="wishlistManager.removeItem(${product.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }).catch(error => {
            console.error('Error loading wishlist products:', error);
            container.innerHTML = '<div class="text-center py-3 text-muted">Error loading wishlist items</div>';
        });
    }

    async loadProductDetails(productIds) {
        if (productIds.length === 0) return [];

        try {
            const response = await fetch('/api/get_products.php?ids=' + productIds.join(','));
            const data = await response.json();

            if (data.success && data.products) {
                return data.products;
            }
        } catch (error) {
            console.error('Error fetching product details:', error);
        }

        return [];
    }

    addToCart(productId) {
        // This would integrate with your cart system
        showInfoToast('Add to cart functionality would be implemented here', 'Cart Integration');
    }

    toggleSidebar() {
        if (this.sidebar) {
            this.sidebar.classList.toggle('open');
            this.overlay.classList.toggle('active');
        }
    }
}

// Global wishlist manager instance
let wishlistManager;

function toggleWishlist() {
    if (wishlistManager) {
        wishlistManager.toggleSidebar();
    }
}

function addToWishlist(productId, productData = null) {
    if (wishlistManager) {
        wishlistManager.toggleItem(productId, productData);
    }
}

function removeFromWishlist(productId) {
    if (wishlistManager) {
        wishlistManager.removeItem(productId);
    }
}

function clearWishlist() {
    if (wishlistManager) {
        wishlistManager.clearWishlist();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    wishlistManager = new WishlistManager();

    // Add wishlist buttons to products if they don't exist
    document.querySelectorAll('.product-card, .product-item').forEach(card => {
        const productId = card.dataset.productId || card.id;
        if (productId && !card.querySelector('.btn-wishlist')) {
            const wishlistBtn = document.createElement('button');
            wishlistBtn.className = 'btn btn-light btn-wishlist';
            wishlistBtn.dataset.productId = productId;
            wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
            wishlistBtn.onclick = (e) => {
                e.stopPropagation();
                addToWishlist(productId);
            };

            // Add to card controls
            const controls = card.querySelector('.card-controls, .product-controls');
            if (controls) {
                controls.appendChild(wishlistBtn);
            }
        }
    });
});
</script>