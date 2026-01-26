<?php
// Session check must happen BEFORE any HTML output
include '../includes/client_session_check.php';

$page_title = 'Secure Checkout - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

// Get customer data for auto-fill
$customer_data = null;
if (isset($_SESSION['customer_id'])) {
    include '../includes/config.php';
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['customer_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer_data = $result->fetch_assoc();
    $stmt->close();
}

// Get cart data
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_total = 0;
$cart_count = 0;
$special_order_items = 0;

foreach ($cart_items as $item) {
    $cart_total += $item['subtotal'];
    $cart_count += $item['quantity'];
    if ($item['stock'] == 0) {
        $special_order_items++;
    }
}

// Split full name for form
$first_name = '';
$last_name = '';
if ($customer_data && isset($customer_data['full_name'])) {
    $name_parts = explode(' ', $customer_data['full_name'], 2);
    $first_name = $name_parts[0] ?? '';
    $last_name = $name_parts[1] ?? '';
}

// Calculate shipping and deposit
$shipping_cost = 0; // Will be calculated based on location
$deposit_required = $special_order_items > 0 ? ceil($cart_total * 0.5) : 0; // 50% deposit for special orders
$final_total = $cart_total + $shipping_cost;
?>

<!-- Page Header -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 text-white fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    <i class="fas fa-credit-card me-3"></i>Secure Checkout
                </h1>
                <p class="lead text-white-50 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    Complete your order securely. Your payment information is protected with bank-level security.
                </p>
                <div class="d-flex gap-3 flex-wrap wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-shield-alt fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">SSL Secured</h6>
                            <small class="text-white-50">256-bit encryption</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-truck fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">Fast Delivery</h6>
                            <small class="text-white-50">2-5 business days</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-undo fa-lg text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">30-Day Returns</h6>
                            <small class="text-white-50">Money back guarantee</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center wow fadeInUp" data-wow-delay="0.7s">
                <div class="position-relative">
                    <img src="/img/logo/logox.jpg" alt="SPARE XPRESS" class="img-fluid rounded shadow-lg" style="max-width: 250px; filter: brightness(1.1);">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-to-r from-primary to-secondary rounded opacity-25"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Background Pattern -->
    <div class="position-absolute top-0 end-0 opacity-10" style="font-size: 200px;">
        <i class="fas fa-lock text-white"></i>
    </div>
</div>

<!-- Checkout Process Steps -->
<div class="container-fluid py-4 bg-light border-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="checkout-steps d-flex justify-content-center">
                    <div class="step completed">
                        <div class="step-circle">1</div>
                        <span class="step-text">Cart</span>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step active">
                        <div class="step-circle">2</div>
                        <span class="step-text">Checkout</span>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step">
                        <div class="step-circle">3</div>
                        <span class="step-text">Payment</span>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step">
                        <div class="step-circle">4</div>
                        <span class="step-text">Complete</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Checkout Content -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <form id="checkoutForm" method="POST" action="process_checkout.php">
            <div class="row g-5">
                <!-- Billing & Shipping Information -->
                <div class="col-lg-8">
                    <div class="checkout-section">
                        <div class="section-header">
                            <h4 class="mb-4">
                                <i class="fas fa-user me-2 text-primary"></i>Billing & Shipping Information
                            </h4>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">First Name *</label>
                                <input type="text" class="form-control form-control-lg" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Last Name *</label>
                                <input type="text" class="form-control form-control-lg" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address *</label>
                                <input type="email" class="form-control form-control-lg" name="email" value="<?php echo htmlspecialchars($customer_data['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number *</label>
                                <input type="tel" class="form-control form-control-lg" name="phone" value="<?php echo htmlspecialchars($customer_data['phone'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Delivery Address *</label>
                                <textarea class="form-control form-control-lg" name="address" rows="3" placeholder="House number, street name, area..." required><?php echo htmlspecialchars($customer_data['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">City/District *</label>
                                <input type="text" class="form-control form-control-lg" name="city" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Region *</label>
                                <select class="form-select form-select-lg" name="region" required>
                                    <option value="">Select Region</option>
                                    <option value="kigali">Kigali City</option>
                                    <option value="northern">Northern Province</option>
                                    <option value="southern">Southern Province</option>
                                    <option value="eastern">Eastern Province</option>
                                    <option value="western">Western Province</option>
                                </select>
                            </div>
                            <!-- Vehicle Year Specification for Parts -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Vehicle Year for Parts</label>
                                <p class="text-muted small mb-3">Please specify the year of your vehicle for each part to ensure compatibility</p>

                                <div id="vehicleYearsSection" class="border rounded p-3 bg-light">
                                    <?php if (!empty($cart_items)): ?>
                                        <?php foreach ($cart_items as $index => $item): ?>
                                            <div class="vehicle-year-item mb-3 pb-3 border-bottom">
                                                <div class="d-flex align-items-center mb-2">
                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         class="rounded me-3" style="width: 40px; height: 40px; object-fit: contain;">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold small"><?php echo htmlspecialchars($item['name']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($item['brand'] ?? ''); ?> <?php echo htmlspecialchars($item['model'] ?? ''); ?></small>
                                                    </div>
                                                </div>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Vehicle Year *</label>
                                                        <select class="form-select form-select-sm vehicle-year-select" name="vehicle_year[<?php echo $index; ?>]" required>
                                                            <option value="">Select Year</option>
                                                            <!-- Years will be populated dynamically -->
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Compatibility</label>
                                                        <div class="compatibility-status small text-muted" id="compatibility-<?php echo $index; ?>">
                                                            <i class="fas fa-info-circle me-1"></i>Select year to check compatibility
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="cart_item_id[<?php echo $index; ?>]" value="<?php echo $item['id']; ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-3">
                                            <small class="text-muted">No items in cart</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Order Notes (Optional)</label>
                                <textarea class="form-control form-control-lg" name="order_notes" rows="3" placeholder="Any special instructions for delivery or installation..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section mt-4">
                        <div class="section-header">
                            <h4 class="mb-4">
                                <i class="fas fa-credit-card me-2 text-primary"></i>Payment Method
                            </h4>
                        </div>

                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" class="btn-check" name="payment_method" id="bank_transfer" value="bank_transfer" autocomplete="off" checked>
                                <label class="btn btn-outline-primary w-100 text-start p-3" for="bank_transfer">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-university fa-2x me-3"></i>
                                        <div>
                                            <div class="fw-bold">Bank Transfer</div>
                                            <small class="text-muted">Direct bank transfer - Secure & Reliable</small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" class="btn-check" name="payment_method" id="mobile_money" value="mobile_money" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 text-start p-3" for="mobile_money">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-mobile-alt fa-2x me-3"></i>
                                        <div>
                                            <div class="fw-bold">Mobile Money (MTN/Airtel)</div>
                                            <small class="text-muted">Pay with your mobile money account</small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="payment-option">
                                <input type="radio" class="btn-check" name="payment_method" id="cash_delivery" value="cash_delivery" autocomplete="off">
                                <label class="btn btn-outline-primary w-100 text-start p-3" for="cash_delivery">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave fa-2x me-3"></i>
                                        <div>
                                            <div class="fw-bold">Cash on Delivery</div>
                                            <small class="text-muted">Pay when you receive your order</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="checkout-summary">
                        <div class="summary-header">
                            <h4 class="mb-4">
                                <i class="fas fa-shopping-cart me-2"></i>Order Summary
                            </h4>
                        </div>

                        <!-- Cart Items -->
                        <div class="cart-items mb-4">
                            <?php if (empty($cart_items)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Your cart is empty</p>
                                    <a href="/pages/shop.php" class="btn btn-primary">Continue Shopping</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($cart_items as $index => $item): ?>
                                    <div class="cart-item d-flex align-items-center mb-3 pb-3 border-bottom">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: contain;">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['brand'] ?? ''); ?> <?php echo htmlspecialchars($item['model'] ?? ''); ?></small>
                                            <?php if (!empty($item['year_from']) && !empty($item['year_to'])): ?>
                                                <small class="text-info d-block">
                                                    <i class="fas fa-calendar me-1"></i>Compatible: <?php echo $item['year_from']; ?>-<?php echo $item['year_to']; ?>
                                                </small>
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                                <span class="fw-bold text-primary">RWF <?php echo number_format($item['subtotal'], 0, '.', ','); ?></span>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <span id="cart-year-display-<?php echo $index; ?>">
                                                        <i class="fas fa-clock me-1"></i>Year: <span class="text-warning">Not selected</span>
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Order Totals -->
                        <div class="order-totals">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal (<?php echo $cart_count; ?> items)</span>
                                <span>RWF <?php echo number_format($cart_total, 0, '.', ','); ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span id="shippingCost">Calculated at next step</span>
                            </div>

                            <?php if ($deposit_required > 0): ?>
                                <div class="d-flex justify-content-between mb-2 text-warning">
                                    <span>Deposit Required (50%)</span>
                                    <span>RWF <?php echo number_format($deposit_required, 0, '.', ','); ?></span>
                                </div>
                                <div class="alert alert-warning py-2 mb-3">
                                    <small><i class="fas fa-info-circle me-1"></i>Special order items require a 50% deposit. Remaining balance due before delivery.</small>
                                </div>
                            <?php endif; ?>

                            <hr class="my-3">
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span class="text-primary" id="finalTotal">RWF <?php echo number_format($final_total, 0, '.', ','); ?></span>
                            </div>
                        </div>

                        <!-- Order Summary with Year Confirmation -->
                        <div class="order-confirmation mt-4 p-3 bg-light rounded">
                            <h6 class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                Order Confirmation
                            </h6>
                            <div id="yearConfirmationSummary" class="small text-muted">
                                <p class="mb-2">Please review your vehicle year selections:</p>
                                <div id="yearSummaryList">
                                    <!-- Year summary will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>

                        <!-- Place Order Button -->
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success btn-lg w-100" id="placeOrderBtn">
                                <i class="fas fa-lock me-2"></i>
                                Place Order Securely
                            </button>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Your payment information is secure
                                </small>
                            </div>
                        </div>

                        <!-- Security Badges -->
                        <div class="security-badges mt-4 pt-3 border-top">
                            <div class="row g-2 text-center">
                                <div class="col-4">
                                    <i class="fas fa-lock fa-lg text-success"></i>
                                    <div class="small mt-1">SSL Secured</div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-shield-alt fa-lg text-primary"></i>
                                    <div class="small mt-1">Protected</div>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-check-circle fa-lg text-info"></i>
                                    <div class="small mt-1">Verified</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Checkout Page Styles */
.checkout-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.section-header h4 {
    color: #2d3748;
    font-weight: 700;
    border-bottom: 3px solid #e2e8f0;
    padding-bottom: 0.5rem;
}

.checkout-summary {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    position: sticky;
    top: 20px;
}

.summary-header h4 {
    color: #2d3748;
    font-weight: 700;
    border-bottom: 3px solid #e2e8f0;
    padding-bottom: 0.5rem;
}

.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    margin-left: -0.5rem;
    margin-right: -0.5rem;
}

.order-totals {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 10px;
    margin-top: 1rem;
}

.payment-methods .payment-option {
    margin-bottom: 1rem;
}

.payment-methods .btn-check:checked + .btn {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.checkout-steps {
    position: relative;
}

.checkout-steps .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

.checkout-steps .step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.checkout-steps .step.completed .step-circle {
    background-color: #28a745;
    color: white;
}

.checkout-steps .step.active .step-circle {
    background-color: #007bff;
    color: white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.3);
}

.checkout-steps .step:not(.completed):not(.active) .step-circle {
    background-color: #e9ecef;
    color: #6c757d;
}

.checkout-steps .step-text {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
    text-align: center;
}

.checkout-steps .step.active .step-text {
    color: #007bff;
}

.checkout-steps .step.completed .step-text {
    color: #28a745;
}

.checkout-steps .step-connector {
    width: 60px;
    height: 2px;
    background-color: #e9ecef;
    margin: 0 10px;
    align-self: center;
}

.checkout-steps .step.completed + .step-connector {
    background-color: #28a745;
}

.checkout-steps .step.active + .step-connector {
    background-color: #007bff;
}

/* Form enhancements */
.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    background: linear-gradient(135deg, #218838 0%, #17a2b8 100%);
}

/* Security badges */
.security-badges .fa-lg {
    font-size: 1.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .checkout-section, .checkout-summary {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .checkout-steps .step-connector {
        width: 30px;
    }

    .checkout-steps .step-circle {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .checkout-steps {
        flex-direction: column;
        gap: 1rem;
    }

    .checkout-steps .step-connector {
        width: 2px;
        height: 30px;
        margin: 10px 0;
    }
}

/* Vehicle Year Selection Styles */
#vehicleYearsSection {
    max-height: 400px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #007bff #f8f9fa;
}

#vehicleYearsSection::-webkit-scrollbar {
    width: 6px;
}

#vehicleYearsSection::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 3px;
}

#vehicleYearsSection::-webkit-scrollbar-thumb {
    background: #007bff;
    border-radius: 3px;
}

.vehicle-year-item {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.vehicle-year-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.vehicle-year-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.vehicle-year-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.compatibility-status {
    padding: 0.25rem 0;
    font-weight: 500;
}

/* Enhanced cart item year display */
#cart-year-display-0, #cart-year-display-1, #cart-year-display-2, #cart-year-display-3, #cart-year-display-4 {
    display: block;
    margin-top: 0.25rem;
}

/* Loading states */
#placeOrderBtn.loading {
    opacity: 0.7;
    pointer-events: none;
}

