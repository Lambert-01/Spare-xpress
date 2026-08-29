<?php
/*
 * Central brand-logo resolver so the homepage (index.php) and any other view
 * show the same logos as pages/brands.php, including for brands whose
 * logo_image is NULL in the database (falling back to CDN/known assets).
 */

if (!function_exists('spx_brand_logo_url')) {
    function spx_brand_logo_url(?string $brandName, $dbLogoImage = null): ?string
    {
        $brandName = trim((string)$brandName);

        // 1) Use the DB logo_image if the local file actually exists.
        if (!empty($dbLogoImage)) {
            $safe = basename(str_replace(['../../', '../'], '', (string)$dbLogoImage));
            $local = 'uploads/brands/' . $safe;
            if (file_exists(dirname(__DIR__) . '/' . $local)) {
                return $local;
            }
        }

        // 2) Fall back to a known mapping (local files + CDN), same as brands.php.
        $known = [
            'audi' => 'https://cdn.simpleicons.org/audi/0066CC',
            'byd' => '/uploads/brands/byd-1.svg',
            'changan' => '/uploads/brands/changan-automobile-logo-1.svg',
            'chery' => '/uploads/brands/chery-3.svg',
            'chevrolet' => 'https://cdn.simpleicons.org/chevrolet/CC0000',
            'citroën' => '/uploads/brands/citroen-racing-2009-2016-logo.svg',
            'citroen' => '/uploads/brands/citroen-racing-2009-2016-logo.svg',
            'dongfeng' => '/uploads/brands/DONGFENG.png',
            'geely' => '/uploads/brands/geely-logo-2.svg',
            'great wall' => '/uploads/brands/great-wall-seeklogo.png',
            'isuzu' => 'https://cdn.simpleicons.org/isuzu/CC0000',
            'jac' => '/uploads/brands/jac-motors-seeklogo.png',
            'kia' => 'https://cdn.simpleicons.org/kia/05141F',
            'land rover' => 'https://cdn.simpleicons.org/landrover/005A2B',
            'lexus' => 'https://cdn.simpleicons.org/lexus/1A1A1A',
            'mazda' => 'https://cdn.simpleicons.org/mazda/101820',
            'mg' => 'https://cdn.simpleicons.org/mg/CC0000',
            'mitsubishi' => 'https://cdn.simpleicons.org/mitsubishi/E60012',
            'nissan' => 'https://cdn.simpleicons.org/nissan/C3002F',
            'peugeot' => 'https://cdn.simpleicons.org/peugeot/00267E',
            'skoda' => 'https://cdn.simpleicons.org/skoda/4BA82E',
            'subaru' => 'https://cdn.simpleicons.org/subaru/013C74',
            'suzuki' => 'https://cdn.simpleicons.org/suzuki/E30613',
            'volvo' => 'https://cdn.simpleicons.org/volvo/003057',
            'wuling' => 'https://cdn.simpleicons.org/wuling/FF6600',
        ];

        $key = strtolower($brandName);
        if (isset($known[$key])) {
            return $known[$key];
        }

        // 3) Common local assets keyed by slug/brand name (best-effort).
        $localAssets = [
            'toyota' => '/uploads/brands/toyota_logo.svg',
            'honda' => '/uploads/brands/honda_logo.svg',
            'bmw' => '/uploads/brands/bmw_logo.svg',
            'mercedes-benz' => '/uploads/brands/mercedes-benz-9.svg',
            'ford' => '/uploads/brands/ford_logo.svg',
            'volkswagen' => '/uploads/brands/volkswagen_logo.svg',
            'hyundai' => '/uploads/brands/hyundai_logo.svg',
            'renault' => '/uploads/brands/renault-2.svg',
            'baic' => '/uploads/brands/BAIC.png',
        ];
        if (isset($localAssets[$key])) {
            return $localAssets[$key];
        }

        return null;
    }
}
