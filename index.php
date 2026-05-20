<?php
$page_title = 'Home';
include 'includes/header.php';
include 'includes/navigation.php';
?>

<!-- ===================== HERO ===================== -->
<section class="spx-hero">
    <div class="spx-hero-bg"></div>
    <div class="container spx-hero-inner">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="spx-hero-badge"><i class="fas fa-map-marker-alt me-1"></i>Kigali, Rwanda</span>
                <h1 class="spx-hero-title">Genuine Auto Parts,<br><span class="spx-hero-accent">Delivered Fast</span></h1>
                <p class="spx-hero-sub">In-stock parts you can buy today. Can't find it? We source from Japan, Dubai, Europe &amp; China.</p>
                <div class="spx-hero-pills">
                    <span><i class="fas fa-shield-alt"></i> Genuine Parts</span>
                    <span><i class="fas fa-truck"></i> Rwanda Delivery</span>
                    <span><i class="fas fa-headset"></i> Expert Support</span>
                </div>
                <div class="spx-hero-cta">
                    <a href="pages/shop.php" class="btn btn-primary btn-lg"><i class="fas fa-store me-2"></i>Shop Parts</a>
                    <a href="pages/order_request.php" class="spx-hero-link"><i class="fas fa-search me-1"></i>Request a Part</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <div class="spx-hero-card-wrap">
                    <img src="img/logo/logox.jpg" alt="SPARE XPRESS" class="spx-hero-img">
                    <div class="spx-hero-float spx-hero-float-1"><i class="fas fa-check-circle text-success me-1"></i>Verified Quality</div>
                    <div class="spx-hero-float spx-hero-float-2"><i class="fas fa-globe text-primary me-1"></i>Global Sourcing</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== TRUST BAR ===================== -->
<section class="spx-trust-bar">
    <div class="container">
        <div class="spx-trust-grid">
            <div class="spx-trust-item">
                <i class="fas fa-shield-alt"></i>
                <div><strong>100% Genuine</strong><span>Authentic parts only</span></div>
            </div>
            <div class="spx-trust-item">
                <i class="fas fa-truck"></i>
                <div><strong>Fast Delivery</strong><span>Across Rwanda</span></div>
            </div>
            <div class="spx-trust-item">
                <i class="fas fa-globe"></i>
                <div><strong>Global Sourcing</strong><span>Japan · Dubai · Europe</span></div>
            </div>
            <div class="spx-trust-item">
                <i class="fas fa-headset"></i>
                <div><strong>Expert Support</strong><span>Mon–Sat 8AM–6PM</span></div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== BRANDS ===================== -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="spx-section-head">
            <span class="spx-section-tag">Vehicle Brands</span>
            <h2>Shop by Brand</h2>
            <p>Genuine parts for all major automotive brands available in Rwanda</p>
        </div>
        <div class="row g-3 justify-content-center">
            <?php foreach ($brands as $brand):
                $logo = 'img/no-image.png';
                if (!empty($brand['logo_image'])) {
                    $clean = 'uploads/brands/' . basename(str_replace(['../../','../'], '', $brand['logo_image']));
                    if (file_exists($clean)) $logo = $clean;
                }
            ?>
            <div class="col-4 col-sm-3 col-md-2">
                <a href="pages/models.php?brand=<?php echo urlencode($brand['slug']); ?>" class="spx-brand-card">
                    <img src="<?php echo $logo; ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>" onerror="this.src='img/no-image.png'">
                    <span><?php echo htmlspecialchars($brand['name']); ?></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== CATEGORIES ===================== -->
<section class="py-5">
    <div class="container">
        <div class="spx-section-head">
            <span class="spx-section-tag">Parts Catalog</span>
            <h2>Shop by Category</h2>
            <p>Browse our full range of in-stock spare parts by category</p>
        </div>
        <div class="row g-3 justify-content-center">
            <?php foreach ($categories as $cat): ?>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="pages/shop.php?category=<?php echo urlencode($cat['slug']); ?>" class="spx-cat-card">
                    <i class="<?php echo $cat['icon']; ?>"></i>
                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== QUICK SEARCH ===================== -->
