<?php
$page_title = 'Contact Us';
include '../includes/header.php';
include '../includes/navigation.php';
?>

<!-- Page Hero -->
<div class="spx-page-hero">
    <div class="container position-relative">
        <h1 class="fw-bold mb-2"><i class="fas fa-headset me-2"></i>Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
                <li class="breadcrumb-item active">Contact Us</li>
            </ol>
        </nav>
    </div>
</div>

<div class="spx-portal-wrap py-5">
    <div class="container">

        <!-- Top CTA Row -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="spx-contact-info-card h-100">
                    <div class="spx-contact-icon blue"><i class="fas fa-phone"></i></div>
                    <div>
                        <div class="fw-700 text-dark mb-1" style="font-weight:700">Call Us</div>
                        <a href="tel:+250792865114" class="text-decoration-none text-primary fw-600">+250 792 865 114</a>
                        <div class="text-muted small mt-1">Mon–Sat, 8 AM – 6 PM</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="spx-contact-info-card h-100">
                    <div class="spx-contact-icon green"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <div class="fw-700 text-dark mb-1" style="font-weight:700">WhatsApp</div>
                        <a href="https://wa.me/250792865114" target="_blank" class="spx-whatsapp-btn mt-1">
                            <i class="fab fa-whatsapp"></i>Chat Now
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="spx-contact-info-card h-100">
                    <div class="spx-contact-icon blue"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="fw-700 text-dark mb-1" style="font-weight:700">Email</div>
                        <a href="mailto:info@sparexpressltd.com" class="text-decoration-none text-primary fw-600" style="font-size:.85rem">info@sparexpressltd.com</a>
                        <div class="text-muted small mt-1">Reply within 24 hours</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="spx-contact-info-card h-100">
                    <div class="spx-contact-icon orange"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="fw-700 text-dark mb-1" style="font-weight:700">Visit Us</div>
                        <div class="text-muted small">Kagarama, Kicukiro<br>Kigali, Rwanda</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="spx-panel">
                    <div class="spx-panel-header">
                        <h5 class="spx-panel-title"><i class="fas fa-paper-plane me-2 text-primary"></i>Send Us a Message</h5>
                    </div>
                    <div class="spx-panel-body">
                        <div id="formAlert"></div>
                        <form id="contact-form" action="/api/submit_contact.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="spx-input-group">
                                        <label for="name">Your Name *</label>
                                        <i class="fas fa-user spx-input-icon"></i>
                                        <input type="text" id="name" name="name" placeholder="Full name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spx-input-group">
                                        <label for="email">Email Address *</label>
                                        <i class="fas fa-envelope spx-input-icon"></i>
                                        <input type="email" id="email" name="email" placeholder="your@email.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spx-input-group">
                                        <label for="phone">Phone Number *</label>
                                        <i class="fas fa-phone spx-input-icon"></i>
                                        <input type="tel" id="phone" name="phone" placeholder="+250 7XX XXX XXX" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spx-input-group">
                                        <label for="subject">Subject *</label>
                                        <i class="fas fa-tag spx-input-icon"></i>
                                        <input type="text" id="subject" name="subject" placeholder="How can we help?" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="spx-input-group no-icon">
                                        <label for="message">Message *</label>
                                        <textarea id="message" name="message" rows="5" placeholder="Describe your inquiry, the part you need, or your vehicle details..." required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-3" id="submit-btn">
                                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Hours + Map -->
            <div class="col-lg-5">
                <!-- Business Hours -->
                <div class="spx-panel mb-4">
                    <div class="spx-panel-header">
                        <h5 class="spx-panel-title"><i class="fas fa-clock me-2 text-primary"></i>Business Hours</h5>
                        <span class="badge bg-success">Open Now</span>
                    </div>
                    <div class="spx-panel-body">
                        <table class="spx-hours-table">
                            <tr><td>Monday</td><td>8:00 AM – 6:00 PM</td></tr>
                            <tr><td>Tuesday</td><td>8:00 AM – 6:00 PM</td></tr>
                            <tr><td>Wednesday</td><td>8:00 AM – 6:00 PM</td></tr>
                            <tr><td>Thursday</td><td>8:00 AM – 6:00 PM</td></tr>
                            <tr><td>Friday</td><td>8:00 AM – 6:00 PM</td></tr>
                            <tr><td>Saturday</td><td>8:00 AM – 4:00 PM</td></tr>
                            <tr class="closed"><td>Sunday</td><td>Closed</td></tr>
                        </table>
                        <div class="mt-3 p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <small class="text-success fw-600"><i class="fas fa-info-circle me-1"></i>For urgent orders outside hours, WhatsApp us — we respond as soon as possible.</small>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="spx-panel">
                    <div class="spx-panel-header">
                        <h5 class="spx-panel-title"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Find Us</h5>
                    </div>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.755!2d30.1127!3d-1.9441!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca4b7c1e74b9d%3A0x4f4e4e4e4e4e4e4e!2sKagarama%2C%20Kicukiro%2C%20Kigali%2C%20Rwanda!5e0!3m2!1sen!2srw!4v1694259649153!5m2!1sen!2srw"
                        width="100%" height="220" style="border:0;display:block;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="spx-panel-body pt-3">
                        <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt text-primary me-1"></i>Kagarama, Kicukiro District, Kigali, Rwanda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const spinner = btn.querySelector('.spinner-border');
    spinner.classList.remove('d-none');
    btn.disabled = true;

    fetch('/api/submit_contact.php', { method: 'POST', body: new FormData(this) })
        .then(r => r.json())
        .then(data => {
            const alert = document.getElementById('formAlert');
            if (data.success) {
                alert.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Message sent! We\'ll reply within 24 hours.</div>';
                this.reset();
            } else {
                alert.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + (data.message || 'Error sending message.') + '</div>';
            }
        })
        .catch(() => {
            document.getElementById('formAlert').innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
        })
        .finally(() => { spinner.classList.add('d-none'); btn.disabled = false; });
});
</script>

<?php include '../includes/footer.php'; ?>
