<?php
require_once __DIR__ . '/env.php';

if (!function_exists('spx_is_remote_url')) {
    function spx_is_remote_url($value)
    {
        return is_string($value) && preg_match('/^https?:\/\//i', $value);
    }
}

if (!function_exists('spx_public_asset_url')) {
    function spx_public_asset_url($path, $prefix = '')
    {
        if (empty($path) || spx_is_remote_url($path)) {
            return $path;
        }

        return rtrim($prefix, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('spx_cloudinary_enabled')) {
    function spx_cloudinary_enabled()
    {
        return spx_env('CLOUDINARY_CLOUD_NAME') && spx_env('CLOUDINARY_API_KEY') && spx_env('CLOUDINARY_API_SECRET');
    }
}

if (!function_exists('spx_upload_image_to_cloudinary')) {
    function spx_upload_image_to_cloudinary($file, $folder, $publicIdBase = null)
    {
        if (!spx_cloudinary_enabled() || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        if (!function_exists('curl_init')) {
            error_log('Cloudinary upload skipped: PHP cURL extension is not enabled.');
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!empty($file['type']) && !in_array($file['type'], $allowedTypes, true)) {
            return false;
        }

        $cloudName = spx_env('CLOUDINARY_CLOUD_NAME');
        $apiKey = spx_env('CLOUDINARY_API_KEY');
        $apiSecret = spx_env('CLOUDINARY_API_SECRET');
        $timestamp = time();
        $folder = trim($folder, '/');
        $publicId = $publicIdBase ? preg_replace('/[^A-Za-z0-9_\-\/]/', '-', trim($publicIdBase, '/')) : null;

        $signatureParams = [
            'folder' => $folder,
            'timestamp' => $timestamp,
        ];

        if ($publicId) {
            $signatureParams['public_id'] = $publicId;
        }

        ksort($signatureParams);
        $signatureBase = [];
        foreach ($signatureParams as $key => $value) {
            $signatureBase[] = $key . '=' . $value;
        }
        $signature = sha1(implode('&', $signatureBase) . $apiSecret);

        $postFields = [
            'file' => new CURLFile($file['tmp_name'], $file['type'] ?: 'application/octet-stream', $file['name'] ?? 'upload'),
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'folder' => $folder,
            'signature' => $signature,
        ];

        if ($publicId) {
            $postFields['public_id'] = $publicId;
        }

        $ch = curl_init('https://api.cloudinary.com/v1_1/' . rawurlencode($cloudName) . '/image/upload');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            error_log('Cloudinary upload failed: ' . ($error ?: $response));
            return false;
        }

        $data = json_decode($response, true);
        return $data['secure_url'] ?? false;
    }
}
?>
