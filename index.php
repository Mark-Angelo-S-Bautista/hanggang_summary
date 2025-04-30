<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Arman Salon</title>
  <!-- Load classy cursive font -->
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/index_styles.css">
</head>
<body>
  <div class="container">
    <img src="img/logo.png" alt="Arman Salon Logo" class="logo">
    <h1>Arman Salon</h1>
    <p>Welcome! Please select your role to continue.</p>
    <form action="form.php" method="post">
      <button class="role-btn client" type="submit">I’m a Client</button>
    </form>
    <form action="admin_login.php" method="get">
      <button class="role-btn admin" type="submit">I’m an Admin</button>
    </form>
  </div>
</body>
</html>