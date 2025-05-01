<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
</head>
<body>
  <h1>Welcome to the Admin Dashboard!</h1>
  <p><a href="logout.php">Logout</a></p>
</body>
</html>

