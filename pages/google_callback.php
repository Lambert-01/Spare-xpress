<?php
require_once __DIR__ . '/../includes/google_auth.php';

function fail_google_login($message)
{
    $_SESSION['google_auth_error'] = $message;
    header('Location: login.php');
    exit;
}

if (!spx_google_enabled()) {
    fail_google_login('Google login is not configured.');
}

if (!empty($_GET['error'])) {
    fail_google_login('Google sign-in was cancelled or denied.');
}

$state = $_GET['state'] ?? '';
if ($state === '' || empty($_SESSION['google_oauth_state']) || !hash_equals($_SESSION['google_oauth_state'], $state)) {
    fail_google_login('Invalid Google sign-in session. Please try again.');
}

$code = $_GET['code'] ?? '';
if ($code === '') {
    fail_google_login('Missing Google authorization code.');
}

unset($_SESSION['google_oauth_state']);
$redirectAfterLogin = $_SESSION['google_oauth_redirect'] ?? '../index.php';
unset($_SESSION['google_oauth_mode'], $_SESSION['google_oauth_redirect']);

if (!function_exists('curl_init')) {
    fail_google_login('Google login requires the PHP cURL extension.');
}

$tokenPayload = [
    'code' => $code,
    'client_id' => GOOGLE_CLIENT_ID,
    'client_secret' => GOOGLE_CLIENT_SECRET,
    'redirect_uri' => spx_google_redirect_uri(),
    'grant_type' => 'authorization_code',
];

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($tokenPayload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT => 30,
]);
$tokenResponse = curl_exec($ch);
$tokenStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$tokenError = curl_error($ch);
curl_close($ch);

if ($tokenResponse === false || $tokenStatus < 200 || $tokenStatus >= 300) {
    error_log('Google token exchange failed: ' . ($tokenError ?: $tokenResponse));
    fail_google_login('Google sign-in failed. Please try again.');
}

$tokenData = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? '';
if ($accessToken === '') {
    fail_google_login('Google sign-in did not return an access token.');
}

$ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $accessToken],
    CURLOPT_TIMEOUT => 30,
]);
$userResponse = curl_exec($ch);
$userStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$userError = curl_error($ch);
curl_close($ch);

if ($userResponse === false || $userStatus < 200 || $userStatus >= 300) {
    error_log('Google userinfo failed: ' . ($userError ?: $userResponse));
    fail_google_login('Could not read your Google profile.');
}

$googleUser = json_decode($userResponse, true);
$email = trim($googleUser['email'] ?? '');
$emailVerified = !empty($googleUser['email_verified']);

if ($email === '' || !$emailVerified || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fail_google_login('Your Google account must have a verified email address.');
}

$stmt = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as full_name, email, phone, customer_status FROM customers_enhanced WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

if ($customer && $customer['customer_status'] !== 'active') {
    fail_google_login('This customer account is not active.');
}

if (!$customer) {
    $name = trim($googleUser['name'] ?? '');
    $firstName = trim($googleUser['given_name'] ?? '');
    $lastName = trim($googleUser['family_name'] ?? '');

    if ($firstName === '' && $name !== '') {
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
    }

    if ($firstName === '') {
        $firstName = 'Customer';
    }

    $customerNumber = 'CUST-' . date('Ymd') . '-' . str_pad((string)mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $password = password_hash(bin2hex(random_bytes(24)), PASSWORD_DEFAULT);
    $profileImage = $googleUser['picture'] ?? null;

    $insert = $conn->prepare("INSERT INTO customers_enhanced (customer_number, first_name, last_name, email, phone, password, profile_image, customer_status, email_verified) VALUES (?, ?, ?, ?, NULL, ?, ?, 'active', 1)");
    $insert->bind_param("ssssss", $customerNumber, $firstName, $lastName, $email, $password, $profileImage);

    if (!$insert->execute()) {
        error_log('Google customer create failed: ' . $insert->error);
        $insert->close();
        fail_google_login('Could not create your customer account.');
    }

    $customerId = $conn->insert_id;
    $insert->close();

    $customer = [
        'id' => $customerId,
        'full_name' => trim($firstName . ' ' . $lastName),
        'email' => $email,
        'phone' => null,
    ];
}

session_regenerate_id(true);

$_SESSION['customer_id'] = $customer['id'];
$_SESSION['customer_name'] = trim($customer['full_name']) ?: $email;
$_SESSION['customer_email'] = $customer['email'];
$_SESSION['customer_phone'] = $customer['phone'] ?? '';
$_SESSION['login_time'] = time();
$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';

$update = $conn->prepare("UPDATE customers_enhanced SET last_login = NOW(), email_verified = 1 WHERE id = ?");
$update->bind_param("i", $customer['id']);
$update->execute();
$update->close();

header('Location: ' . $redirectAfterLogin);
exit;
?>