#placeOrderBtn.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    margin-left: 8px;
    border: 2px solid #ffffff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Enhanced form validation */
.vehicle-year-select:invalid {
    border-color: #dc3545;
}

.vehicle-year-select:invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Order Confirmation Section */
.order-confirmation {
    border: 2px solid #e9ecef;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.order-confirmation h6 {
    color: #2d3748;
    font-weight: 700;
    margin-bottom: 1rem;
}

#yearSummaryList .d-flex {
    padding: 0.25rem 0;
    border-bottom: 1px solid #e9ecef;
}

#yearSummaryList .d-flex:last-child {
    border-bottom: none;
}

/* Mobile enhancements */
@media (max-width: 768px) {
    #vehicleYearsSection {
        max-height: 300px;
    }

    .vehicle-year-item {
        padding: 0.75rem;
    }

    .vehicle-year-item img {
        width: 35px !important;
        height: 35px !important;
    }

    .order-confirmation {
        margin-top: 1rem;
    }
}
</style>

<script>
// Checkout form handling with enhanced year selection
document.addEventListener('DOMContentLoaded', function() {
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderBtn = document.getElementById('placeOrderBtn');

    // Initialize year dropdowns
    initializeYearDropdowns();

    // Update shipping cost based on region
    document.querySelector('select[name="region"]').addEventListener('change', function() {
        updateShippingCost(this.value);
    });

    // Form submission with year validation
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate year selections
        if (!validateYearSelections()) {
            return;
        }

        // Show loading state
        placeOrderBtn.classList.add('loading');
        placeOrderBtn.innerHTML = '<span>Processing Order...</span>';

        // Collect form data
        const formData = new FormData(this);

        // Add cart data with year selections
        const cartDataWithYears = <?php echo json_encode($cart_items); ?>.map((item, index) => {
            const yearSelect = document.querySelector(`select[name="vehicle_year[${index}]"]`);
            return {
                ...item,
                selected_year: yearSelect ? yearSelect.value : null
            };
        });
        formData.append('cart_data', JSON.stringify(cartDataWithYears));

        // Submit order
        fetch('process_checkout.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to order confirmation
                window.location.href = `order_confirmation.php?order_id=${data.order_id}`;
            } else {
                showError(data.message);
                placeOrderBtn.classList.remove('loading');
                placeOrderBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Place Order Securely';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred. Please try again.');
            placeOrderBtn.classList.remove('loading');
            placeOrderBtn.innerHTML = '<i class="fas fa-lock me-2"></i>Place Order Securely';
        });
    });
});

