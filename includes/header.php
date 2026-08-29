<?php include_once 'config.php'; ?>
<?php include_once __DIR__ . '/seo.php'; ?>

<?php
// Get SEO data for current page
$seo_page = spx_get_seo_page_key();
$seo = spx_get_seo_data($seo_page);
// Allow page-specific overrides via $page_title, $page_description, etc.
if (isset($page_title) && $page_title !== 'Home') {
    $seo['title'] = $page_title . ' - SPARE XPRESS LTD';
}
if (isset($page_description)) {
    $seo['description'] = $page_description;
}
if (isset($page_keywords)) {
    $seo['keywords'] = $page_keywords;
}
if (isset($page_image)) {
    $seo['image'] = $page_image;
}
if (isset($page_canonical)) {
    $seo['canonical'] = $page_canonical;
}
?>

<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">

<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($seo['title'], ENT_QUOTES, 'UTF-8'); ?></title>

    <?php spx_render_meta_tags($seo); ?>
    <?php spx_preload_hints(); ?>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/img/logo/icon.jpg">
    <link rel="apple-touch-icon" href="/img/logo/icon.jpg">
    <link rel="shortcut icon" href="/img/logo/icon.jpg">

    <!-- Google Web Fonts - Premium Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet (CDN) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Premium Design System -->
    <link href="/css/premium-design.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="/css/style.css" rel="stylesheet">

    <!-- Unified Client Portal Design -->
    <link href="/css/client-portal.css" rel="stylesheet">

    <!-- Mobile Responsive -->
    <link href="/css/mobile.css" rel="stylesheet">

    <?php
    // Structured Data - Website SearchAction (on all pages)
    spx_jsonld_website();
    // Structured Data - LocalBusiness (on homepage only)
    if ($seo_page === 'home') {
        spx_jsonld_local_business();
    }
    ?>
</head>

<body>
