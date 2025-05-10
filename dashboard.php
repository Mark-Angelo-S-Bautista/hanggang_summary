<?php
session_start();
// CRITICAL: Check if user is logged in (e.g., by checking for 'id' in session)
if (!isset($_SESSION['id'])) {
    header("Location: admin_login.php"); // Redirect to login page if not logged in
    exit(); // Stop further script execution
}

// Send no-cache headers to prevent browser caching of this protected page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0"); // Proxies

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
  <script>
document.addEventListener("DOMContentLoaded", function() {
    var logoutBtn = document.getElementById('logoutBtn');
    var logoutModal = document.getElementById('logoutModal');
    var container = document.querySelector('.container');

    console.log("logoutBtn:", logoutBtn);
    console.log("logoutModal:", logoutModal);
    console.log("container:", container);

    if (logoutBtn && logoutModal) {
        logoutBtn.addEventListener('click', function(event) {
            event.preventDefault(); // prevent default link behavior
            console.log("Logout button clicked");
            logoutModal.style.display = 'block';
            if (container) {
                container.classList.add('blur');
            }
        });
    } else {
        console.error("Logout button or modal not found.");
    }

    function closeModal() {
        logoutModal.style.display = 'none';
        if (container) {
            container.classList.remove('blur');
        }
        console.log("Modal closed");
    }

    var closeIcon = document.querySelector('#logoutModal .modal-content .close');
    if (closeIcon) {
        closeIcon.addEventListener('click', closeModal);
    }

    var cancelBtn = document.querySelector('#logoutModal .modal-content .cancel-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // Close modal if user clicks outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target === logoutModal) {
            closeModal();
        }
    });
});
</script>
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
    /* Modal overlay */
.modal {
  display: none; /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
  z-index: 1000;
  overflow: auto;
}

/* Modal content container */
.modal-content {
  position: relative;
  width: 90%;
  max-width: 400px;
  margin: 15% auto;
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  text-align: center;
}

/* Close button styling */
.modal-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  font-weight: bold;
  color: #333;
  cursor: pointer;
}

/* Heading style */
.modal-content h3 {
  margin-top: 0;
  color: #f35b53;
}

/* Paragraph style */
.modal-content p {
  font-size: 16px;
  margin: 15px 0;
}

/* Form button styling */
.modal-content button {
  padding: 10px 20px;
  margin-top: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

/* Logout (confirm) button */
.modal-content button[type="submit"] {
  background-color: #f35b53;
  color: #fff;
}

/* Cancel button */
.modal-content button.cancel-btn {
  background-color: #ccc;
  color: #333;
}

/* Blur effect for background container when modal is active */
.blur {
  filter: blur(4px);
}
.modal {
  display: none; /* Hidden by default */
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6); /* Semi-transparent background */
  z-index: 1000;
  overflow: auto;
}

/* Modal content container */
.modal-content {
  position: relative;
  width: 90%;
  max-width: 400px;
  margin: 15% auto;
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  text-align: center;
}

/* Close button styling */
.modal-content .close {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 24px;
  font-weight: bold;
  color: #333;
  cursor: pointer;
}

/* Heading style */
.modal-content h3 {
  margin-top: 0;
  color: #f35b53;
}

/* Paragraph style */
.modal-content p {
  font-size: 16px;
  margin: 15px 0;
}

/* Form button styling */
.modal-content button {
  padding: 10px 20px;
  margin-top: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

/* Logout (confirm) button */
.modal-content button[type="submit"] {
  background-color: #f35b53;
  color: #fff;
}

/* Cancel button */
.modal-content button.cancel-btn {
  background-color: #ccc;
  color: #333;
}

/* Blur effect for background container when modal is active */
.blur {
  filter: blur(4px);
}

.sidebar a.active {
  background-color: #e04848; /* Darker red for active */
  color: #fff;
  border-left: 5px solid #fff;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(255,91,91,0.15); /* Optional: subtle shadow */
  transition: background 0.2s;
}
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div>
        <h2>Arman Salon</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="info_man.php">Transactions</a>
        <a href="reports.php">Reports</a>
        <a href="settings.php">Settings</a>
      </div>
      <div class="logout-link">
        <a href="log_out.php" id="logoutBtn">Logout</a>
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
<!-- Logout Modal Markup -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Confirm Logout</h3>
        <form method="POST" action="log_out.php">
            <p>Are you sure you want to logout?</p>
            <button type="submit" name="confirm_logout">Logout</button>
            <button type="button" class="cancel-btn">Cancel</button>
        </form>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
