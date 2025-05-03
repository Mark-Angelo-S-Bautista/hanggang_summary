<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'armansalon');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['psw']);

    if (empty($username) || empty($email) || empty($password)) {
        header("Location: admin_register.php?error=" . urlencode("Lahat ng fields ay kailangan."));
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, email, psw) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        header("Location: admin_login.php?success=1");
    } else {
        header("Location: admin_register.php?error=" . urlencode("Username o Email ay ginagamit na."));
    }

    $stmt->close();
    $conn->close();
}
?>
