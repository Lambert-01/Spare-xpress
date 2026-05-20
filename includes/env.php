<?php
if (!function_exists('spx_load_env')) {
    function spx_load_env($path)
    {
        if (!is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            if (getenv($key) === false) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

if (!function_exists('spx_env')) {
    function spx_env($key, $default = null)
    {
        $value = getenv($key);
        return $value === false ? $default : $value;
    }
}

if (!function_exists('spx_database_config')) {
    function spx_database_config()
    {
        $databaseUrl = spx_env('DATABASE_URL');
        if ($databaseUrl) {
            $parts = parse_url($databaseUrl);
            $scheme = $parts['scheme'] ?? '';

            if (!in_array($scheme, ['mysql', 'mysqli'], true)) {
                die('Unsupported DATABASE_URL scheme "' . htmlspecialchars($scheme) . '". This project currently uses MySQL/mysqli. Neon PostgreSQL URLs require a PostgreSQL migration first.');
            }

            return [
                'host' => $parts['host'] ?? 'localhost',
                'port' => $parts['port'] ?? null,
                'name' => isset($parts['path']) ? ltrim($parts['path'], '/') : '',
                'user' => isset($parts['user']) ? urldecode($parts['user']) : '',
                'pass' => isset($parts['pass']) ? urldecode($parts['pass']) : '',
            ];
        }

        return [
            'host' => spx_env('DB_HOST', 'localhost'),
            'port' => spx_env('DB_PORT'),
            'name' => spx_env('DB_NAME', 'sparedb'),
            'user' => spx_env('DB_USER', 'root'),
            'pass' => spx_env('DB_PASS', ''),
        ];
    }
}

spx_load_env(dirname(__DIR__) . '/.env');
?>
