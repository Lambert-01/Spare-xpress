<?php
/**
 * SEO Helper Functions for SPARE XPRESS LTD
 * Handles meta tags, Open Graph, Twitter Cards, JSON-LD structured data, canonical URLs
 */

// Only load SEO on page requests, not API/AJAX calls
if (!defined('SPX_SEO_LOADED') && !is_api_request()) {
    define('SPX_SEO_LOADED', true);
}

function is_api_request() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $http_x = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return strpos($uri, '/api/') !== false || strtolower($http_x) === 'xmlhttprequest';
}

// ─── Page Defaults ──────────────────────────────────────────────────────
$seo_defaults = [
    'home' => [
        'title'       => 'SPARE XPRESS LTD | Genuine Auto Parts in Rwanda - Buy Vehicle Spare Parts Online',
        'description' => 'Buy genuine vehicle spare parts in Rwanda. SPARE XPRESS LTD offers OEM & aftermarket parts for Toyota, Honda, BMW, Mercedes & 30+ brands. Fast delivery across Kigali & Rwanda. In-stock parts & special order sourcing.',
        'keywords'    => 'auto parts Rwanda, spare parts Kigali, vehicle spare parts, car parts Rwanda, Toyota parts Rwanda, Honda parts, BMW parts, genuine auto parts, brake pads, engine parts, oil filter, car accessories Rwanda, automotive store Kigali, car spares online, vehicle maintenance parts',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/',
    ],
    'shop' => [
        'title'       => 'Shop Auto Parts Online | Genuine Vehicle Spare Parts in Rwanda - SPARE XPRESS',
        'description' => 'Browse our complete catalog of genuine auto parts in Rwanda. Filter by brand, model, year & category. Toyota, Honda, BMW, Mercedes parts available. Fast delivery across Rwanda. OEM & aftermarket parts.',
        'keywords'    => 'buy auto parts online Rwanda, shop spare parts, car parts catalog, vehicle parts store, engine parts, brake pads, oil filters, suspension parts, transmission parts, exhaust parts, electrical parts Rwanda',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/shop.php',
    ],
    'brands' => [
        'title'       => 'Auto Parts by Brand | Toyota, Honda, BMW, Mercedes Parts - SPARE XPRESS Rwanda',
        'description' => 'Find genuine auto parts by brand at SPARE XPRESS LTD. We stock parts for 30+ automotive brands including Toyota, Honda, BMW, Mercedes-Benz, Hyundai, Kia, Nissan & more. All brands available in Rwanda.',
        'keywords'    => 'Toyota parts Rwanda, Honda parts, BMW parts, Mercedes parts, Hyundai parts, Kia parts, Nissan parts, Volkswagen parts, Subaru parts, Mazda parts, automotive brands Rwanda',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/brands.php',
    ],
    'contact' => [
        'title'       => 'Contact Us | SPARE XPRESS LTD - Auto Parts Store in Kigali, Rwanda',
        'description' => 'Contact SPARE XPRESS LTD for genuine auto parts in Rwanda. Visit us at Kagarama, Kicukiro, Kigali. Call +250 792 865 114 or WhatsApp us. Open Mon-Sat 8AM-6PM.',
        'keywords'    => 'contact spare xpress, auto parts Kigali, car parts shop Rwanda, spare parts store Kigali, automotive shop Kicukiro, Kagarama auto parts',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/contact.php',
    ],
    'order_request' => [
        'title'       => 'Request Auto Parts | Special Order Vehicle Spare Parts - SPARE XPRESS Rwanda',
        'description' => 'Can\'t find a part? Submit a special order request at SPARE XPRESS LTD. We source from Japan, Dubai, Europe & China. Get a quote within 24 hours. Genuine parts guaranteed.',
        'keywords'    => 'order auto parts Rwanda, special order car parts, request spare parts, import auto parts Rwanda, source car parts Japan, Dubai auto parts',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/order_request.php',
    ],
    'cart' => [
        'title'       => 'Shopping Cart | SPARE XPRESS LTD - Auto Parts Rwanda',
        'description' => 'Review your shopping cart at SPARE XPRESS LTD. Secure checkout with mobile money, bank transfer, or card payment.',
        'keywords'    => 'shopping cart, auto parts checkout, buy car parts online',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/cart.php',
    ],
    'login' => [
        'title'       => 'Login | My Account - SPARE XPRESS LTD',
        'description' => 'Login to your SPARE XPRESS LTD account to track orders, view order history, manage your profile, and request auto parts.',
        'keywords'    => 'login, my account, order tracking, spare xpress account',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/login.php',
    ],
    'register' => [
        'title'       => 'Create Account | Register - SPARE XPRESS LTD',
        'description' => 'Create your SPARE XPRESS LTD account to order auto parts online, track deliveries, and get exclusive offers.',
        'keywords'    => 'register, create account, sign up, spare xpress',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/register.php',
    ],
    'order_history' => [
        'title'       => 'Order History | Track Your Orders - SPARE XPRESS LTD',
        'description' => 'View your order history and track your auto parts orders at SPARE XPRESS LTD. Real-time delivery updates.',
        'keywords'    => 'order history, track orders, delivery status, order tracking',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/order_history.php',
    ],
    'bestseller' => [
        'title'       => 'Bestselling Auto Parts | Top Selling Car Parts in Rwanda - SPARE XPRESS',
        'description' => 'Discover our bestselling auto parts in Rwanda. Most popular brake pads, oil filters, engine parts & accessories. Trusted by thousands of Rwandan car owners.',
        'keywords'    => 'bestselling auto parts, popular car parts, top selling spare parts, most bought car parts Rwanda',
        'image'       => '/img/logo/logox.jpg',
        'type'        => 'website',
        'canonical'   => '/pages/bestseller.php',
    ],
];

