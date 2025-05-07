<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'armansalon');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$today = date('Y-m-d');

// Total Appointments Today
$apptResult = $conn->query("SELECT COUNT(*) AS total FROM form_info WHERE DATE(selected_date) = '$today'");
$totalAppointments = $apptResult->fetch_assoc()['total'];

// Total Customers Today
$customerResult = $conn->query("SELECT COUNT(DISTINCT id) AS total FROM form_info WHERE DATE(selected_date) = '$today'");
$totalCustomers = $customerResult->fetch_assoc()['total'];

// Appointments Today Only
$upcomingResult = $conn->query("
  SELECT username, selected_date, selected_time 
  FROM form_info 
  WHERE selected_date = '$today' 
  ORDER BY selected_time
");
?>
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
    margin-bottom: 45px;
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

    /*binura ko yung sa logout ha*/ 

    .main-content {
      flex: 1;
      padding: 30px;
      overflow-y: auto;
    }

    h1 {
      text-align: center;
      font-family: 'Great Vibes', cursive;
      font-size: 42px;
      color: #FF5B5B;
      margin-bottom: 30px;
    }

    .stats {
      display: flex;
      gap: 20px;
      justify-content: center;
      margin-bottom: 30px;
    }

    .card {
      background: #fff5f5;
      border-left: 5px solid #FF5B5B;
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      flex: 1;
      max-width: 250px;
    }

    .card h2 {
      margin: 10px 0;
      font-size: 32px;
      color: #CC3A3A;
    }

    .card p {
      font-weight: bold;
      color: #333;
    }

    .appointments {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 0 25px rgba(0,0,0,0.1);
      max-height: 50vh;
      overflow-y: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #FF5B5B;
      color: white;
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

    <div class="main-content">
      <h1>Welcome Admin!</h1>

      <div class="stats">
        <div class="card">
          <p>Total Appointments Today</p>
          <h2><?php echo $totalAppointments; ?></h2>
        </div>
        <div class="card">
          <p>Customers Today</p>
          <h2><?php echo $totalCustomers; ?></h2>
        </div>
      </div>

      <div class="appointments">
        <h2>Appointments Scheduled for Today</h2>
        <table>
          <tr>
            <th>Customer Name</th>
            <th>Date</th>
            <th>Time</th>
          </tr>
          <?php while($row = $upcomingResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['username']); ?></td>
              <td><?php echo htmlspecialchars($row['selected_date']); ?></td>
              <td><?php echo htmlspecialchars($row['selected_time']); ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
<?php $conn->close(); ?>
