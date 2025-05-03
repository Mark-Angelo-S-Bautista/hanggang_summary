<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$conn = new mysqli("localhost", "root", "", "armansalon");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $edit_id = $_POST['edit_id'];

    if (isset($_POST['update'])) {
        $edit_username = $_POST['edit_username'];
        $edit_selected_date = $_POST['edit_selected_date'];
        $edit_selected_time = $_POST['edit_selected_time'];
        $edit_stylist = $_POST['edit_stylist'];
        $edit_selected_service = $_POST['edit_selected_service'];
        $edit_email = $_POST['edit_email'];
        $edit_phoneNum = $_POST['edit_phoneNum'];
        $edit_gender = $_POST['edit_gender'];
        $edit_status = $_POST['edit_status'];

        // Fetch the current selected_date and selected_time from the database
$fetchSql = "SELECT selected_date, selected_time, status FROM form_info WHERE id = ?";
$fetchStmt = $conn->prepare($fetchSql);
$fetchStmt->bind_param("i", $edit_id);
$fetchStmt->execute();
$fetchResult = $fetchStmt->get_result();
$currentData = $fetchResult->fetch_assoc();
$fetchStmt->close();

$current_date = $currentData['selected_date'];
$current_time = $currentData['selected_time'];
$current_status = $currentData['status'];

// Convert new date and time to DateTime
date_default_timezone_set('Asia/Manila');
$combinedDateTime = new DateTime($edit_selected_date . ' ' . $edit_selected_time);
$currentDateTime = new DateTime();

// Reset status to "Scheduled" only if date/time changed, status wasn't manually changed, and new time is in the future
if (
    ($edit_selected_date !== $current_date || $edit_selected_time !== $current_time) &&
    $edit_status === $current_status &&
    $combinedDateTime > $currentDateTime
) {
    $edit_status = "Scheduled";
}

// Allow admin override from Cancelled to In Session
if ($current_status === "Cancelled" && $edit_status === "In Session") {
    $edit_status = "In Session";
}


        $updateSql = "UPDATE form_info SET 
                        username = ?, 
                        selected_date = ?, 
                        selected_time = ?, 
                        stylist = ?, 
                        selected_service = ?, 
                        email = ?, 
                        phoneNum = ?, 
                        gender = ?, 
                        status = ? 
                      WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssssssssi", 
                        $edit_username, 
                        $edit_selected_date, 
                        $edit_selected_time, 
                        $edit_stylist, 
                        $edit_selected_service, 
                        $edit_email, 
                        $edit_phoneNum, 
                        $edit_gender, 
                        $edit_status, 
                        $edit_id);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $deleteSql = "DELETE FROM form_info WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $edit_id);
        if ($stmt->execute()) {
            echo "<script>alert('Record deleted successfully.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        }
        $stmt->close();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM form_info WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "Record deleted successfully.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        $stmt->close();
    }
}

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');
$now = new DateTime();

$sql = "SELECT * FROM form_info WHERE selected_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $appointmentId = $row['id'];
    $appointmentTime = new DateTime($row['selected_date'] . ' ' . $row['selected_time']);
    $appointmentTime->modify('+15 minutes');

    $status = $row['status'];
    if ($status !== 'Cancelled' && $status !== 'Completed' && $now > $appointmentTime) {
        $updateStmt = $conn->prepare("UPDATE form_info SET status = 'Cancelled' WHERE id = ?");
        $updateStmt->bind_param("i", $appointmentId);
        $updateStmt->execute();
        $updateStmt->close();
    }
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Info Table</title>
    <link rel="stylesheet" href="styles/info_man.css">

    <style>
        .header-wrapper {
        margin-top: 80px; /* Add space to account for the fixed header-background */
        text-align: center;
        padding: 20px;
        }

        .header-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%; /* Extend the background to the full width of the page */
        height: 80px; /* Match the height of the header */
        background-color: #f35b53;
        z-index: 1000; /* Place the background behind the header content */
        }

        .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background-color: #f35b53;
        color: white;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 90%; /* Keep the content width */
        max-width: 1200px; /* Ensure it doesn't exceed the table's max width */
        margin: 0 auto; /* Center the content horizontally */
        position: relative; /* Keep the content positioned relative to the header */
        z-index: 1001; /* Ensure the header content stays above the background */
        }

        .header-logo img {
        height: 50px;
        width: auto;
        }

        .header-title {
        font-size: 24px;
        font-weight: bold;
        font-family: "Segoe UI", sans-serif;
        text-align: center;
        flex-grow: 1; /* Push the title to the center */
        }

        .header-buttons {
        display: flex;
        gap: 15px;
        }

        .header-button {
        padding: 10px 15px;
        background-color: white;
        color: #f35b53;
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        }

        .header-button:hover {
        background-color: #e35b53;
        color: white;
        }

        .header-button.active {
        background-color: #e35b53;
        color: white;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
        #editModal {
            display: none;
            position: fixed;
            top: 20%;
            left: 30%;
            background: #fff;
            border: 1px solid #000;
            padding: 20px;
            z-index: 1000;
        }
        #modalOverlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <div class="header-background">
        <div class="header">
            <div class="header-logo">
                <img src="img/logo.png" alt="Arman Salon Logo">
            </div>
            <div class="header-title">
                Arman Salon
            </div>
            <div class="header-buttons">
                <a href="dashboard.php" class="header-button">Dashboard</a>
                <a href="info_man.php" class="header-button active">Information Management</a>
                <a href="reports.php" class="header-button">Reports</a>
                <a href="settings.php" class="header-button">Settings</a>
                <a href="logout.php" class="header-button">Logout</a>
            </div>
        </div>
    </div>