// ─── SEO Page Key Mapping ──────────────────────────────────────────────
function spx_get_seo_page_key() {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);
    $path = strtolower(trim($path, '/'));

    if ($path === '' || $path === 'index.php') return 'home';
    if (strpos($path, 'shop') !== false) return 'shop';
    if (strpos($path, 'brands') !== false) return 'brands';
    if (strpos($path, 'contact') !== false) return 'contact';
    if (strpos($path, 'order_request') !== false) return 'order_request';
    if (strpos($path, 'cart') !== false) return 'cart';
    if (strpos($path, 'login') !== false) return 'login';
    if (strpos($path, 'register') !== false) return 'register';
    if (strpos($path, 'order_history') !== false) return 'order_history';
    if (strpos($path, 'bestseller') !== false) return 'bestseller';
    if (strpos($path, 'models') !== false) return 'brands';

    return 'home';
}

// ─── Get SEO Data for Current Page ─────────────────────────────────────
function spx_get_seo_data($page_key = null, $overrides = []) {
    global $seo_defaults;
    if (!$page_key) $page_key = spx_get_seo_page_key();
    $base = $seo_defaults[$page_key] ?? $seo_defaults['home'];
    return array_merge($base, $overrides);
}

// ─── Canonical URL ─────────────────────────────────────────────────────
function spx_canonical_url($path = null) {
    if (!$path) {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
    }
    // Remove query string for canonical
    $path = strtok($path, '?');
    // Remove trailing slash except root
    $path = rtrim($path, '/');
    if (empty($path)) $path = '/';
    return SITE_URL . $path;
}

