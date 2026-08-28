<?php include_once 'config.php'; ?>

<!-- Footer Start -->
<div class="container-fluid footer py-4 wow fadeIn" data-wow-delay="0.2s" style="background-color: #1a1a1a;">
    <div class="container py-3">
        <div class="row g-4">
            <!-- Brand & Contact -->
            <div class="col-md-6 col-lg-4">
                <div class="d-flex align-items-center mb-3">
                    <img src="/img/logo/logox.jpg" alt="Logo" style="height:44px;border-radius:.5rem;" class="me-3">
                    <div>
                        <h6 class="text-white mb-0 fw-bold">SPARE XPRESS LTD</h6>
                        <small class="text-muted">Genuine Auto Parts &middot; Rwanda</small>
                    </div>
                </div>
                <p class="text-muted small mb-3">Your trusted source for genuine vehicle spare parts in Rwanda. In-stock parts &amp; special-order sourcing from Japan, Dubai, Europe &amp; China.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="https://wa.me/250792865114" target="_blank" class="btn btn-sm" style="background:#25d366;color:#fff;"><i class="fab fa-whatsapp me-1"></i>WhatsApp</a>
                    <a href="mailto:info@sparexpressltd.com" class="btn btn-sm btn-outline-secondary"><i class="fas fa-envelope me-1"></i>Email Us</a>
                </div>
            </div>
            <!-- Hours -->
            <div class="col-md-6 col-lg-2">
                <h6 class="text-primary mb-3">Business Hours</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-1"><span class="text-light">Mon &ndash; Fri</span><br>8:00 AM &ndash; 6:00 PM</li>
                    <li class="mb-1 mt-2"><span class="text-light">Saturday</span><br>8:00 AM &ndash; 4:00 PM</li>
                    <li class="mt-2"><span class="text-danger">Sunday</span><br>Closed</li>
                </ul>
            </div>
            <!-- Customer Service -->
            <div class="col-md-6 col-lg-2">
                <h6 class="text-primary mb-3">Customer Service</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="/pages/contact.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Contact Us</a></li>
                    <li class="mb-1"><a href="/pages/order_history.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Order Tracking</a></li>
                    <li class="mb-1"><a href="/pages/my_account.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>My Account</a></li>
                    <li class="mb-1"><a href="/pages/messages.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Messages</a></li>
                </ul>
            </div>
            <!-- Shop -->
            <div class="col-md-6 col-lg-2">
                <h6 class="text-primary mb-3">Shop</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="/pages/shop.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Browse Parts</a></li>
                    <li class="mb-1"><a href="/pages/order_request.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Special Orders</a></li>
                    <li class="mb-1"><a href="/pages/brands.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Brands</a></li>
                    <li class="mb-1"><a href="/pages/cart.php" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Cart</a></li>
                </ul>
            </div>
            <!-- Newsletter -->
            <div class="col-md-6 col-lg-2">
                <h6 class="text-primary mb-3">Newsletter</h6>
                <p class="text-muted small mb-2">Get updates on new parts &amp; promotions.</p>
                <div class="input-group input-group-sm">
                    <input type="email" class="form-control" placeholder="Your email">
                    <button class="btn btn-primary btn-sm" type="button">Go</button>
                </div>
                <div class="mt-3">
                    <small class="text-muted d-block mb-1"><i class="fas fa-envelope me-1 text-primary"></i>info@sparexpressltd.com</small>
                    <small class="text-muted d-block mb-1"><i class="fas fa-phone me-1 text-primary"></i><?php echo SITE_PHONE; ?></small>
                    <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1 text-primary"></i>Kagarama, Kicukiro, Kigali</small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<!-- Copyright Start -->
<div class="container-fluid copyright py-2" style="background-color: #0f0f0f;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <span class="text-light small">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-muted">Kagarama, Kicukiro &middot; Kigali, Rwanda &middot; info@sparexpressltd.com</small>
            </div>
        </div>
    </div>
</div>
<!-- Copyright End -->

<!-- Back to Top -->
<a href="#" class="btn btn-primary btn-lg-square back-to-top"><i class="fa fa-arrow-up"></i></a>

<!-- JavaScript Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- Premium Animations & Interactions -->
<script src="/js/animations.js"></script>

<!-- Template Javascript -->
<script src="/js/main.js"></script>

<!-- Visitor Tracking -->
<script>
(function() {
    // Generate or retrieve session ID
    function getSessionId() {
        let sid = localStorage.getItem('spx_visitor_sid');
        if (!sid) {
            sid = 'v_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 12);
            localStorage.setItem('spx_visitor_sid', sid);
        }
        return sid;
    }

    // Detect device type
    function getDeviceType() {
        const w = window.innerWidth;
        if (/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) return 'mobile';
        if (/Tablet|iPad/i.test(navigator.userAgent)) return 'tablet';
        if (w <= 768) return 'mobile';
        if (w <= 1024) return 'tablet';
        return 'desktop';
    }

    // Send tracking data
    function trackVisit() {
        const data = {
            session_id: getSessionId(),
            page_url: window.location.pathname + window.location.search,
            screen_width: window.screen.width,
            screen_height: window.screen.height,
            language: navigator.language || ''
        };

        fetch('/api/track_visitor.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
            keepalive: true
        }).catch(() => {});
    }

    // Track on page load
    trackVisit();

    // Track time spent on page
    let startTime = Date.now();
    window.addEventListener('beforeunload', function() {
        const duration = Math.round((Date.now() - startTime) / 1000);
        if (duration > 2) {
            navigator.sendBeacon('/api/track_visitor.php', JSON.stringify({
                session_id: getSessionId(),
                page_url: window.location.pathname + window.location.search,
                duration: duration
            }));
        }
    });
})();
</script>
</body>
</html>