<div class="header-wrapper">
    <h2>Appointments for Today</h2>
    <div class="date-time">
        <div id="currentDate" class="date"></div>
        <div id="currentDayTime" class="weekday-time"></div>
    </div>
</div>


<table>
    <tr>
        <th>ID</th>
        <th>Selected Date</th>
        <th>Selected Time</th>
        <th>Stylist</th>
        <th>Selected Service</th>
        <th>Username</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>Gender</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = htmlspecialchars($row["id"]);
            $selected_date = htmlspecialchars($row["selected_date"]);
            $raw_time = $row["selected_time"];
            $selected_time = date("h:i A", strtotime($raw_time));
            $stylist = htmlspecialchars($row["stylist"]);
            $selected_service = htmlspecialchars($row["selected_service"]);
            $username = htmlspecialchars($row["username"]);
            $email = htmlspecialchars($row["email"]);
            $phoneNum = htmlspecialchars($row["phoneNum"]);
            $gender = htmlspecialchars($row["gender"]);
            $status = htmlspecialchars($row["status"]);

            echo "<tr>";
            echo "<td>$id</td>";
            echo "<td>$selected_date</td>";
            echo "<td>$selected_time</td>";
            echo "<td>$stylist</td>";
            echo "<td>$selected_service</td>";
            echo "<td>$username</td>";
            echo "<td>$email</td>";
            echo "<td>$phoneNum</td>";
            echo "<td>$gender</td>";
            if (strtolower($status) === 'in session') {
                echo "<td><span class='status-in-session'>$status</span></td>";
            } else {
                echo "<td>$status</td>";
            }
            
            echo "<td>
                <a href='#' class='edit-btn' onclick='openEditModal(\"$id\", \"$selected_date\", \"$selected_time\", \"$stylist\", \"$selected_service\", \"$username\", \"$email\", \"$phoneNum\", \"$gender\", \"$status\")'>Edit</a> | 
                <a href='" . $_SERVER['PHP_SELF'] . "?id=$id' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this record?')\">Delete</a>
              </td>";
            echo "</tr>";
        }
    }
    ?>
</table>

<!-- Modal Background -->
<div id="modalOverlay"></div>

<!-- Modal Popup Form -->
<div id="editModal">
    <h3>Edit Appointment</h3>
    <form id="editForm" method="POST" action="">
        <input type="hidden" name="edit_id" id="edit_id">
        <p><label>Selected Date:</label><br><input type="date" name="edit_selected_date" id="edit_selected_date"></p>
        <p><label>Selected Time:</label><br>
            <select name="edit_selected_time" id="edit_selected_time">
                <option value="09:00 AM">9:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="01:00 PM">1:00 PM</option>
                <option value="02:00 PM">2:00 PM</option>
                <option value="03:00 PM">3:00 PM</option>
                <option value="04:00 PM">4:00 PM</option>
                <option value="05:00 PM">5:00 PM</option>
            </select>
        </p>
        <p><label>Stylist:</label><br><input type="text" name="edit_stylist" id="edit_stylist"></p>
        <p><label>Selected Service:</label><br><input type="text" name="edit_selected_service" id="edit_selected_service"></p>
        <p><label>Username:</label><br><input type="text" name="edit_username" id="edit_username"></p>
        <p><label>Email:</label><br><input type="email" name="edit_email" id="edit_email"></p>
        <p><label>Phone Number:</label><br><input type="tel" name="edit_phoneNum" id="edit_phoneNum"></p>
        <p>
            <label>Gender:</label><br>
            <select name="edit_gender" id="edit_gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Prefer not to say">Prefer not to say</option>
            </select>
        </p>
        <p>
            <label>Status:</label><br>
            <select name="edit_status" id="edit_status">
                <option value="Scheduled">Scheduled</option>
                <option value="In Session">In Session</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </p>
        <p>
            <button type="submit" name="update" onclick="return confirm('Are you sure you want to update this record?')">Update</button>
            <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </p>
    </form>
</div>

<script>
function openEditModal(id, selected_date, selected_time, stylist, selected_service, username, email, phoneNum, gender, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_selected_date').value = selected_date;
    document.getElementById('edit_selected_time').value = selected_time;
    document.getElementById('edit_stylist').value = stylist;
    document.getElementById('edit_selected_service').value = selected_service;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phoneNum').value = phoneNum;

    const genderSelect = document.getElementById('edit_gender');
    for (let i = 0; i < genderSelect.options.length; i++) {
        if (genderSelect.options[i].value.toLowerCase() === gender.toLowerCase()) {
            genderSelect.selectedIndex = i;
            break;
        }
    }

    const statusSelect = document.getElementById('edit_status');
    for (let i = 0; i < statusSelect.options.length; i++) {
        if (statusSelect.options[i].value.toLowerCase() === status.trim().toLowerCase()) {
            statusSelect.selectedIndex = i;
            break;
        }
    }

    document.getElementById('editModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}

function formatDateTime() {
    const now = new Date();

    const optionsDate = { year: 'numeric', month: 'long', day: 'numeric' };
    const optionsDay = { weekday: 'long' };
    
    const date = now.toLocaleDateString('en-US', optionsDate);
    const day = now.toLocaleDateString('en-US', optionsDay);
    
    let hours = now.getHours();
    let minutes = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12; // Convert to 12-hour format
    minutes = minutes < 10 ? '0' + minutes : minutes;

    const time = `${hours}:${minutes} ${ampm}`;

    document.getElementById('currentDate').innerText = date;
    document.getElementById('currentDayTime').innerText = `${day} | ${time}`;
}

formatDateTime();
setInterval(formatDateTime, 60000); // update every minute
</script>

</body>
</html>