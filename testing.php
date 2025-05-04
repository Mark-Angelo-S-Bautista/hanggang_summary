<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Arman Salon</title>
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('img/bg.jpg');
      background-size: cover;
      background-position: center;
      height: 100vh;
      overflow: hidden;
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: #FF5B5B; /* Red color */
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 0;
      height: 100vh; /* Full height */
      flex-shrink: 0; /* Prevent shrinking */
    }

    .sidebar h2 {
    text-align: center;
    font-family: 'Great Vibes', cursive;
    font-size: 38px; /* Increased font size */
    font-weight: bold;
    color: white; /* White color for "Arman Salon" */
    margin-bottom: 30px;
    text-transform: uppercase;
    }

    .sidebar div {
      flex-grow: 1; /* Allow this section to grow */
      display: flex;
      flex-direction: column;
    }

    .sidebar a {
    display: flex;
    justify-content: center; /* Center text horizontally */
    align-items: center; /* Center text vertically */
    margin: 15px 20px; /* Increased margin to add more spacing between links */
    padding: 18px; /* Increased padding for better space */
    background-color: #fff5f5;
    color: #CC3A3A;
    text-decoration: none;
    font-weight: bold;
    border-left: 5px solid #FF5B5B;
    border-radius: 10px;
    text-align: center; /* Ensures the text is centered */
    font-size: 18px; /* Button text size */
    height: 60px; /* Fixed height for consistency */
    }

    .sidebar a:hover {
      background-color: #f1c0c0;
    }
</style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div>
        <h2>Arman Salon</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="info_man.php">Information Management</a>
        <a href="reports.php">Reports</a>
        <a href="settings.php">Settings</a>
      </div>
      <div class="logout-link">
        <a href="logout.php">Logout</a>
      </div>
    </div>