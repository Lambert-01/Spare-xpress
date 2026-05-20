<?php
$page_title = 'Order Spare Parts On Demand - SPARE XPRESS LTD';
include '../includes/header.php';
include '../includes/navigation.php';

$success_message = '';
if (isset($_SESSION['success_message'])) { $success_message = $_SESSION['success_message']; unset($_SESSION['success_message']); }
$form_errors = [];
$form_data = [];
if (isset($_SESSION['form_errors'])) { $form_errors = $_SESSION['form_errors']; $form_data = $_SESSION['form_data'] ?? []; unset($_SESSION['form_errors'], $_SESSION['form_data']); }

// Pre-fill logged-in user data
$prefill_name = $prefill_email = $prefill_phone = '';
if (isset($_SESSION['customer_id'])) {
    $prefill_name  = $form_data['full_name'] ?? $_SESSION['customer_name'] ?? '';
    $prefill_email = $form_data['email'] ?? $_SESSION['customer_email'] ?? '';
    $prefill_phone = $form_data['phone_number'] ?? $_SESSION['customer_phone'] ?? '';
}
?>

<!-- Page Hero -->
<div class="spx-page-hero">
    <div class="container position-relative">
        <h1 class="fw-bold mb-2"><i class="fas fa-search me-2"></i>Order Spare Parts On Demand</h1>
        <p class="mb-2" style="color:rgba(255,255,255,.8);">Can't find it in stock? We source from Japan, Dubai, Europe &amp; China.</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item active">Order Request</li>
            </ol>
        </nav>
    </div>
</div>

