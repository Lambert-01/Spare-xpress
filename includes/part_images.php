<?php
/**
 * SPARE XPRESS LTD - Demo part imagery.
 *
 * Returns a product image URL. If the product has a real uploaded image we use
 * it; otherwise we pick a branded demo illustration based on the part's
 * category and name keywords. Replace demo images later by uploading a real
 * main_image for the product — no code change needed.
 */

if (!function_exists('spx_part_image')) {

    function spx_part_image(?string $existing_image, ?string $product_name = '', ?string $category_name = ''): string
    {
        // 1. A real uploaded image always wins.
        $img = trim((string) $existing_image);
        if ($img !== '' && stripos($img, 'no-image') === false) {
            return $img;
        }

        $base = '/img/parts/';
        $name = strtolower((string) $product_name);

        // 2. Strong keyword matches from the product name.
        $keywords = [
            'mirror'  => 'mirror',
            'lamp'    => 'lamp',
            'light'   => 'lamp',
            'headlamp'=> 'lamp',
            'bulb'    => 'lamp',
            'brake'   => 'brake-system',
            'piston'  => 'pistons-rings',
            'belt'    => 'belts-hoses',
            'hose'    => 'belts-hoses',
            'filter'  => 'oil-system',
            'oil'     => 'oil-system',
            'radiator'=> 'cooling-system',
            'coolant' => 'cooling-system',
            'exhaust' => 'exhaust-system',
            'muffler' => 'exhaust-system',
            'suspension' => 'suspension-steering',
            'shock'   => 'suspension-steering',
            'spring'  => 'suspension-steering',
            'spark'   => 'electrical-system',
            'battery' => 'electrical-system',
            'valve'   => 'valves-timing',
            'timing'  => 'valves-timing',
            'fuel'    => 'fuel-system',
            'pump'    => 'fuel-system',
        ];
        foreach ($keywords as $kw => $slug) {
            if (strpos($name, $kw) !== false) {
                return $base . $slug . '.svg';
            }
        }

        // 3. Fall back to the category slug.
        $slug = strtolower((string) $category_name);
        $slug = str_replace('&', '', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        $known = [
            'brake-system','electrical-system','engine-parts','cooling-system',
            'oil-system','fuel-system','suspension-steering','body-parts',
            'exhaust-system','belts-hoses','pistons-rings','transmission',
            'valves-timing','air-conditioning',
        ];
        if (in_array($slug, $known, true)) {
            return $base . $slug . '.svg';
        }

        // 4. Generic spare-part illustration.
        return $base . 'default.svg';
    }
}
