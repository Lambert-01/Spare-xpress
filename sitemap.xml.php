<?php
/**
 * Dynamic XML Sitemap Generator for SPARE XPRESS LTD
 * Generates sitemap.xml with all pages, brands, models, and products
 */

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex'); // Don't index the sitemap itself

require_once __DIR__ . '/includes/config.php';

$base_url = SITE_URL ?: 'https://sparexpressltd.com';
$now = date('c');

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

// ─── Static Pages ──────────────────────────────────────────────────────
$static_pages = [
    ['url' => '/',                        'priority' => '1.0', 'changefreq' => 'daily'],
    ['url' => '/pages/shop.php',          'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => '/pages/brands.php',        'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/pages/order_request.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['url' => '/pages/contact.php',       'priority' => '0.7', 'changefreq' => 'monthly'],
    ['url' => '/pages/bestseller.php',    'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/pages/login.php',         'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => '/pages/register.php',      'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => '/pages/my_account.php',    'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => '/pages/order_history.php', 'priority' => '0.4', 'changefreq' => 'monthly'],
    ['url' => '/pages/cart.php',          'priority' => '0.5', 'changefreq' => 'daily'],
];

foreach ($static_pages as $page) {
    echo '<url>';
    echo '<loc>' . htmlspecialchars($base_url . $page['url']) . '</loc>';
    echo '<lastmod>' . $now . '</lastmod>';
    echo '<changefreq>' . $page['changefreq'] . '</changefreq>';
    echo '<priority>' . $page['priority'] . '</priority>';
    echo '</url>';
}

// ─── Brand Pages ───────────────────────────────────────────────────────
try {
    $result = $conn->query("SELECT slug, updated_at FROM vehicle_brands_enhanced WHERE is_active = 1 ORDER BY brand_name");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lastmod = !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now;
            echo '<url>';
            echo '<loc>' . htmlspecialchars($base_url . '/pages/brands.php?brand=' . urlencode($row['slug'])) . '</loc>';
            echo '<lastmod>' . $lastmod . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.7</priority>';
            echo '</url>';
        }
    }
} catch (Exception $e) { }

// ─── Model Pages ───────────────────────────────────────────────────────
try {
    $result = $conn->query("SELECT slug, brand_slug, updated_at FROM vehicle_models_enhanced WHERE is_active = 1 ORDER BY model_name");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lastmod = !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now;
            $brand = !empty($row['brand_slug']) ? $row['brand_slug'] : '';
            echo '<url>';
            echo '<loc>' . htmlspecialchars($base_url . '/pages/models.php?brand=' . urlencode($brand) . '&model=' . urlencode($row['slug'])) . '</loc>';
            echo '<lastmod>' . $lastmod . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.6</priority>';
            echo '</url>';
        }
    }
} catch (Exception $e) { }

// ─── Product Pages ─────────────────────────────────────────────────────
try {
    $result = $conn->query("SELECT slug, updated_at FROM spare_parts WHERE status = 'active' AND is_active = 1 ORDER BY name LIMIT 5000");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $lastmod = !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now;
            echo '<url>';
            echo '<loc>' . htmlspecialchars($base_url . '/pages/shop.php?product=' . urlencode($row['slug'])) . '</loc>';
            echo '<lastmod>' . $lastmod . '</lastmod>';
            echo '<changefreq>weekly</changefreq>';
            echo '<priority>0.6</priority>';
            echo '</url>';
        }
    }
} catch (Exception $e) { }

echo '</urlset>';
?>
