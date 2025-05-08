<?php
session_start(); // Must be called to access session data

// No need for Cache-Control headers here primarily, but they don't harm.
// Their main role is on pages you want to prevent caching of *content*.

// Check if the logout was confirmed via POST (from your modal)
// If you want to allow GET logout as well, you might adjust this or remove the condition.
if (isset($_POST['confirm_logout'])) {
    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session data on the server.
    session_destroy();
} else {
    // If accessed without confirm_logout (e.g., direct GET),
    // you might still want to log out or redirect.
    // For now, this means only POST from modal logs out.
    // If this is not the desired behavior, you can remove the if-condition
    // or add `|| $_SERVER['REQUEST_METHOD'] === 'GET'` (with caution).
    // However, sticking to POST for state-changing actions like logout is good practice.
}

// Redirect to the login page after logout or if the script is accessed.
header("Location: admin_login.php"); // Ensure this is your correct login page
exit();
?>