// Initialize year dropdowns with available years
function initializeYearDropdowns() {
    const yearSelects = document.querySelectorAll('.vehicle-year-select');

    if (yearSelects.length === 0) return;

    // Generate year options (from 1990 to current year + 2)
    const currentYear = new Date().getFullYear();
    const startYear = 1990;
    let yearOptions = '<option value="">Select Year</option>';

    for (let year = currentYear + 2; year >= startYear; year--) {
        yearOptions += `<option value="${year}">${year}</option>`;
    }

    // Populate all year dropdowns
    yearSelects.forEach(select => {
        select.innerHTML = yearOptions;

        // Add change event listener for compatibility checking
        select.addEventListener('change', function() {
            checkCompatibility(this);
        });
    });
}

// Check compatibility between selected year and product
function checkCompatibility(yearSelect) {
    const selectedYear = yearSelect.value;
    const itemIndex = Array.from(yearSelect.closest('.vehicle-year-item').parentNode.children).indexOf(yearSelect.closest('.vehicle-year-item'));
    const compatibilityStatus = document.getElementById(`compatibility-${itemIndex}`);

    if (!selectedYear) {
        compatibilityStatus.innerHTML = '<i class="fas fa-info-circle me-1"></i>Select year to check compatibility';
        compatibilityStatus.className = 'compatibility-status small text-muted';
        return;
    }

    // Get product data from cart items
    const cartItems = <?php echo json_encode($cart_items); ?>;
    const product = cartItems[itemIndex];

    if (!product) return;

    // Check compatibility
    let isCompatible = true;
    let statusMessage = '';
    let statusClass = '';

    if (product.year_from && product.year_to) {
        const year = parseInt(selectedYear);
        const fromYear = parseInt(product.year_from);
        const toYear = parseInt(product.year_to);

        if (year >= fromYear && year <= toYear) {
            statusMessage = `<i class="fas fa-check-circle me-1"></i>Compatible (${product.year_from}-${product.year_to})`;
            statusClass = 'compatibility-status small text-success';
        } else {
            isCompatible = false;
            statusMessage = `<i class="fas fa-exclamation-triangle me-1"></i>Not compatible (${product.year_from}-${product.year_to})`;
            statusClass = 'compatibility-status small text-warning';
        }
    } else {
        statusMessage = '<i class="fas fa-info-circle me-1"></i>Compatibility data not available';
        statusClass = 'compatibility-status small text-info';
    }

    compatibilityStatus.innerHTML = statusMessage;
    compatibilityStatus.className = statusClass;

    // Update cart display
    updateCartYearDisplay(yearSelect, selectedYear, isCompatible);

    // Update form validation
    yearSelect.setCustomValidity(isCompatible ? '' : 'Selected year is not compatible with this part');
}