<section class="spx-search-section">
    <div class="container">
        <div class="spx-section-head" style="color:#fff;">
            <span class="spx-section-tag" style="background:rgba(255,255,255,.15);color:#fff;">Find Your Part</span>
            <h2 style="color:#fff;">Search Our Catalog</h2>
            <p style="color:rgba(255,255,255,.75);">Filter by brand, model, year and category</p>
        </div>
        <div class="spx-search-card">
            <form id="quickSearchForm" class="row g-3 align-items-end" action="pages/shop.php" method="GET">
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="spx-search-label"><i class="fas fa-car me-1"></i>Brand</label>
                    <select class="form-select" id="qs-brand" name="brand">
                        <option value="">All Brands</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="spx-search-label"><i class="fas fa-car-side me-1"></i>Model</label>
                    <select class="form-select" id="qs-model" name="model" disabled>
                        <option value="">Select Brand</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="spx-search-label"><i class="fas fa-calendar me-1"></i>Year</label>
                    <select class="form-select" id="qs-year" name="year_from">
                        <option value="">Any Year</option>
                        <?php for ($y = date('Y'); $y >= 1990; $y--): ?>
                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="spx-search-label"><i class="fas fa-cogs me-1"></i>Category</label>
                    <select class="form-select" id="qs-category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-8 col-lg-3">
                    <label class="spx-search-label"><i class="fas fa-search me-1"></i>Part Name / SKU</label>
                    <input type="text" class="form-control" name="search" placeholder="e.g. Brake Pads, Oil Filter...">
                </div>
                <div class="col-12 col-md-4 col-lg-1">
                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ===================== HOW IT WORKS ===================== -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="spx-section-head">
            <span class="spx-section-tag">Process</span>
            <h2>How It Works</h2>
            <p>Simple 4-step process to get the part you need</p>
        </div>
        <div class="row g-4">
            <?php
            $steps = [
                ['fas fa-search',       '1', 'Search or Request',    'Browse in-stock parts or submit a special order request for rare parts.'],
                ['fas fa-money-bill-wave','2','Confirm & Pay 50%',   'Pay a 50% deposit via mobile money to confirm your special order.'],
                ['fas fa-headset',      '3', 'We Source & Verify',   'Our team verifies fitment and sources your part from trusted suppliers.'],
                ['fas fa-truck',        '4', 'Pickup or Delivery',   'Pay the balance and receive your genuine part with warranty.'],
            ];
            foreach ($steps as $s): ?>
            <div class="col-6 col-md-3">
                <div class="spx-how-card">
                    <div class="spx-how-num"><?php echo $s[1]; ?></div>
                    <div class="spx-how-icon"><i class="<?php echo $s[0]; ?>"></i></div>
                    <h6><?php echo $s[2]; ?></h6>
                    <p><?php echo $s[3]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================== CTA BANNER ===================== -->
<section class="spx-cta-banner">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <h3 class="text-white fw-bold mb-2">Can't find the part you need?</h3>
                <p class="text-white mb-0" style="opacity:.85;">We source from Japan, Dubai, Europe &amp; China. Submit a request and we'll get back to you within 24 hours.</p>
            </div>
            <div class="col-md-4 text-md-end d-flex gap-3 justify-content-md-end flex-wrap">
                <a href="pages/order_request.php" class="btn btn-light fw-600"><i class="fas fa-clipboard-list me-2"></i>Request a Part</a>
                <a href="https://wa.me/250792865114" target="_blank" class="spx-whatsapp-btn"><i class="fab fa-whatsapp"></i>WhatsApp</a>
            </div>
        </div>
    </div>
</section>

