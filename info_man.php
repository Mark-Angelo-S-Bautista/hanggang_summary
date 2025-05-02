<?php
// error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
$conn = new mysqli("localhost", "root", "", "armansalon");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Update or Delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $edit_id = $_POST['edit_id'];

    if (isset($_POST['update'])) {
        // Collect all the updated data
        $edit_username = $_POST['edit_username'];
        $edit_selected_date = $_POST['edit_selected_date'];
        $edit_selected_time = $_POST['edit_selected_time'];
        $edit_stylist = $_POST['edit_stylist'];
        $edit_selected_service = $_POST['edit_selected_service'];
        $edit_email = $_POST['edit_email'];
        $edit_phoneNum = $_POST['edit_phoneNum'];
        $edit_gender = $_POST['edit_gender'];
        $edit_status = $_POST['edit_status'];

        // Update query
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
        // Delete query
        $deleteSql = "DELETE FROM form_info WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $edit_id);
        if ($stmt->execute()) {
            echo "<script>alert('Record deleted successfully.'); window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        }
        $stmt->close();
    }
}

// Delete via GET request
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

// Fetch records
$sql = "SELECT * FROM form_info";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Info Table</title>
    <style>
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

<h2>Form Info Table</h2>

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
            $selected_time = date("h:i A", strtotime($raw_time)); // Format as hh:mm AM/PM
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
            echo "<td>$status</td>";
            echo "<td>
                <a href='#' onclick='openEditModal(\"$id\", \"$selected_date\", \"$selected_time\", \"$stylist\", \"$selected_service\", \"$username\", \"$email\", \"$phoneNum\", \"$gender\", \"$status\")'>Edit</a> | 
                <a href='" . $_SERVER['PHP_SELF'] . "?id=$id' onclick=\"return confirm('Are you sure you want to delete this record?')\">Delete</a>
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
        <p>
            <label>Selected Date:</label><br>
            <input type="date" name="edit_selected_date" id="edit_selected_date">
        </p>
        <p>
            <label>Selected Time:</label><br>
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

        <p>
            <label>Stylist:</label><br>
            <input type="text" name="edit_stylist" id="edit_stylist">
        </p>
        <p>
            <label>Selected Service:</label><br>
            <input type="text" name="edit_selected_service" id="edit_selected_service">
        </p>
        <p>
            <label>Username:</label><br>
            <input type="text" name="edit_username" id="edit_username">
        </p>
        <p>
            <label>Email:</label><br>
            <input type="email" name="edit_email" id="edit_email">
        </p>
        <p>
            <label>Phone Number:</label><br>
            <input type="tel" name="edit_phoneNum" id="edit_phoneNum">
        </p>
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

    // Set the selected option for gender
    const genderSelect = document.getElementById('edit_gender');
    for (let i = 0; i < genderSelect.options.length; i++) {
        if (genderSelect.options[i].value.toLowerCase() === gender.toLowerCase()) {
            genderSelect.selectedIndex = i;
            break;
        }
    }

    // Set the selected option for status
    // Only set status if it's not "Scheduled"
    if (status && status.trim().toLowerCase() !== "scheduled") {
        for (let i = 0; i < statusSelect.options.length; i++) {
            if (statusSelect.options[i].value.toLowerCase() === status.trim().toLowerCase()) {
                statusSelect.selectedIndex = i;
                break;
            }
        }
    }
// If status is "Scheduled", don't change the dropdown (it stays on first option or previously selected)


    document.getElementById('editModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}
</script>
<p>hello</p>
</body>
</html>