// ─── Render Meta Tags ──────────────────────────────────────────────────
function spx_render_meta_tags($seo_data) {
    if (is_api_request()) return;
    $url = spx_canonical_url($seo_data['canonical'] ?? null);
    $img = $seo_data['image'];
    if (strpos($img, 'http') !== 0) {
        $img = SITE_URL . $img;
    }
    $desc = htmlspecialchars($seo_data['description'], ENT_QUOTES, 'UTF-8');
    $title = htmlspecialchars($seo_data['title'], ENT_QUOTES, 'UTF-8');
    $keywords = htmlspecialchars($seo_data['keywords'], ENT_QUOTES, 'UTF-8');

    echo "\n<!-- ═══════ SEO Meta Tags ═══════ -->\n";
    echo "<meta name=\"description\" content=\"{$desc}\">\n";
    echo "<meta name=\"keywords\" content=\"{$keywords}\">\n";
    echo "<meta name=\"author\" content=\"SPARE XPRESS LTD\">\n";
    echo "<meta name=\"robots\" content=\"index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1\">\n";
    echo "<meta name=\"googlebot\" content=\"index, follow\">\n";
    echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    echo "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n";
    echo "<meta name=\"theme-color\" content=\"#2563eb\">\n";

    // Canonical
    echo "<link rel=\"canonical\" href=\"{$url}\">\n";

    // Open Graph
    echo "\n<!-- ═══════ Open Graph (Facebook, LinkedIn) ═══════ -->\n";
    echo "<meta property=\"og:type\" content=\"{$seo_data['type']}\">\n";
    echo "<meta property=\"og:title\" content=\"{$title}\">\n";
    echo "<meta property=\"og:description\" content=\"{$desc}\">\n";
    echo "<meta property=\"og:image\" content=\"{$img}\">\n";
    echo "<meta property=\"og:image:width\" content=\"1200\">\n";
    echo "<meta property=\"og:image:height\" content=\"630\">\n";
    echo "<meta property=\"og:image:alt\" content=\"SPARE XPRESS LTD - Genuine Auto Parts\">\n";
    echo "<meta property=\"og:url\" content=\"{$url}\">\n";
    echo "<meta property=\"og:site_name\" content=\"SPARE XPRESS LTD\">\n";
    echo "<meta property=\"og:locale\" content=\"en_US\">\n";

    // Twitter Card
    echo "\n<!-- ═══════ Twitter Card ═══════ -->\n";
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    echo "<meta name=\"twitter:title\" content=\"{$title}\">\n";
    echo "<meta name=\"twitter:description\" content=\"{$desc}\">\n";
    echo "<meta name=\"twitter:image\" content=\"{$img}\">\n";
    echo "<meta name=\"twitter:site\" content=\"@SpareXpress\">\n";

    // Geo Tags (Rwanda)
    echo "\n<!-- ═══════ Geo Tags ═══════ -->\n";
    echo "<meta name=\"geo.region\" content=\"RW\">\n";
    echo "<meta name=\"geo.placename\" content=\"Kigali\">\n";
    echo "<meta name=\"geo.position\" content=\"-1.9403;29.8739\">\n";
    echo "<meta name=\"ICBM\" content=\"-1.9403, 29.8739\">\n";
    echo "<!-- ═══════ End SEO Meta Tags ═══════ -->\n\n";
}

// ─── JSON-LD: LocalBusiness ────────────────────────────────────────────
function spx_jsonld_local_business() {
    if (is_api_request()) return;
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'AutoPartsStore',
        'name'        => 'SPARE XPRESS LTD',
        'description' => 'Your trusted source for genuine vehicle spare parts in Rwanda. OEM & aftermarket parts for 30+ automotive brands.',
        'url'         => SITE_URL,
        'logo'        => SITE_URL . '/img/logo/logox.jpg',
        'image'       => SITE_URL . '/img/logo/logox.jpg',
        'telephone'   => '+250792865114',
        'email'       => 'info@sparexpressltd.com',
        'address'     => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Kagarama, Kicukiro',
            'addressLocality' => 'Kigali',
            'addressRegion'   => 'Kigali',
            'postalCode'      => '00000',
            'addressCountry'  => 'RW',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => -1.9403,
            'longitude' => 29.8739,
        ],
        'openingHoursSpecification' => [
            [
                '@type'         => 'OpeningHoursSpecification',
                'dayOfWeek'     => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens'         => '08:00',
                'closes'        => '18:00',
            ],
            [
                '@type'         => 'OpeningHoursSpecification',
                'dayOfWeek'     => 'Saturday',
                'opens'         => '08:00',
                'closes'        => '16:00',
            ],
        ],
        'priceRange'    => 'RWF',
        'paymentAccepted' => 'Mobile Money, Cash, Bank Transfer, Credit Card',
        'currenciesAccepted' => 'RWF',
        'areaServed'    => [
            '@type'  => 'Country',
            'name'   => 'Rwanda',
        ],
        'sameAs' => [
            'https://www.facebook.com/sparexpressltd',
            'https://wa.me/250792865114',
        ],
        'hasMap' => 'https://maps.google.com/?q=Kagarama+Kicukiro+Kigali+Rwanda',
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.8',
            'reviewCount' => '150',
            'bestRating'  => '5',
            'worstRating' => '1',
        ],
    ];

    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}

