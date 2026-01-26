<?php
$page_title = 'Order Spare Parts On Demand - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

// Handle success messages
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Handle form errors
$form_errors = [];
$form_data = [];
if (isset($_SESSION['form_errors'])) {
    $form_errors = $_SESSION['form_errors'];
    $form_data = $_SESSION['form_data'] ?? [];
    unset($_SESSION['form_errors']);
    unset($_SESSION['form_data']);
}
?>

<!-- Hero Section Start -->
<div class="container-fluid page-header py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 text-white fw-bold mb-4 wow fadeInUp" data-wow-delay="0.1s">
                    Order Spare Parts On Demand
                </h1>
                <p class="lead text-white-50 mb-4 wow fadeInUp" data-wow-delay="0.3s">
                    "If the part is not available in our stock, we can source it for you from Japan, Dubai, Europe, and China. Fast, reliable, and delivered across Rwanda."
                </p>
                <div class="d-flex gap-3 flex-wrap wow fadeInUp" data-wow-delay="0.5s">
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-globe fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0">Global Sourcing</h6>
                            <small>Japan • Dubai • Europe • China</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <i class="fas fa-truck fa-2x me-3"></i>
                        <div>
                            <h6 class="mb-0">Fast Delivery</h6>
                            <small>Across Rwanda</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center wow fadeInUp" data-wow-delay="0.7s">
                <img src="/img/logo/logox.jpg" alt="SPARE XPRESS" class="img-fluid rounded shadow" style="max-width: 250px;">
            </div>
        </div>
    </div>
</div>
<!-- Hero Section End -->

<!-- How It Works Section Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">How It Works</h4>
            <h1 class="mb-0 display-5 wow fadeInUp" data-wow-delay="0.3s">Simple 4-Step Process</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">1</span>
                    </div>
                    <h5 class="mb-3">Submit Your Request</h5>
                    <p class="mb-0 text-muted">Fill out the form below with your vehicle and part details. Upload photos if available.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">2</span>
                    </div>
                    <h5 class="mb-3">We Verify & Source</h5>
                    <p class="mb-0 text-muted">Our experts verify fitment and source the part from trusted international suppliers.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">3</span>
                    </div>
                    <h5 class="mb-3">Pay 50% Deposit</h5>
                    <p class="mb-0 text-muted">Once sourced, pay 50% deposit via mobile money. We handle import and customs.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="text-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px;">
                        <span class="text-white fw-bold fs-4">4</span>
                    </div>
                    <h5 class="mb-3">Delivery to You</h5>
                    <p class="mb-0 text-muted">Receive your part with professional installation guidance and warranty.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- How It Works Section End -->

