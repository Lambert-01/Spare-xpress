<?php
// Error Logging System for SPARE XPRESS LTD
// Created: 2025-12-30

class ErrorLogger {
    private static $log_file = __DIR__ . '/error_log.txt';
    private static $error_log_file = __DIR__ . '/product_errors.txt';
    
    /**
     * Log error with timestamp and context
     */
    public static function logError($message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $context_str = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $log_entry = "[$timestamp] ERROR: $message$context_str" . PHP_EOL;
        
        // Write to main error log
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log SQL query and bind parameters for debugging
     */
    public static function logQuery($query, $types, $params) {
        $timestamp = date('Y-m-d H:i:s');
        
        // Count parameters
        $type_count = strlen($types);
        $param_count = count($params);
        
        $log_entry = "[$timestamp] QUERY DEBUG:" . PHP_EOL;
        $log_entry .= "  Query: $query" . PHP_EOL;
        $log_entry .= "  Type string: '$types' (length: $type_count)" . PHP_EOL;
        $log_entry .= "  Parameters: " . count($params) . PHP_EOL;
        $log_entry .= "  Parameter details:" . PHP_EOL;
        
        foreach ($params as $i => $param) {
            $type = $types[$i] ?? '?';
            $value = is_null($param) ? 'NULL' : (is_string($param) ? "'$param'" : $param);
            $log_entry .= "    [$i] Type: $type, Value: $value" . PHP_EOL;
        }
        
        $log_entry .= "  Match: " . ($type_count === $param_count ? "YES" : "NO - MISMATCH!") . PHP_EOL;
        $log_entry .= PHP_EOL;
        
        file_put_contents(self::$error_log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log successful operations
     */
    public static function logSuccess($message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $context_str = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $log_entry = "[$timestamp] SUCCESS: $message$context_str" . PHP_EOL;
        
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log form submission data
     */
    public static function logFormData($data) {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] FORM DATA:" . PHP_EOL;
        $log_entry .= json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
        
        file_put_contents(self::$error_log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Clear old logs (keep last 1000 entries)
     */
    public static function cleanupLogs() {
        $files = [self::$log_file, self::$error_log_file];
        
        foreach ($files as $file) {
            if (file_exists($file)) {
                $lines = file($file);
                if (count($lines) > 1000) {
                    $lines = array_slice($lines, -1000);
                    file_put_contents($file, implode('', $lines));
                }
            }
        }
    }
}

// Auto-cleanup on each request
ErrorLogger::cleanupLogs();
?>