<?php
session_start();
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $q = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active'");
    $q->bind_param("s", $username);
    $q->execute();
    $result = $q->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['admin'] = $username;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_name'] = $user['full_name'];

            // Log login activity
            $log_stmt = $conn->prepare("INSERT INTO admin_activity_logs (admin_id, action, description, ip_address) VALUES (?, 'login', 'Admin login successful', ?)");
            $log_stmt->bind_param("is", $user['id'], $_SERVER['REMOTE_ADDR']);
            $log_stmt->execute();

            header("Location: enhanced_dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Invalid credentials or account inactive!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login - SPARE XPRESS LTD</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container bg-white">
            <h2 class="text-center mb-4">Admin Login</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>