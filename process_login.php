<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password_attempt = $_POST['psw'] ?? '';

    // Hardcoded admin credentials
    $valid_username = 'admin';
    $password_file = 'password.txt';
    $valid_password = file_exists($password_file) ? trim(file_get_contents($password_file)) : 'admin';

    if ($username === $valid_username && $password_attempt === $valid_password) {
        session_regenerate_id(true);
        $_SESSION['id'] = 1; // Fixed ID for the only admin
        $_SESSION['username'] = $valid_username;

        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: admin_login.php?error=" . urlencode("Invalid username or password."));
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>