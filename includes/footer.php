<?php include_once 'config.php'; ?>

<!-- Footer Start -->
<div class="container-fluid footer py-3 wow fadeIn" data-wow-delay="0.2s" style="background-color: #1a1a1a;">
    <div class="container py-3">
        <!-- Contact Info Section -->
        <div class="row g-3 mb-3">
            <div class="col-md-6 col-lg-6 text-center text-md-start">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-map-marker-alt fa-lg text-primary me-2"></i>
                    <div>
                        <h6 class="text-white mb-0">Address: <?php echo SITE_ADDRESS; ?></h6>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-envelope fa-lg text-primary me-2"></i>
                    <div>
                        <h6 class="text-white mb-0">Email: <?php echo SITE_EMAIL; ?></h6>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 text-center text-md-start">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-phone fa-lg text-primary me-2"></i>
                    <div>
                        <h6 class="text-white mb-0">Telephone: <?php echo SITE_PHONE; ?></h6>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-headset fa-lg text-primary me-2"></i>
                    <div>
                        <h6 class="text-white mb-0">Customer Support: <?php echo SITE_URL; ?> | <?php echo SITE_PHONE; ?></h6>
                    </div>
                </div>
            </div>
        </div>
        <!-- Links and Newsletter Section -->
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <h6 class="text-primary mb-3">Newsletter</h6>
                <p class="text-light small mb-2">Stay updated with new spare parts, promotions, and expert tips for keeping your vehicle in top condition.</p>
                <div class="input-group input-group-sm">
                    <input type="email" class="form-control" placeholder="Enter your email" aria-label="Email">
                    <button class="btn btn-primary btn-sm" type="button">Subscribe</button>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <h6 class="text-primary mb-3">Customer Service</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Contact Us</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Order Tracking</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Returns & Refunds</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>My Account</a></li>
                </ul>
            </div>
            <div class="col-md-6 col-lg-3">
                <h6 class="text-primary mb-3">Information</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>About Us</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Delivery & Shipping</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Privacy Policy</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Terms & Conditions</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>FAQ</a></li>
                </ul>
            </div>
            <div class="col-md-6 col-lg-3">
                <h6 class="text-primary mb-3">Extras</h6>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Brands</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Wishlist</a></li>
                    <li class="mb-1"><a href="#" class="text-light text-decoration-none hover-link"><i class="fas fa-angle-right me-1"></i>Order History</a></li>
                </ul>
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
                <span class="text-light small">© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <!-- Optional: Add payment methods or social links if needed -->
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
<script src="/lib/wow/wow.min.js"></script>
<script src="/lib/owlcarousel/owl.carousel.min.js"></script>

<!-- Premium Animations & Interactions -->
<script src="/js/animations.js"></script>

<!-- Template Javascript -->
<script src="/js/main.js"></script>
</body>
</html>