// Validate all year selections before form submission
function validateYearSelections() {
    const yearSelects = document.querySelectorAll('.vehicle-year-select');
    let allValid = true;

    yearSelects.forEach(select => {
        if (!select.value) {
            select.setCustomValidity('Please select the year of your vehicle for this part');
            allValid = false;
        } else {
            // Check if the selected year is compatible
            const itemIndex = Array.from(select.closest('.vehicle-year-item').parentNode.children).indexOf(select.closest('.vehicle-year-item'));
            const cartItems = <?php echo json_encode($cart_items); ?>;
            const product = cartItems[itemIndex];

            if (product && product.year_from && product.year_to) {
                const year = parseInt(select.value);
                const fromYear = parseInt(product.year_from);
                const toYear = parseInt(product.year_to);

                if (year < fromYear || year > toYear) {
                    select.setCustomValidity(`This part is not compatible with ${year} model year. Compatible years: ${product.year_from}-${product.year_to}`);
                    allValid = false;
                } else {
                    select.setCustomValidity('');
                }
            } else {
                select.setCustomValidity('');
            }
        }
    });

    // Trigger validation display
    if (!allValid) {
        yearSelects[0].reportValidity();
        showError('Please check the year selections for compatibility with your vehicle parts.');
    }

    return allValid;
}

