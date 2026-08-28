<?php
// Visitor Tracking API
// Records page visits with browser, device, IP, session data
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

include_once '../includes/config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input) || empty($input['session_id'])) {
        // Try form data
        $input = $_POST;
    }

    if (empty($input['session_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'session_id required']);
        exit;
    }

    $session_id = substr(trim($input['session_id']), 0, 64);
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
    $page_url = substr($input['page_url'] ?? $_SERVER['HTTP_REFERER'] ?? '/', 0, 500);
    $referrer = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);
    $screen_width = (int)($input['screen_width'] ?? 0);
    $screen_height = (int)($input['screen_height'] ?? 0);
    $language = substr($input['language'] ?? '', 0, 10);

    // Parse user agent for browser/OS/device
    $browser = parseBrowser($user_agent);
    $os = parseOS($user_agent);
    $device_type = parseDevice($user_agent, $screen_width);

    // Check if this is a unique visitor (same session within 30 minutes)
    $is_unique = 1;
    $stmt = $conn->prepare("SELECT id, updated_at, pages_viewed FROM visitor_tracking 
                            WHERE session_id = ? 
                            ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param('s', $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $last_time = strtotime($existing['updated_at']);
        $now = time();
        $diff = $now - $last_time;

        if ($diff < 1800) { // 30 minutes
            // Same session - update existing record
            $is_unique = 0;
            $new_pages = $existing['pages_viewed'] + 1;
            $duration = $diff;

            $update = $conn->prepare("UPDATE visitor_tracking SET 
                                        page_url = ?,
                                        visit_duration = ?,
                                        pages_viewed = ?,
                                        updated_at = NOW()
                                      WHERE id = ?");
            $update->bind_param('siii', $page_url, $duration, $new_pages, $existing['id']);
            $update->execute();
            $update->close();

            echo json_encode(['success' => true, 'action' => 'updated', 'is_unique' => false]);
            exit;
        }
    }

    // New visit - insert record
    $stmt = $conn->prepare("INSERT INTO visitor_tracking 
        (session_id, ip_address, user_agent, browser, os, device_type, 
         page_url, referrer, screen_width, screen_height, language, is_unique)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param('sssssssssiii',
        $session_id, $ip_address, $user_agent, $browser, $os, $device_type,
        $page_url, $referrer, $screen_width, $screen_height, $language, $is_unique
    );

    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'action' => 'recorded', 'is_unique' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

$conn->close();

// ---- Helper functions ----

function parseBrowser($ua) {
    if (strpos($ua, 'Firefox') !== false) return 'Firefox';
    if (strpos($ua, 'Edg') !== false) return 'Edge';
    if (strpos($ua, 'OPR') !== false || strpos($ua, 'Opera') !== false) return 'Opera';
    if (strpos($ua, 'Chrome') !== false && strpos($ua, 'Edg') === false) return 'Chrome';
    if (strpos($ua, 'Safari') !== false && strpos($ua, 'Chrome') === false) return 'Safari';
    if (strpos($ua, 'MSIE') !== false || strpos($ua, 'Trident') !== false) return 'IE';
    return 'Other';
}

function parseOS($ua) {
    if (strpos($ua, 'Windows NT 10') !== false) return 'Windows 10/11';
    if (strpos($ua, 'Windows NT 6.3') !== false) return 'Windows 8.1';
    if (strpos($ua, 'Windows NT 6.1') !== false) return 'Windows 7';
    if (strpos($ua, 'Windows') !== false) return 'Windows';
    if (strpos($ua, 'iPhone') !== false) return 'iOS';
    if (strpos($ua, 'iPad') !== false) return 'iPadOS';
    if (strpos($ua, 'Mac OS X') !== false) return 'macOS';
    if (strpos($ua, 'Android') !== false) return 'Android';
    if (strpos($ua, 'Linux') !== false) return 'Linux';
    return 'Other';
}

function parseDevice($ua, $width) {
    if (strpos($ua, 'iPad') !== false || strpos($ua, 'Tablet') !== false) return 'tablet';
    if (strpos($ua, 'Mobile') !== false || strpos($ua, 'Android') !== false || strpos($ua, 'iPhone') !== false) return 'mobile';
    if ($width > 0 && $width <= 768) return 'mobile';
    return 'desktop';
}
?>
