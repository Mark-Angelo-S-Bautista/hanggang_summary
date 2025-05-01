<!-- admin_register.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Register - Arman Salon</title>
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
    input[type="email"],
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
<form class="form-container" action="process_register.php" method="POST">
  <h1>Arman Salon</h1>
  <p>Create your admin account</p>

  <!-- Add success or error message here -->
  <?php if (isset($_GET['success'])): ?>
    <p style="color: green;">Account created successfully! You can now login.</p>
  <?php elseif (isset($_GET['error'])): ?>
    <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <input type="text" name="username" placeholder="Username" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="psw" placeholder="Password" required>
  <button type="submit">Create Account</button>
  <p>Already have an account? <a href="admin_login.php">Login here</a></p>
</form>

</body>
</html>
