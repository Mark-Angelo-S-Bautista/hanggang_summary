<?php
// Database connection
$servername = "localhost";
$username_db = "root"; // palitan ayon sa database credentials mo
$password_db = "";     // palitan kung may password ka
$database = "armansalon"; // palitan ng pangalan ng database mo

$conn = new mysqli($servername, $username_db, $password_db, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " (" . $conn->connect_errno . ")");
} else {
    echo "Connected to the database successfully!<br>";
}

// Get values from POST
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['psw']) ?? '';

// Input validation
$errors = [];

if (empty($username)) {
    $errors[] = "Username is required.";
}
if (empty($email)) {
    $errors[] = "Email is required.";
}
if (empty($password)) {
    $errors[] = "Password is required.";
}

// If may errors, ipakita muna at huwag mag-insert
if (!empty($errors)) {
    echo "<ul style='color:red;'>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
} else {
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute SQL statement
    $sql = "INSERT INTO admins (username, email, psw) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>Account successfully created!</p>";
        // header("Location: admin_login.php?success=1");
    } else {
        echo "<p style='color:red;'>Error inserting data: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

$conn->close();
?>
