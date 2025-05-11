<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - Arman Salon</title>
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
    .form-container p.subtitle {
      margin-top: -10px;
      margin-bottom: 20px;
      font-size: 18px;
      color: #333;
    }
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
    .success-msg {
      color: green;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      color: #FF5B5B;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<form class="form-container" action="process_change_password.php" method="POST">
  <h1>Arman Salon</h1>
  <p class="subtitle">Change Admin Password</p>

  <?php if (isset($_GET['msg'])): ?>
    <p class="success-msg"><?php echo htmlspecialchars($_GET['msg']); ?></p>
  <?php endif; ?>
  <?php if (isset($_GET['error'])): ?>
    <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
  <?php endif; ?>

  <input type="password" name="current_password" placeholder="Current Password" required>
  <input type="password" name="new_password" placeholder="New Password" required>
  <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
  <button type="submit">Change Password</button>

  <a href="admin_login.php">Back to Login</a>
</form>

<script>
  setTimeout(() => {
    const errorMsg = document.querySelector('.error-msg');
    const successMsg = document.querySelector('.success-msg');
    if (errorMsg) {
      errorMsg.style.transition = 'opacity 1s ease-out';
      errorMsg.style.opacity = '0';
      setTimeout(() => { errorMsg.style.display = 'none'; }, 1000);
    }
    if (successMsg) {
      successMsg.style.transition = 'opacity 1s ease-out';
      successMsg.style.opacity = '0';
      setTimeout(() => { successMsg.style.display = 'none'; }, 1000);
    }
  }, 3000);
</script>

</body>
</html>