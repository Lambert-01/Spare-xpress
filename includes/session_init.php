<?php
if (!function_exists('spx_session_start')) {
    function spx_session_start(array $cookieParams = []): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $defaults = [
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
            'httponly' => true,
            'samesite' => 'Lax',
        ];
        $cookieParams = array_merge($defaults, $cookieParams);

        $rawSavePath = (string)ini_get('session.save_path');
        $savePath = $rawSavePath;
        if (str_contains($rawSavePath, ';')) {
            $parts = explode(';', $rawSavePath);
            $savePath = (string)end($parts);
        }
        $savePath = trim($savePath);

        $needsFallback = ($savePath === '' || !is_dir($savePath) || !is_writable($savePath));
        if ($needsFallback) {
            $candidates = [];

            $tmp = (string)sys_get_temp_dir();
            if ($tmp !== '') {
                $candidates[] = rtrim($tmp, "\\/") . DIRECTORY_SEPARATOR . 'sparexpress_sessions';
            }

            $candidates[] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'sessions';

            foreach ($candidates as $candidate) {
                if (!is_dir($candidate)) {
                    @mkdir($candidate, 0775, true);
                }
                if (is_dir($candidate) && is_writable($candidate)) {
                    session_save_path($candidate);
                    break;
                }
            }
        }

        session_set_cookie_params($cookieParams);

        // Suppress warnings (e.g., unwritable session.save_path) so JSON endpoints don't emit HTML.
        @session_start();
    }
}
