<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $file = 'password.txt';

    if (!file_exists($file)) {
        file_put_contents($file, 'admin'); // Default password
    }

    $stored = trim(file_get_contents($file));

    if ($current !== $stored) {
        header("Location: change_password.php?error=" . urlencode("Current password is incorrect!"));
        exit();
    }

    if ($new !== $confirm) {
        header("Location: change_password.php?error=" . urlencode("New passwords do not match!"));
        exit();
    }

    file_put_contents($file, $new);
    header("Location: change_password.php?msg=" . urlencode("Password successfully changed!"));
    exit();
}
?>