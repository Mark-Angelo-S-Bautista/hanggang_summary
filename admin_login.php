<?php
session_start(); // Start session at the very beginning

// If user is already logged in, redirect them to dashboard
if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Arman Salon</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-image: url('img/bg.jpg'); /* Make sure the image path is correct */
      background-size: cover;
      background-position: center;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .form-container {
      background: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      text-align: center;
    }
    .form-container h1 {
      font-family: 'Great Vibes', cursive;
      color: #FF5B5B;
      margin-bottom: 10px;
      font-size: 42px;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 10px;
      border: 1px solid #ccc;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 15px;
      background-color: #FF5B5B;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 10px;
    }
    button:hover {
      background-color: #CC3A3A;
    }
    .error-msg {
      color: red;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
    }
    p {
      margin-top: 20px;
    }
    a {
      color: #FF5B5B;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<form class="form-container" action="process_login.php" method="POST">
  <h1>Arman Salon</h1>
  <p>Admin Login</p>

  <?php if (isset($_GET['error'])): ?>
    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="psw" placeholder="Password" required>
  <button type="submit">Login</button>

  <!-- New Change Password button -->
  <button type="button" onclick="window.location.href='change_password.php'">Change Password</button>

  <!-- Registration link removed since only 1 admin is allowed -->
</form>

<script>
  // Optional: auto-hide error message after 3 seconds
  setTimeout(() => {
    const errorMsg = document.querySelector('.error-msg');
    if (errorMsg) {
      errorMsg.style.transition = 'opacity 1s ease-out';
      errorMsg.style.opacity = '0';
      setTimeout(() => { errorMsg.style.display = 'none'; }, 1000);
    }
  }, 3000);
</script>

</body>
</html>