<div class="spx-portal-wrap py-5">
    <div class="container">

        <!-- How It Works -->
        <div class="row g-3 mb-5 text-center">
            <?php
            $steps_info = [
                ['fas fa-file-alt','Submit Request','Fill the form with your vehicle & part details'],
                ['fas fa-search','We Source','Our team finds the part from trusted suppliers'],
                ['fas fa-money-bill-wave','Pay 50% Deposit','Confirm with a deposit via mobile money'],
                ['fas fa-truck','Delivery','Receive your part with warranty across Rwanda'],
            ];
            foreach ($steps_info as $i => $s): ?>
            <div class="col-6 col-md-3">
                <div class="spx-panel p-3 h-100">
                    <div class="spx-stat-icon blue mx-auto mb-2" style="width:52px;height:52px;border-radius:50%;font-size:1.3rem;"><i class="<?php echo $s[0]; ?>"></i></div>
                    <div class="fw-700 mb-1" style="font-weight:700;font-size:.9rem;"><?php echo $s[1]; ?></div>
                    <small class="text-muted"><?php echo $s[2]; ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if (!empty($form_errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <strong><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following:</strong>
                <ul class="mb-0 mt-1"><?php foreach ($form_errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="spx-panel">
            <div class="spx-panel-header">
                <h5 class="spx-panel-title"><i class="fas fa-clipboard-list me-2 text-primary"></i>Request Your Spare Part</h5>
                <small class="text-muted">Step <span id="current-step-label">1</span> of 5</small>
            </div>
            <div class="spx-panel-body">

                <!-- Step Progress -->
                <div class="spx-steps-nav mb-4" id="steps-nav">
                    <div class="spx-step-item active" data-step="1"><div class="spx-step-num">1</div><div class="spx-step-label">Vehicle</div></div>
                    <div class="spx-step-item" data-step="2"><div class="spx-step-num">2</div><div class="spx-step-label">Part</div></div>
                    <div class="spx-step-item" data-step="3"><div class="spx-step-num">3</div><div class="spx-step-label">Photos</div></div>
                    <div class="spx-step-item" data-step="4"><div class="spx-step-num">4</div><div class="spx-step-label">Contact</div></div>
                    <div class="spx-step-item" data-step="5"><div class="spx-step-num">5</div><div class="spx-step-label">Review</div></div>
                </div>

                <form action="/process_order_request.php" method="POST" enctype="multipart/form-data" id="orderRequestForm">

                    <!-- Step 1: Vehicle -->
                    <div class="spx-form-step active" id="step-1">
                        <div class="spx-step-section-title"><i class="fas fa-car"></i>Vehicle Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Vehicle Brand *</label>
                                    <select name="vehicle_brand" id="vehicle_brand" required>
                                        <option value="">Select Brand</option>
                                        <?php foreach ($brands as $brand): ?>
                                            <option value="<?php echo $brand['slug']; ?>" <?php echo (isset($form_data['vehicle_brand']) && $form_data['vehicle_brand']==$brand['slug'])?'selected':''; ?>><?php echo $brand['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Vehicle Model *</label>
                                    <select name="vehicle_model" id="vehicle_model" required disabled>
                                        <option value="">Select Brand First</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="spx-input-group no-icon">
                                    <label>Year</label>
                                    <input type="number" name="year" placeholder="e.g. 2020" min="1990" max="2025" value="<?php echo htmlspecialchars($form_data['year'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="spx-input-group no-icon">
                                    <label>Chassis Number</label>
                                    <input type="text" name="chassis_number" placeholder="Optional" value="<?php echo htmlspecialchars($form_data['chassis_number'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="spx-input-group no-icon">
                                    <label>Vehicle Plate</label>
                                    <input type="text" name="vehicle_plate" placeholder="e.g. RAB 123 A" value="<?php echo htmlspecialchars($form_data['vehicle_plate'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Part -->
                    <div class="spx-form-step" id="step-2">
                        <div class="spx-step-section-title"><i class="fas fa-cogs"></i>Part Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Part Name *</label>
                                    <input type="text" name="part_name" placeholder="e.g. Brake Pads Front Set" required value="<?php echo htmlspecialchars($form_data['part_name'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Part Category *</label>
                                    <select name="part_category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat['slug']; ?>" <?php echo (isset($form_data['part_category']) && $form_data['part_category']==$cat['slug'])?'selected':''; ?>><?php echo $cat['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="spx-input-group no-icon">
                                    <label>Part Description</label>
                                    <textarea name="part_description" rows="4" placeholder="Describe the part, any symptoms, or specific requirements..."><?php echo htmlspecialchars($form_data['part_description'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Order Type</label>
                                    <select name="order_type">
                                        <option value="normal" <?php echo (!isset($form_data['order_type'])||$form_data['order_type']==='normal')?'selected':''; ?>>Normal (2–4 weeks)</option>
                                        <option value="urgent" <?php echo (isset($form_data['order_type'])&&$form_data['order_type']==='urgent')?'selected':''; ?>>Urgent (1–2 weeks, +20%)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Photos -->
                    <div class="spx-form-step" id="step-3">
                        <div class="spx-step-section-title"><i class="fas fa-images"></i>Upload Photos <small class="text-muted fw-normal">(optional, up to 4)</small></div>
                        <p class="text-muted mb-3">Photos help us identify the exact part. Upload the damaged part, your vehicle, or any reference images.</p>
                        <div class="spx-image-upload-grid">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="spx-image-upload-box" id="upload-box-<?php echo $i; ?>">
                                <input type="file" name="image_<?php echo $i; ?>" accept="image/*" onchange="previewImg(this, <?php echo $i; ?>)">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <img id="preview-<?php echo $i; ?>" class="preview-img" alt="Preview">
                                <small>Photo <?php echo $i; ?></small>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Step 4: Contact -->
                    <div class="spx-form-step" id="step-4">
                        <div class="spx-step-section-title"><i class="fas fa-user"></i>Your Contact Details</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Full Name *</label>
                                    <input type="text" name="full_name" required value="<?php echo htmlspecialchars($prefill_name); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Phone Number * <small class="text-muted">(+250XXXXXXXXX)</small></label>
                                    <input type="tel" name="phone_number" placeholder="+250790123456" required value="<?php echo htmlspecialchars($prefill_phone); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Email Address *</label>
                                    <input type="email" name="email" required value="<?php echo htmlspecialchars($prefill_email); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-input-group no-icon">
                                    <label>Province</label>
                                    <select name="province_district">
                                        <option value="">Select Province</option>
                                        <?php foreach (['Kigali','Northern Province','Southern Province','Eastern Province','Western Province'] as $p): ?>
                                            <option value="<?php echo $p; ?>" <?php echo (isset($form_data['province_district'])&&$form_data['province_district']==$p)?'selected':''; ?>><?php echo $p; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="spx-input-group no-icon">
                                    <label>Delivery Address</label>
                                    <textarea name="delivery_address" rows="2" placeholder="Street, landmark, or delivery instructions"><?php echo htmlspecialchars($form_data['delivery_address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Review & Submit -->
                    <div class="spx-form-step" id="step-5">
                        <div class="spx-step-section-title"><i class="fas fa-clipboard-check"></i>Review &amp; Submit</div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="spx-panel" style="box-shadow:none;border:1px solid #e5e7eb;">
                                    <div class="spx-panel-header" style="background:#f9fafb;"><h6 class="spx-panel-title">Vehicle</h6></div>
                                    <div class="spx-panel-body py-2">
                                        <div class="spx-review-row"><span class="label">Brand</span><span class="value" id="rv-brand">—</span></div>
                                        <div class="spx-review-row"><span class="label">Model</span><span class="value" id="rv-model">—</span></div>
                                        <div class="spx-review-row"><span class="label">Year</span><span class="value" id="rv-year">—</span></div>
                                        <div class="spx-review-row"><span class="label">Plate</span><span class="value" id="rv-plate">—</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="spx-panel" style="box-shadow:none;border:1px solid #e5e7eb;">
                                    <div class="spx-panel-header" style="background:#f9fafb;"><h6 class="spx-panel-title">Part &amp; Contact</h6></div>
                                    <div class="spx-panel-body py-2">
                                        <div class="spx-review-row"><span class="label">Part</span><span class="value" id="rv-part">—</span></div>
                                        <div class="spx-review-row"><span class="label">Order Type</span><span class="value" id="rv-type">—</span></div>
                                        <div class="spx-review-row"><span class="label">Name</span><span class="value" id="rv-name">—</span></div>
                                        <div class="spx-review-row"><span class="label">Phone</span><span class="value" id="rv-phone">—</span></div>
                                        <div class="spx-review-row"><span class="label">Email</span><span class="value" id="rv-email">—</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 p-3 rounded" style="background:#eff6ff;border:1px solid #bfdbfe;">
                            <label class="d-flex align-items-start gap-2 mb-0" style="cursor:pointer;">
                                <input type="checkbox" name="terms_agree" id="terms_agree" required style="margin-top:3px;">
                                <span class="small">I agree to the <a href="#" class="text-primary">Terms &amp; Conditions</a> and understand that a 50% deposit is required to confirm special orders.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" id="btn-prev" onclick="changeStep(-1)" style="display:none;">
                            <i class="fas fa-arrow-left me-2"></i>Previous
                        </button>
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-primary" id="btn-next" onclick="changeStep(1)">
                                Next <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success d-none" id="btn-submit">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- FAQ -->
        <div class="row g-4 mt-2">
            <div class="col-lg-6">
                <div class="spx-panel">
                    <div class="spx-panel-header"><h5 class="spx-panel-title">Common Questions</h5></div>
                    <div class="spx-panel-body">
                        <div class="accordion accordion-flush" id="faqAcc">
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header"><button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">How long does shipping take?</button></h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body px-0 text-muted small">Normal orders: 2–4 weeks. Urgent orders: 1–2 weeks. Additional time may apply for customs clearance.</div></div>
                            </div>
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header"><button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">How do I pay the deposit?</button></h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body px-0 text-muted small">Once we confirm availability, you'll receive payment instructions via SMS and email. We accept MTN Mobile Money, Airtel Money, and bank transfers.</div></div>
                            </div>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header"><button class="accordion-button collapsed px-0" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">What if the part doesn't fit?</button></h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAcc"><div class="accordion-body px-0 text-muted small">Our team verifies fitment before shipping. If there's any issue, we provide free returns and refunds.</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="spx-panel h-100">
                    <div class="spx-panel-header"><h5 class="spx-panel-title">Why Choose SPARE XPRESS?</h5></div>
                    <div class="spx-panel-body">
                        <?php foreach (['Genuine parts from trusted global suppliers','Competitive pricing with no hidden fees','Professional installation support','Warranty on all parts','Fast delivery across Rwanda','Expert technical consultation'] as $item): ?>
                            <div class="d-flex align-items-center gap-2 mb-2"><i class="fas fa-check-circle text-success"></i><span class="small"><?php echo $item; ?></span></div>
                        <?php endforeach; ?>
                        <div class="mt-3">
                            <a href="https://wa.me/250792865114" target="_blank" class="spx-whatsapp-btn"><i class="fab fa-whatsapp"></i>Quick WhatsApp Order</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 5;

function changeStep(dir) {
    if (dir === 1 && !validateStep(currentStep)) return;
    const prev = currentStep;
    currentStep = Math.max(1, Math.min(totalSteps, currentStep + dir));
    document.getElementById('step-' + prev).classList.remove('active');
    document.getElementById('step-' + currentStep).classList.add('active');
    document.getElementById('current-step-label').textContent = currentStep;

    // Update step nav
    document.querySelectorAll('.spx-step-item').forEach((el, i) => {
        el.classList.remove('active','completed');
        if (i + 1 < currentStep) el.classList.add('completed');
        if (i + 1 === currentStep) el.classList.add('active');
    });

    document.getElementById('btn-prev').style.display = currentStep > 1 ? '' : 'none';
    document.getElementById('btn-next').classList.toggle('d-none', currentStep === totalSteps);
    document.getElementById('btn-submit').classList.toggle('d-none', currentStep !== totalSteps);

    if (currentStep === totalSteps) populateReview();
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function validateStep(step) {
    const s = document.getElementById('step-' + step);
    const required = s.querySelectorAll('[required]');
    let valid = true;
    required.forEach(el => {
        el.classList.remove('is-invalid');
        if (!el.value.trim()) { el.classList.add('is-invalid'); valid = false; }
    });
    if (!valid) { s.querySelector('.is-invalid')?.focus(); }
    return valid;
}

function populateReview() {
    const f = document.getElementById('orderRequestForm');
    const g = name => f.querySelector('[name="' + name + '"]')?.value || '—';
    const gText = name => { const el = f.querySelector('[name="' + name + '"]'); return el?.options?.[el.selectedIndex]?.text || el?.value || '—'; };
    document.getElementById('rv-brand').textContent = gText('vehicle_brand');
    document.getElementById('rv-model').textContent = gText('vehicle_model');
    document.getElementById('rv-year').textContent = g('year');
    document.getElementById('rv-plate').textContent = g('vehicle_plate');
    document.getElementById('rv-part').textContent = g('part_name');
    document.getElementById('rv-type').textContent = gText('order_type');
    document.getElementById('rv-name').textContent = g('full_name');
    document.getElementById('rv-phone').textContent = g('phone_number');
    document.getElementById('rv-email').textContent = g('email');
}

function previewImg(input, num) {
    const preview = document.getElementById('preview-' + num);
    const box = document.getElementById('upload-box-' + num);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            box.querySelector('.upload-icon').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Brand → Model dynamic load
document.getElementById('vehicle_brand').addEventListener('change', function() {
    const brand = this.value;
    const modelSel = document.getElementById('vehicle_model');
    if (!brand) { modelSel.innerHTML = '<option value="">Select Brand First</option>'; modelSel.disabled = true; return; }
    modelSel.innerHTML = '<option value="">Loading...</option>';
    modelSel.disabled = true;
    fetch('/get_models.php?brand=' + brand)
        .then(r => r.json())
        .then(data => {
            modelSel.innerHTML = '<option value="">Select Model</option>';
            if (data.success) data.models.forEach(m => { const o = document.createElement('option'); o.value = m; o.textContent = m; modelSel.appendChild(o); });
            modelSel.disabled = false;
        })
        .catch(() => { modelSel.innerHTML = '<option value="">Error loading models</option>'; });
});

document.getElementById('orderRequestForm').addEventListener('submit', function(e) {
    const phone = this.querySelector('[name="phone_number"]').value;
    if (!/^\+?250[0-9]{9}$/.test(phone)) {
        e.preventDefault();
        alert('Please enter a valid Rwandan phone number (e.g. +250790123456)');
        changeStep(-(currentStep - 4));
        return;
    }
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
});
</script>

<?php include '../includes/footer.php'; ?>