<style>
/* ---- HERO ---- */
.spx-hero {
    background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
    position: relative;
    overflow: hidden;
    padding: 4rem 0 3.5rem;
}
.spx-hero-bg {
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.spx-hero-inner { position: relative; z-index: 1; }
.spx-hero-badge {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,.15); color: #fff;
    padding: .35rem .9rem; border-radius: 999px;
    font-size: .8rem; font-weight: 600;
    margin-bottom: 1.25rem;
    border: 1px solid rgba(255,255,255,.2);
}
.spx-hero-title {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800; color: #fff;
    line-height: 1.15; margin-bottom: 1rem;
}
.spx-hero-accent {
    background: linear-gradient(90deg, #fbbf24, #f97316);
    -webkit-background-clip: text; background-clip: text;
    -webkit-text-fill-color: transparent;
}
.spx-hero-sub { color: rgba(255,255,255,.85); font-size: 1rem; margin-bottom: 1.5rem; max-width: 520px; }
.spx-hero-pills { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.75rem; }
.spx-hero-pills span {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); color: #fff;
    padding: .3rem .8rem; border-radius: 999px;
    font-size: .78rem; font-weight: 500;
    border: 1px solid rgba(255,255,255,.15);
}
.spx-hero-pills span i { font-size: .75rem; }
.spx-hero-cta { display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; }
.spx-hero-link {
    color: rgba(255,255,255,.85); font-weight: 600; font-size: .9rem;
    text-decoration: none; display: flex; align-items: center; gap: .4rem;
    transition: color .2s;
}
.spx-hero-link:hover { color: #fff; }
.spx-hero-card-wrap { position: relative; display: inline-block; }
.spx-hero-img {
    width: 320px; height: 320px; object-fit: cover;
    border-radius: 1.5rem;
    box-shadow: 0 24px 60px rgba(0,0,0,.35);
    border: 4px solid rgba(255,255,255,.15);
}
.spx-hero-float {
    position: absolute;
    background: rgba(255,255,255,.95);
    padding: .5rem 1rem; border-radius: 999px;
    font-size: .8rem; font-weight: 600; color: #111827;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    white-space: nowrap;
}
.spx-hero-float-1 { top: 1.5rem; right: -1.5rem; }
.spx-hero-float-2 { bottom: 2rem; left: -1.5rem; }

/* ---- TRUST BAR ---- */
.spx-trust-bar {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.25rem 0;
}
.spx-trust-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.spx-trust-item {
    display: flex; align-items: center; gap: .875rem;
    padding: .75rem 1rem;
}
.spx-trust-item i {
    font-size: 1.5rem; color: #2563eb;
    flex-shrink: 0;
}
.spx-trust-item strong { display: block; font-size: .875rem; color: #111827; }
.spx-trust-item span { font-size: .75rem; color: #6b7280; }

/* ---- SECTION HEADER ---- */
.spx-section-head { text-align: center; margin-bottom: 2.5rem; }
.spx-section-tag {
    display: inline-block;
    background: #eff6ff; color: #2563eb;
    padding: .3rem .9rem; border-radius: 999px;
    font-size: .78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: .75rem;
}
.spx-section-head h2 { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 800; color: #111827; margin-bottom: .5rem; }
.spx-section-head p { color: #6b7280; font-size: .9rem; max-width: 520px; margin: 0 auto; }

/* ---- BRAND CARDS ---- */
.spx-brand-card {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .5rem;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: .875rem; padding: 1rem .75rem;
    text-decoration: none;
    transition: all .2s;
    min-height: 110px;
}
.spx-brand-card:hover { border-color: #2563eb; box-shadow: 0 4px 16px rgba(37,99,235,.12); transform: translateY(-3px); }
.spx-brand-card img { width: 52px; height: 52px; object-fit: contain; }
.spx-brand-card span { font-size: .75rem; font-weight: 600; color: #374151; text-align: center; }

/* ---- CATEGORY CARDS ---- */
.spx-cat-card {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: .5rem;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: .875rem; padding: 1.25rem .75rem;
    text-decoration: none;
    transition: all .2s;
    min-height: 110px;
}
.spx-cat-card:hover { border-color: #2563eb; background: #eff6ff; transform: translateY(-3px); box-shadow: 0 4px 16px rgba(37,99,235,.1); }
.spx-cat-card i { font-size: 1.75rem; color: #2563eb; }
.spx-cat-card span { font-size: .75rem; font-weight: 600; color: #374151; text-align: center; }

/* ---- SEARCH SECTION ---- */
.spx-search-section {
    background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
    padding: 3.5rem 0;
    position: relative;
}
.spx-search-card {
    background: #fff;
    border-radius: 1rem;
    padding: 1.75rem;
    box-shadow: 0 8px 32px rgba(0,0,0,.15);
}
.spx-search-label { font-size: .8rem; font-weight: 600; color: #374151; margin-bottom: .35rem; display: block; }

/* ---- HOW IT WORKS ---- */
.spx-how-card {
    background: #fff; border-radius: 1rem;
    padding: 1.5rem 1.25rem;
    text-align: center;
    border: 1px solid #e5e7eb;
    height: 100%;
    transition: all .2s;
}
.spx-how-card:hover { border-color: #2563eb; box-shadow: 0 4px 20px rgba(37,99,235,.1); transform: translateY(-3px); }
.spx-how-num {
    width: 32px; height: 32px; border-radius: 50%;
    background: #2563eb; color: #fff;
    font-size: .85rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .75rem;
}
.spx-how-icon { font-size: 1.75rem; color: #2563eb; margin-bottom: .75rem; }
.spx-how-card h6 { font-weight: 700; color: #111827; font-size: .9rem; margin-bottom: .5rem; }
.spx-how-card p { font-size: .78rem; color: #6b7280; margin: 0; line-height: 1.5; }

/* ---- CTA BANNER ---- */
.spx-cta-banner {
    background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
    padding: 3rem 0;
}

/* ---- RESPONSIVE ---- */
@media (max-width: 991px) {
    .spx-trust-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 575px) {
    .spx-hero { padding: 2.5rem 0 2rem; }
    .spx-hero-title { font-size: 1.75rem; }
    .spx-hero-sub { font-size: .875rem; }
    .spx-trust-grid { grid-template-columns: repeat(2, 1fr); gap: .5rem; }
    .spx-trust-item { padding: .5rem; gap: .5rem; }
    .spx-trust-item i { font-size: 1.2rem; }
    .spx-trust-item strong { font-size: .78rem; }
    .spx-trust-item span { font-size: .7rem; }
    .spx-hero-cta { gap: .875rem; }
    .spx-hero-float { display: none; }
}
</style>

<script>
// Quick search: load brands
fetch('/api/get_filters.php')
    .then(r => r.json())
    .then(data => {
        if (data.brands) {
            const sel = document.getElementById('qs-brand');
            data.brands.forEach(b => {
                const o = document.createElement('option');
                o.value = b.slug; o.textContent = b.name;
                sel.appendChild(o);
            });
        }
    }).catch(() => {});

document.getElementById('qs-brand').addEventListener('change', function() {
    const brand = this.value;
    const modelSel = document.getElementById('qs-model');
    if (!brand) { modelSel.innerHTML = '<option value="">Select Brand</option>'; modelSel.disabled = true; return; }
    modelSel.innerHTML = '<option value="">Loading...</option>'; modelSel.disabled = true;
    fetch('/get_models.php?brand=' + brand)
        .then(r => r.json())
        .then(data => {
            modelSel.innerHTML = '<option value="">All Models</option>';
            if (data.success) data.models.forEach(m => { const o = document.createElement('option'); o.value = m; o.textContent = m; modelSel.appendChild(o); });
            modelSel.disabled = false;
        }).catch(() => { modelSel.innerHTML = '<option value="">Error</option>'; });
});
</script>

<?php include 'includes/footer.php'; ?>
