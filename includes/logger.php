<?php
if (!function_exists('spx_log')) {
    function spx_log(string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $payload = $context ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        error_log("[SPX][$timestamp] $message$payload");
    }
}

