<?php
session_start();
require "database.php"; // Your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password_attempt = $_POST['psw'] ?? ''; // Matches name="psw" in admin_login.php

    if (empty($username) || empty($password_attempt)) {
        header("Location: admin_login.php?error=" . urlencode("Username and password are required."));
        exit();
    }

    try {
        // Fetch user from the database based on username
        // ENSURE your table is named correctly (e.g., 'admins' or 'users')
        // and columns are 'id', 'username', 'password_hash'
        $stmt = $pdo->prepare("SELECT id, username, psw FROM admins WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password_attempt, $user['psw'])) {
            // Password is correct, regenerate session ID for security
            session_regenerate_id(true);

            // Store user information in session
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            // Add any other user details you need in the session

            header("Location: dashboard.php"); // Redirect to the reports page
            exit();
        } else {
            // Invalid username or password
            header("Location: admin_login.php?error=" . urlencode("Invalid username or password."));
            exit();
        }
    } catch (PDOException $e) {
        // Log the database error, don't show specifics to user
        error_log("Login PDOException: " . $e->getMessage());
        header("Location: admin_login.php?error=" . urlencode("A system error occurred. Please try again later."));
        exit();
    }
} else {
    // If not a POST request, redirect to login page
    header("Location: admin_login.php");
    exit();
}
?>