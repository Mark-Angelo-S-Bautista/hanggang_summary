<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
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

        .success-msg {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .error-msg {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        a {
            color: #FF5B5B;
            text-decoration: none;
        }
    </style>
</head>
<body>

<form class="form-container" action="process_register.php" method="post">
    <h1>Arman Salon</h1>
    <p>Admin Registration</p>

    <?php if (isset($_GET['error'])): ?>
        <p class="error-msg"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['registered']) && $_GET['registered'] === "true"): ?>
        <p class="success-msg">Account successfully created!</p>
    <?php endif; ?>

    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="psw" placeholder="Password" required>
    <button type="submit" name="submit">Register</button>
    <p>Already have an account? <a href="admin_login.php">Login here</a></p>
</form>

</body>
</html>
