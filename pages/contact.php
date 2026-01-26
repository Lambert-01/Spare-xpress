<?php
$page_title = 'Contact Us';
include '../includes/header.php';
include '../includes/navigation.php';
?>

    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6 wow fadeInUp" data-wow-delay="0.1s">Contact Us</h1>
        <ol class="breadcrumb justify-content-center mb-0 wow fadeInUp" data-wow-delay="0.3s">
            <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="/pages/contact.php">Contact</a></li>
            <li class="breadcrumb-item active text-white">Contact Us</li>
        </ol>
    </div>
    <!-- Single Page Header End -->

    <!-- Contucts Start -->
    <div class="container-fluid contact py-5">
        <div class="container py-5">
            <div class="p-5 bg-light rounded">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 900px;">
                            <h4 class="text-primary border-bottom border-primary border-2 d-inline-block pb-2">Get in
                                touch</h4>
                            <p class="mb-5 fs-5 text-dark">Your trusted source for genuine automotive spare parts in Rwanda. We're committed to providing quality parts, expert advice, and exceptional service to keep your vehicle running smoothly.</p>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h5 class="text-primary wow fadeInUp" data-wow-delay="0.1s">Let’s Connect</h5>
                        <h1 class="display-5 mb-4 wow fadeInUp" data-wow-delay="0.3s">Send Your Message</h1>
                        <p class="mb-4 wow fadeInUp" data-wow-delay="0.5s">Have questions about our automotive spare parts? Need help finding the right part for your vehicle? Our expert team is here to assist you. Contact us for inquiries about our inventory, special orders, or technical support. We respond to all inquiries within 24 hours.</p>
                        <div class="alert alert-info wow fadeInUp" data-wow-delay="0.7s">
                            <strong>SPARE XPRESS Ltd</strong> <br>
                            <small>Licensed to operate in Rwanda with expertise in automotive parts distribution.</small>
                        </div>
                        <form id="contact-form" action="/api/submit_contact.php" method="POST">
                            <div class="row g-4 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                                        <label for="name">Your Name</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                        <label for="email">Your Email</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone" required>
                                        <label for="phone">Your Phone</label>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="project" name="project" placeholder="Project">
                                        <label for="project">Your Project</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                                        <label for="subject">Subject</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Leave a message here" id="message" name="message"
                                            style="height: 160px" required></textarea>
                                        <label for="message">Message</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100" id="submit-btn">
                                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                                        Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="h-100 rounded">
                            <iframe class="rounded w-100" style="height: 100%;"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.755!2d30.1127!3d-1.9441!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca4b7c1e74b9d%3A0x4f4e4e4e4e4e4e4e!2sKagarama%2C%20Kicukiro%2C%20Kigali%2C%20Rwanda!5e0!3m2!1sen!2srw!4v1694259649153!5m2!1sen!2srw"
                                loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="row g-4 align-items-center justify-content-center">
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4>Address</h4>
                                        <p class="mb-2">Kagarama, Kicukiro<br>Umujyi wa Kigali, RWANDA</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-envelope fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4>Mail Us</h4>
                                        <p class="mb-2">argandherve@gmail.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-phone fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4>Telephone</h4>
                                        <p class="mb-2">+250 792 865 114</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">
                                <div class="rounded p-4">
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-4"
                                        style="width: 70px; height: 70px;">
                                        <i class="fas fa-building fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4>Company</h4>
                                        <p class="mb-2">SPARE XPRESS Ltd<br></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contuct End -->

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submit-btn');
    const spinner = submitBtn.querySelector('.spinner-border');
    const originalText = submitBtn.innerHTML;

    // Show loading state
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;

    const formData = new FormData(this);

    fetch('/api/submit_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showAlert('Message sent successfully! We will get back to you within 24 hours.', 'success');
            // Reset form
            document.getElementById('contact-form').reset();
        } else {
            showAlert('Error sending message: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'danger');
    })
    .finally(() => {
        // Reset button state
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
    });
});

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    const form = document.getElementById('contact-form');
    form.insertAdjacentHTML('afterend', alertHtml);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = form.nextElementSibling;
        if (alert && alert.classList.contains('alert')) {
            alert.remove();
        }
    }, 5000);
}
</script>

<?php include '../includes/footer.php'; ?>
