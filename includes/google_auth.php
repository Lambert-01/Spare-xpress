<?php
require_once __DIR__ . '/config.php';

if (!function_exists('spx_google_enabled')) {
    function spx_google_enabled()
    {
        return GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '';
    }
}

if (!function_exists('spx_current_base_url')) {
    function spx_current_base_url()
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $scheme = $https ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? parse_url(SITE_URL, PHP_URL_HOST);

        return $scheme . '://' . $host;
    }
}

if (!function_exists('spx_google_redirect_uri')) {
    function spx_google_redirect_uri()
    {
        if (GOOGLE_REDIRECT_URI !== '') {
            return GOOGLE_REDIRECT_URI;
        }

        return rtrim(spx_current_base_url(), '/') . '/pages/google_callback.php';
    }
}

if (!function_exists('spx_google_auth_url')) {
    function spx_google_auth_url($mode = 'login', $redirect = '../index.php')
    {
        if (!spx_google_enabled()) {
            return '#';
        }

        $state = bin2hex(random_bytes(16));
        $_SESSION['google_oauth_state'] = $state;
        $_SESSION['google_oauth_mode'] = $mode;
        $_SESSION['google_oauth_redirect'] = $redirect ?: '../index.php';

        $params = [
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => spx_google_redirect_uri(),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }
}
?>
