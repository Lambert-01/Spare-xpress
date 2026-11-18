<?php
/**
 * SPARE XPRESS LTD - CSRF protection helpers.
 *
 * Two layers of protection are provided:
 *   1. Token-based (for classic HTML <form method="POST"> submissions):
 *        - spx_csrf_field()        prints a hidden token input inside the form
 *        - spx_csrf_require_token() aborts the request if the posted token is missing/wrong
 *   2. Same-origin check (for JSON/fetch endpoints that post no form field):
 *        - spx_csrf_require_origin() aborts if the request did not originate from this site
 *
 * Both rely on an active session (spx_session_start() must already have run).
 */

require_once __DIR__ . '/session_init.php';

if (!function_exists('spx_csrf_token')) {
    /** Return the current session CSRF token, creating one if needed. */
    function spx_csrf_token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            spx_session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('spx_csrf_field')) {
    /** Print a hidden input carrying the CSRF token. Call inside each POST form. */
    function spx_csrf_field(): void
    {
        $token = htmlspecialchars(spx_csrf_token(), ENT_QUOTES, 'UTF-8');
        echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}

if (!function_exists('spx_csrf_meta')) {
    /** Print a <meta> tag so JavaScript can read the token if it wants to send it. */
    function spx_csrf_meta(): void
    {
        $token = htmlspecialchars(spx_csrf_token(), ENT_QUOTES, 'UTF-8');
        echo '<meta name="csrf-token" content="' . $token . '">';
    }
}

if (!function_exists('spx_csrf_token_valid')) {
    /** True if the request carries a valid token (form field or X-CSRF-Token header). */
    function spx_csrf_token_valid(): bool
    {
        $expected = $_SESSION['csrf_token'] ?? '';
        if ($expected === '') {
            return false;
        }
        $given = $_POST['csrf_token']
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return is_string($given) && hash_equals($expected, $given);
    }
}

if (!function_exists('spx_request_is_same_origin')) {
    /**
     * True if the request's Origin/Referer host matches the host being served.
     * Blocks cross-site form posts and fetches without needing a token in every
     * JS call. A state-changing POST with neither header is treated as unsafe.
     */
    function spx_request_is_same_origin(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host === '') {
            return false;
        }

        $check = static function (?string $url) use ($host): ?bool {
            if (!$url) {
                return null; // header absent -> no opinion
            }
            $urlHost = parse_url($url, PHP_URL_HOST);
            if ($urlHost === null || $urlHost === false) {
                return false;
            }
            $urlPort = parse_url($url, PHP_URL_PORT);
            if ($urlPort) {
                $urlHost .= ':' . $urlPort;
            }
            return strcasecmp($urlHost, $host) === 0;
        };

        $origin = $check($_SERVER['HTTP_ORIGIN'] ?? null);
        if ($origin !== null) {
            return $origin;
        }
        $referer = $check($_SERVER['HTTP_REFERER'] ?? null);
        if ($referer !== null) {
            return $referer;
        }
        return false; // no Origin and no Referer on a state-changing request
    }
}

if (!function_exists('spx_csrf_require_token')) {
    /** For HTML form POST handlers: stop with 403 if the token is invalid. */
    function spx_csrf_require_token(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        if (!spx_csrf_token_valid()) {
            http_response_code(403);
            die('Invalid or missing security token. Please reload the page and try again.');
        }
    }
}

if (!function_exists('spx_csrf_require_origin')) {
    /** For JSON/fetch POST endpoints: stop with a 403 JSON error if cross-origin. */
    function spx_csrf_require_origin(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }
        if (!spx_request_is_same_origin()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Cross-origin request blocked.']);
            exit;
        }
    }
}
