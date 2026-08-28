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

<!-- WhatsApp Floating Button -->
<div id="whatsapp-float" style="position:fixed;bottom:20px;right:20px;z-index:9980;">
    <div id="whatsapp-popup" style="display:none;position:absolute;bottom:70px;right:0;width:300px;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);overflow:hidden;animation:fadeInUp 0.3s ease;">
        <div style="background:linear-gradient(135deg,#25d366,#128c7e);padding:16px 20px;color:#fff;">
            <div style="display:flex;align-items:center;gap:10px;">
                <img src="/img/logo/logox.jpg" alt="Logo" style="width:36px;height:36px;border-radius:8px;border:2px solid rgba(255,255,255,0.3);">
                <div>
                    <div style="font-weight:700;font-size:0.9rem;">SPARE XPRESS LTD</div>
                    <div style="font-size:0.72rem;opacity:0.85;">Typically replies in minutes</div>
                </div>
            </div>
        </div>
        <div style="padding:16px;">
            <div style="background:#f0f9f0;border-radius:12px;padding:12px;margin-bottom:12px;border-left:3px solid #25d366;">
                <div style="font-size:0.82rem;color:#333;line-height:1.5;">Hello! 👋 How can we help you today? Ask about parts, prices, or delivery.</div>
                <div style="font-size:0.68rem;color:#999;margin-top:4px;">Just now</div>
            </div>
            <a href="https://wa.me/250792865114?text=Hello%20SPARE%20XPRESS!%20I%20need%20help%20with%20auto%20parts." target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;background:#25d366;color:#fff;padding:12px;border-radius:12px;text-decoration:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;">
                <i class="fab fa-whatsapp" style="font-size:1.2rem;"></i> Start Chat
            </a>
            <div style="text-align:center;margin-top:8px;">
                <small style="color:#999;font-size:0.7rem;">Powered by WhatsApp Business</small>
            </div>
        </div>
    </div>
    <button onclick="document.getElementById('whatsapp-popup').style.display=document.getElementById('whatsapp-popup').style.display==='none'?'block':'none'" style="width:60px;height:60px;border-radius:50%;background:#25d366;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(37,211,102,0.4);display:flex;align-items:center;justify-content:center;transition:transform 0.2s;animation:pulse 2s infinite;">
        <i class="fab fa-whatsapp" style="font-size:28px;color:#fff;"></i>
    </button>
</div>

<!-- FAQ Floating Button -->
<div id="faq-float" style="position:fixed;bottom:90px;right:20px;z-index:9979;">
    <button onclick="document.getElementById('faq-popup').style.display=document.getElementById('faq-popup').style.display==='none'?'block':'none'" style="width:48px;height:48px;border-radius:50%;background:#2563eb;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(37,99,235,0.4);display:flex;align-items:center;justify-content:center;transition:transform 0.2s;">
        <i class="fas fa-question" style="font-size:18px;color:#fff;"></i>
    </button>
</div>

<!-- FAQ Popup -->
<div id="faq-popup" style="display:none;position:fixed;bottom:148px;right:20px;width:300px;max-height:400px;background:#fff;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.2);z-index:9980;overflow:hidden;animation:fadeInUp 0.3s ease;">
    <div style="background:linear-gradient(135deg,#2563eb,#1a365d);padding:14px 18px;color:#fff;">
        <div style="font-weight:700;font-size:0.95rem;">Frequently Asked Questions</div>
        <div style="font-size:0.72rem;opacity:0.8;">Quick answers to common questions</div>
    </div>
    <div style="padding:12px;max-height:320px;overflow-y:auto;">
        <details style="margin-bottom:8px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <summary style="padding:10px 12px;font-size:0.82rem;font-weight:600;cursor:pointer;background:#f8fafc;">How do I order a part?</summary>
            <div style="padding:0 12px 10px;font-size:0.78rem;color:#666;line-height:1.5;">Browse our shop for in-stock parts, or submit a special order request. We source from Japan, Dubai, Europe & China.</div>
        </details>
        <details style="margin-bottom:8px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <summary style="padding:10px 12px;font-size:0.82rem;font-weight:600;cursor:pointer;background:#f8fafc;">What payment methods do you accept?</summary>
            <div style="padding:0 12px 10px;font-size:0.78rem;color:#666;line-height:1.5;">Mobile Money (MoMo), Bank Transfer, Cash on delivery, and Card payments.</div>
        </details>
        <details style="margin-bottom:8px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <summary style="padding:10px 12px;font-size:0.82rem;font-weight:600;cursor:pointer;background:#f8fafc;">How long does delivery take?</summary>
            <div style="padding:0 12px 10px;font-size:0.78rem;color:#666;line-height:1.5;">In-stock parts: 24-48 hours. Special orders: 1-3 weeks depending on sourcing.</div>
        </details>
        <details style="margin-bottom:8px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <summary style="padding:10px 12px;font-size:0.82rem;font-weight:600;cursor:pointer;background:#f8fafc;">Do you offer warranties?</summary>
            <div style="padding:0 12px 10px;font-size:0.78rem;color:#666;line-height:1.5;">Yes! All genuine parts come with manufacturer warranty. Aftermarket parts have 30-day warranty.</div>
        </details>
        <details style="margin-bottom:8px;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <summary style="padding:10px 12px;font-size:0.82rem;font-weight:600;cursor:pointer;background:#f8fafc;">Can I track my order?</summary>
            <div style="padding:0 12px 10px;font-size:0.78rem;color:#666;line-height:1.5;">Yes! Log into your account to view order status and tracking information.</div>
        </details>
        <a href="/pages/contact.php" style="display:block;text-align:center;padding:10px;background:#eff6ff;border-radius:10px;color:#2563eb;font-weight:600;font-size:0.82rem;text-decoration:none;margin-top:8px;">View All FAQs →</a>
    </div>
</div>

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