// Update cart display with selected year
function updateCartYearDisplay(yearSelect, selectedYear, isCompatible) {
    const itemIndex = Array.from(yearSelect.closest('.vehicle-year-item').parentNode.children).indexOf(yearSelect.closest('.vehicle-year-item'));
    const cartYearDisplay = document.getElementById(`cart-year-display-${itemIndex}`);

    if (cartYearDisplay) {
        if (selectedYear) {
            const statusClass = isCompatible ? 'text-success' : 'text-danger';
            const statusIcon = isCompatible ? 'check-circle' : 'exclamation-triangle';
            cartYearDisplay.innerHTML = `
                <i class="fas fa-${statusIcon} me-1"></i>
                Year: <span class="${statusClass}">${selectedYear}</span>
            `;
        } else {
            cartYearDisplay.innerHTML = `
                <i class="fas fa-clock me-1"></i>
                Year: <span class="text-warning">Not selected</span>
            `;
        }
    }

    // Update confirmation summary
    updateYearConfirmationSummary();
}

// Update the year confirmation summary
function updateYearConfirmationSummary() {
    const summaryList = document.getElementById('yearSummaryList');
    const cartItems = <?php echo json_encode($cart_items); ?>;

    let summaryHtml = '';

    cartItems.forEach((item, index) => {
        const yearSelect = document.querySelector(`select[name="vehicle_year[${index}]"]`);
        const selectedYear = yearSelect ? yearSelect.value : '';

        let statusClass = 'text-warning';
        let statusText = 'Not selected';
        let iconClass = 'clock';

        if (selectedYear) {
            // Check compatibility
            let isCompatible = true;
            if (item.year_from && item.year_to) {
                const year = parseInt(selectedYear);
                const fromYear = parseInt(item.year_from);
                const toYear = parseInt(item.year_to);
                isCompatible = year >= fromYear && year <= toYear;
            }

            statusClass = isCompatible ? 'text-success' : 'text-danger';
            statusText = selectedYear;
            iconClass = isCompatible ? 'check-circle' : 'exclamation-triangle';
        }

        summaryHtml += `
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small class="text-truncate me-2" style="max-width: 200px;">${item.name}</small>
                <small class="${statusClass}">
                    <i class="fas fa-${iconClass} me-1"></i>${statusText}
                </small>
            </div>
        `;
    });

    summaryList.innerHTML = summaryHtml;
}

function updateShippingCost(region) {
    const shippingCostElement = document.getElementById('shippingCost');
    const finalTotalElement = document.getElementById('finalTotal');

    let shippingCost = 0;

    switch(region) {
        case 'kigali':
            shippingCost = 5000; // RWF 5,000 for Kigali
            break;
        case 'northern':
        case 'southern':
        case 'eastern':
        case 'western':
            shippingCost = 15000; // RWF 15,000 for other provinces
            break;
        default:
            shippingCost = 0;
    }

    const subtotal = <?php echo $cart_total; ?>;
    const finalTotal = subtotal + shippingCost;

    shippingCostElement.textContent = shippingCost > 0 ? `RWF ${shippingCost.toLocaleString()}` : 'Free';
    finalTotalElement.textContent = `RWF ${finalTotal.toLocaleString()}`;
}

function showError(message) {
    // Create error alert
    const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-exclamation-triangle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', alertHtml);

    setTimeout(() => {
        const alert = document.querySelector('.alert:last-child');
        if (alert) alert.remove();
    }, 5000);
}
</script>

<?php include '../includes/footer.php'; ?>