// ─── JSON-LD: BreadcrumbList ───────────────────────────────────────────
function spx_jsonld_breadcrumbs($items) {
    if (is_api_request()) return;
    // $items = [['name' => 'Home', 'url' => '/'], ['name' => 'Shop', 'url' => '/pages/shop.php']]
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [],
    ];

    foreach ($items as $i => $item) {
        $schema['itemListElement'][] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $item['name'],
            'item'     => SITE_URL . $item['url'],
        ];
    }

    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}

// ─── JSON-LD: Product ──────────────────────────────────────────────────
function spx_jsonld_product($product) {
    if (is_api_request()) return;
    $img = $product['image'] ?? '/img/no-image.png';
    if (strpos($img, 'http') !== 0) $img = SITE_URL . '/' . ltrim($img, '/');

    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Product',
        'name'          => $product['name'] ?? '',
        'description'   => $product['description'] ?? $product['name'] ?? '',
        'image'         => $img,
        'brand'         => [
            '@type' => 'Brand',
            'name'  => $product['brand_name'] ?? 'SPARE XPRESS',
        ],
        'sku'           => $product['sku'] ?? '',
        'url'           => SITE_URL . '/pages/shop.php?product=' . ($product['slug'] ?? ''),
        'offers'        => [
            '@type'           => 'Offer',
            'url'             => SITE_URL . '/pages/shop.php?product=' . ($product['slug'] ?? ''),
            'priceCurrency'   => 'RWF',
            'price'           => $product['sale_price'] ?? $product['regular_price'] ?? '0',
            'availability'    => ($product['stock_quantity'] ?? 0) > 0
                ? 'https://schema.org/InStock'
                : 'https://schema.org/OutOfStock',
            'itemCondition'   => 'https://schema.org/NewCondition',
            'seller'          => [
                '@type' => 'AutoPartsStore',
                'name'  => 'SPARE XPRESS LTD',
            ],
        ],
        'category'      => $product['category_name'] ?? 'Auto Parts',
    ];

    if (!empty($product['rating'])) {
        $schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => $product['rating'],
            'reviewCount' => $product['review_count'] ?? 1,
        ];
    }

    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}

// ─── JSON-LD: WebSite (Sitelinks Searchbox) ────────────────────────────
function spx_jsonld_website() {
    if (is_api_request()) return;
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => 'SPARE XPRESS LTD',
        'url'             => SITE_URL,
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'        => 'EntryPoint',
                'urlTemplate'  => SITE_URL . '/pages/shop.php?search={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}

// ─── JSON-LD: FAQPage ──────────────────────────────────────────────────
function spx_jsonld_faq($faqs) {
    if (is_api_request()) return;
    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [],
    ];

    foreach ($faqs as $faq) {
        $schema['mainEntity'][] = [
            '@type'          => 'Question',
            'name'           => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $faq['answer'],
            ],
        ];
    }

    echo "<script type=\"application/ld+json\">\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}

// ─── Preload Hints ─────────────────────────────────────────────────────
function spx_preload_hints() {
    if (is_api_request()) return;
    echo "<!-- Preload critical resources -->\n";
    echo "<link rel=\"preload\" href=\"/css/style.css\" as=\"style\">\n";
    echo "<link rel=\"preload\" href=\"/css/mobile.css\" as=\"style\">\n";
    echo "<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n";
    echo "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n";
    echo "<link rel=\"dns-prefetch\" href=\"https://cdnjs.cloudflare.com\">\n";
    echo "<link rel=\"dns-prefetch\" href=\"https://ajax.googleapis.com\">\n";
    echo "<link rel=\"dns-prefetch\" href=\"https://cdn.jsdelivr.net\">\n";
}
?>