<!-- Order Request Form Start -->
<div class="container-fluid py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="bg-white rounded shadow p-4 p-lg-5 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="text-center mb-5">
                        <h2 class="mb-3">Request Your Spare Part</h2>
                        <p class="text-muted">Fill out the form below and we'll source your part from international suppliers</p>
                        
                        <!-- Success Message -->
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success_message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Error Messages -->
                        <?php if (!empty($form_errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($form_errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="/process_order_request.php" method="POST" enctype="multipart/form-data" id="orderRequestForm">
                        <!-- Vehicle Details Section -->
                        <div class="mb-5">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-car me-2"></i>Vehicle Details
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="vehicle_brand" class="form-label fw-bold">Vehicle Brand <span class="text-danger">*</span></label>
                                    <select class="form-select" id="vehicle_brand" name="vehicle_brand" required>
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $brand): ?>
                                            <option value="<?php echo $brand['slug']; ?>" <?php echo (isset($form_data['vehicle_brand']) && $form_data['vehicle_brand'] == $brand['slug']) ? 'selected' : ''; ?>>
                                                <?php echo $brand['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="vehicle_model" class="form-label fw-bold">Vehicle Model <span class="text-danger">*</span></label>
                                    <select class="form-select" id="vehicle_model" name="vehicle_model" required disabled>
                                        <option value="">Select Model</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="year" class="form-label fw-bold">Year</label>
                                    <input type="number" class="form-control" id="year" name="year" placeholder="e.g., 2020" min="1990" max="2025" value="<?php echo isset($form_data['year']) ? htmlspecialchars($form_data['year']) : ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="chassis_number" class="form-label fw-bold">Chassis Number</label>
                                    <input type="text" class="form-control" id="chassis_number" name="chassis_number" placeholder="Optional" value="<?php echo isset($form_data['chassis_number']) ? htmlspecialchars($form_data['chassis_number']) : ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="vehicle_plate" class="form-label fw-bold">Vehicle Plate</label>
                                    <input type="text" class="form-control" id="vehicle_plate" name="vehicle_plate" placeholder="e.g., RAB 123 A" value="<?php echo isset($form_data['vehicle_plate']) ? htmlspecialchars($form_data['vehicle_plate']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Part Details Section -->
                        <div class="mb-5">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-cogs me-2"></i>Part Details
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="part_name" class="form-label fw-bold">Part Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="part_name" name="part_name" placeholder="e.g., Brake Pads Front Set" required value="<?php echo isset($form_data['part_name']) ? htmlspecialchars($form_data['part_name']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="part_category" class="form-label fw-bold">Part Category <span class="text-danger">*</span></label>
                                    <select class="form-select" id="part_category" name="part_category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['slug']; ?>" <?php echo (isset($form_data['part_category']) && $form_data['part_category'] == $category['slug']) ? 'selected' : ''; ?>>
                                                <?php echo $category['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="part_description" class="form-label fw-bold">Part Description</label>
                                    <textarea class="form-control" id="part_description" name="part_description" rows="4" placeholder="Describe the part you need, any specific requirements, or symptoms you're experiencing..."><?php echo isset($form_data['part_description']) ? htmlspecialchars($form_data['part_description']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="mb-5">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-images me-2"></i>Upload Images (Optional)
                            </h4>
                            <p class="text-muted mb-3">Upload up to 4 images to help us identify the exact part you need</p>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <input type="file" class="form-control" id="image_1" name="image_1" accept="image/*" onchange="previewImage(this, 'preview1')">
                                        <img id="preview1" class="img-fluid mt-2" style="max-height: 100px; display: none;">
                                        <small class="text-muted">Image 1</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <input type="file" class="form-control" id="image_2" name="image_2" accept="image/*" onchange="previewImage(this, 'preview2')">
                                        <img id="preview2" class="img-fluid mt-2" style="max-height: 100px; display: none;">
                                        <small class="text-muted">Image 2</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <input type="file" class="form-control" id="image_3" name="image_3" accept="image/*" onchange="previewImage(this, 'preview3')">
                                        <img id="preview3" class="img-fluid mt-2" style="max-height: 100px; display: none;">
                                        <small class="text-muted">Image 3</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <input type="file" class="form-control" id="image_4" name="image_4" accept="image/*" onchange="previewImage(this, 'preview4')">
                                        <img id="preview4" class="img-fluid mt-2" style="max-height: 100px; display: none;">
                                        <small class="text-muted">Image 4</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information Section -->
                        <div class="mb-5">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-user me-2"></i>Customer Information
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required value="<?php echo isset($form_data['full_name']) ? htmlspecialchars($form_data['full_name']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="+250790123456" required value="<?php echo isset($form_data['phone_number']) ? htmlspecialchars($form_data['phone_number']) : ''; ?>">
                                    <small class="text-muted">Rwandan phone number with country code</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($form_data['email']) ? htmlspecialchars($form_data['email']) : ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="province_district" class="form-label fw-bold">Province/District</label>
                                    <select class="form-select" id="province_district" name="province_district">
                                        <option value="">Select Province</option>
                                        <option value="Kigali" <?php echo (isset($form_data['province_district']) && $form_data['province_district'] == 'Kigali') ? 'selected' : ''; ?>>Kigali</option>
                                        <option value="Northern" <?php echo (isset($form_data['province_district']) && $form_data['province_district'] == 'Northern') ? 'selected' : ''; ?>>Northern Province</option>
                                        <option value="Southern" <?php echo (isset($form_data['province_district']) && $form_data['province_district'] == 'Southern') ? 'selected' : ''; ?>>Southern Province</option>
                                        <option value="Eastern" <?php echo (isset($form_data['province_district']) && $form_data['province_district'] == 'Eastern') ? 'selected' : ''; ?>>Eastern Province</option>
                                        <option value="Western" <?php echo (isset($form_data['province_district']) && $form_data['province_district'] == 'Western') ? 'selected' : ''; ?>>Western Province</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="delivery_address" class="form-label fw-bold">Delivery Address</label>
                                    <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" placeholder="Street address, landmark, or specific delivery instructions"><?php echo isset($form_data['delivery_address']) ? htmlspecialchars($form_data['delivery_address']) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Order Type Section -->
                        <div class="mb-5">
                            <h4 class="mb-4 text-primary">
                                <i class="fas fa-clock me-2"></i>Order Type
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="order_type" id="normal_order" value="normal" <?php echo (!isset($form_data['order_type']) || $form_data['order_type'] == 'normal') ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="normal_order">
                                            Normal Order
                                            <small class="text-muted d-block">Standard processing (2-4 weeks)</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="order_type" id="urgent_order" value="urgent" <?php echo (isset($form_data['order_type']) && $form_data['order_type'] == 'urgent') ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="urgent_order">
                                            Urgent Order
                                            <small class="text-muted d-block">Express processing (1-2 weeks) + 20% surcharge</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Submit -->
                        <div class="text-center">
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms_agree" name="terms_agree" required <?php echo (isset($form_data['terms_agree'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="terms_agree">
                                    I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and understand that a 50% deposit is required for special orders.
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3">
                                <i class="fas fa-paper-plane me-2"></i>Submit Order Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Order Request Form End -->

<!-- FAQ Section Start -->
<div class="container-fluid py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block p-2 title-border-radius wow fadeInUp" data-wow-delay="0.1s">Frequently Asked Questions</h4>
            <h1 class="mb-0 display-5 wow fadeInUp" data-wow-delay="0.3s">Common Questions</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How long does shipping take?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Normal orders typically take 2-4 weeks from order confirmation. Urgent orders are processed within 1-2 weeks. Additional time may be needed for customs clearance.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do I pay the deposit?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once we confirm the part is available, you'll receive payment instructions via SMS and email. We accept mobile money payments (MTN Mobile Money, Airtel Money) and bank transfers.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What if the part doesn't fit my vehicle?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Our team verifies fitment before shipping. If there's any issue, we provide free returns and refunds. We also offer professional installation guidance.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="bg-white rounded p-4 shadow">
                    <h5 class="mb-3">Why Choose SPARE XPRESS?</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Genuine parts from trusted suppliers</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Competitive pricing with no hidden fees</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Professional installation support</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Warranty on all parts</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Fast delivery across Rwanda</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Expert technical consultation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FAQ Section End -->

<script>
// Dynamic brand-model loading
document.getElementById('vehicle_brand').addEventListener('change', function() {
    const brand = this.value;
    const modelSelect = document.getElementById('vehicle_model');

    if (brand) {
        // Show loading
        modelSelect.innerHTML = '<option value="">Loading...</option>';
        modelSelect.disabled = true;

        // Fetch models via AJAX
        fetch(`/get_models.php?brand=${brand}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modelSelect.innerHTML = '<option value="">Select Model</option>';
                    data.models.forEach(model => {
                        const option = document.createElement('option');
                        option.value = model;
                        option.textContent = model;
                        modelSelect.appendChild(option);
                    });
                    modelSelect.disabled = false;
                } else {
                    modelSelect.innerHTML = '<option value="">Error loading models</option>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                modelSelect.innerHTML = '<option value="">Error loading models</option>';
            });
    } else {
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        modelSelect.disabled = true;
    }
});

// Image preview function
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Form validation
document.getElementById('orderRequestForm').addEventListener('submit', function(e) {
    const phoneInput = document.getElementById('phone_number');
    const phonePattern = /^\+?250[0-9]{9}$/;

    if (!phonePattern.test(phoneInput.value)) {
        e.preventDefault();
        alert('Please enter a valid Rwandan phone number (e.g., +250790123456)');
        phoneInput.focus();
        return false;
    }

    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
});
</script>

<?php include '../includes/footer.php'; ?>