<?php
session_start();
$accountCreated = false;

if (isset($_SESSION['account_created'])) {
    $accountCreated = true;
    unset($_SESSION['account_created']);
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
      background-image: url('img/bg.jpg');
      background-size: cover;
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
    }

    button:hover {
      background-color: #CC3A3A;
    }

    .success-msg {
      color: green;
      font-weight: bold;
      margin-top: 10px;
    }

    .error-msg {
      color: red;
      font-weight: bold;
      margin-top: 10px;
    }

    p {
      margin-top: 20px;
    }

    a {
      color: #FF5B5B;
      text-decoration: none;
    }
  </style>
</head>
<body>

<form class="form-container" action="process_login.php" method="POST">
  <h1>Arman Salon</h1>
  <p>Admin Login</p>

  <!-- Success message (only after registration) -->
  <?php if ($accountCreated): ?>
    <p class="success-msg">Account successfully created!</p>
  <?php endif; ?>

  <!-- Error message (optional) -->
  <?php if (isset($_GET['error'])): ?>
    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="psw" placeholder="Password" required>
  <button type="submit">Login</button>
  <p>No account yet? <a href="admin_register.php">Create one</a></p>
</form>

<!-- Optional: Auto-hide success message -->
<script>
  setTimeout(() => {
    const msg = document.querySelector('.success-msg');
    if (msg) msg.style.display = 'none';
  }, 3000);
</script>

</body>
</html>
