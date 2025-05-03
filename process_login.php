<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'armansalon');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = trim($_POST['username']);
$password = trim($_POST['psw']);

$sql = "SELECT * FROM admins WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['psw'])) {
        $_SESSION['admin_id'] = $row['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: admin_login.php?error=" . urlencode("Maling password."));
        exit();
    }
} else {
    header("Location: admin_login.php?error=" . urlencode("Hindi nahanap ang account."));
    exit();
}

$stmt->close();
$conn->close();
?>
