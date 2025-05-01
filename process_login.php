<?php
require "process_register.php";
$conn = new mysqli('localhost', 'root', '', 'armansalon');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$username = $_POST['username'];
$password = $_POST['psw'];

$sql = "SELECT * FROM admins WHERE username=?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  if (password_verify($password, $row['psw'])) {
    session_start();
    $_SESSION['admin_id'] = $row['id'];
    header("Location: dashboard.php");
  } else {
    $error = urlencode("Invalid password.");
    header("Location: admin_login.php?error=$error");
  }
} else {
  $error = urlencode("No user found.");
  header("Location: admin_login.php?error=$error");
}

$stmt->close();
$conn->close();
?>
