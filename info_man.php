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

$conn = new mysqli("localhost", "root", "", "armansalon");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch hairstylists and services options
$stylistQuery = "SELECT option_value FROM options WHERE option_type = 'hairstylists'";
$stylistResult = $conn->query($stylistQuery);
$hairstylists = [];
while ($row = $stylistResult->fetch_assoc()) {
    $hairstylists[] = $row['option_value'];
}

$serviceQuery = "SELECT option_value FROM options WHERE option_type = 'services'";
$serviceResult = $conn->query($serviceQuery);
$services = [];
while ($row = $serviceResult->fetch_assoc()) {
    $services[] = $row['option_value'];
}

$timeQuery = "SELECT option_value FROM options WHERE option_type = 'times' ORDER BY option_value ASC";
$timeResult = $conn->query($timeQuery);
$available_times = [];
while ($row = $timeResult->fetch_assoc()) {
    $available_times[] = $row['option_value'];
}
// If you want to be sure, sort using PHP as well:
usort($available_times, function($a, $b) {
    return strtotime($a) - strtotime($b);
});

//$currentPage = basename($_SERVER['PHP_SELF']); // Get the current page filename

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

        // Reset status to "Scheduled" if date/time changed, status wasn't manually changed, and new time + 15min is >= now
        if (
            ($edit_selected_date !== $current_date || $edit_selected_time !== $current_time) &&
            $edit_status === $current_status
        ) {
            $graceDateTime = new DateTime($edit_selected_date . ' ' . $edit_selected_time);
            $graceDateTime->modify('+15 minutes');
            $currentDateTime = new DateTime();

            if ($graceDateTime >= $currentDateTime) {
                $edit_status = "Scheduled";
            }
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
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $conn->error;
        }

        $stmt->close();
        exit(); // Stop further execution
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
$now = new DateTime();

$today = date('Y-m-d');

$sql = "SELECT * FROM form_info WHERE selected_date = ? ORDER BY STR_TO_DATE(selected_time, '%l:%i %p') ASC";
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
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="styles/info_man.css">
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div>
            <h2 class="as-heading">Arman Salon</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="info_man.php" class="active">Transactions</a>
            <a href="reports.php">Reports</a>
            <a href="settings.php">Settings</a>
        </div>
        <div class="logout-link">
            <a href="log_out.php" id="logoutBtn">Logout</a>
        </div>
    </div>

    <div class="main-content-wrapper">
    <div class="main-content">
        <div class="header-wrapper">
            <h2>Appointments for Today</h2>
            <div class="date-time">
                <div id="currentDate" class="date"></div>
                <div id="currentDayTime" class="weekday-time"></div>
            </div>
        </div>

        <div class="table-container">
        <div class="search-wrapper">
            <span class="search-icon">
                <!-- You can use an SVG for crisp icon -->
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                    <circle cx="9" cy="9" r="7" stroke="#ff5b5b" stroke-width="2"/>
                    <line x1="14.4142" y1="14" x2="18" y2="17.5858" stroke="#ff5b5b" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <input type="text" id="searchName" placeholder="Search by name...">
        </div>
        <table>
            <thead>
            <tr>
                <th>Selected Date</th>
                <th>Selected Time</th>
                <th>Stylist</th>
                <th>Selected Service</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = htmlspecialchars($row["id"]);
                    $selected_date = htmlspecialchars($row["selected_date"]);
                    $raw_time = $row["selected_time"];
                    $selected_time = date("g:i A", strtotime($raw_time));
                    $stylist = htmlspecialchars($row["stylist"]);
                    $selected_service = htmlspecialchars($row["selected_service"]);
                    $username = htmlspecialchars($row["username"]);
                    $email = htmlspecialchars($row["email"]);
                    $phoneNum = htmlspecialchars($row["phoneNum"]);
                    $gender = htmlspecialchars($row["gender"]);
                    $status = htmlspecialchars($row["status"]);

                    echo "<tr>";
                    echo "<td>$selected_date</td>";
                    echo "<td>$selected_time</td>";
                    echo "<td>$stylist</td>";
                    echo "<td>$selected_service</td>";
                    echo "<td>$username</td>";
                    echo "<td>$email</td>";
                    echo "<td>$phoneNum</td>";
                    echo "<td>$gender</td>";
                    if (strtolower($status) === 'in session') {
                        echo "<td><span class='status-in-session'><span class='dot'></span>$status</span></td>";
                    } elseif (strtolower($status) === 'completed') {
                        echo "<td><span class='status-completed'>$status</span></td>";
                    } elseif (strtolower($status) === 'cancelled') {
                        echo "<td><span class='status-cancelled'>$status</span></td>";
                    } else {
                        echo "<td>$status</td>";
                    }
                    
                    echo "<td>
                      <span class='action-buttons'>
                        <a href='#' class='edit-btn' onclick='openEditModal(\"$id\", \"$selected_date\", \"$selected_time\", \"$stylist\", \"$selected_service\", \"$username\", \"$email\", \"$phoneNum\", \"$gender\", \"$status\")'>Edit</a>
                        <span class='action-sep'>|</span>
                        <a href='" . $_SERVER['PHP_SELF'] . "?id=$id' class='delete-btn' onclick=\"return confirm('Are you sure you want to delete this record?')\">Delete</a>
                      </span>
                    </td>";
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
        <div class="table-container">
    </div>
</div>
</div>

<!-- Modal Background -->
<div id="modalOverlay"></div>

<!-- Modal Popup Form -->
<div id="editModal">
    <h3>Edit Appointment</h3>
    <form id="editForm" method="POST" action="" onsubmit="return validateForm();">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="form-container">
            <!-- Left Side -->
            <div class="form-column">
                <p><label>Selected Date:</label><br><input type="date" name="edit_selected_date" id="edit_selected_date"></p>
                <p><label>Selected Time:</label><br>
                    <select name="edit_selected_time" id="edit_selected_time">
                        <?php foreach ($available_times as $time): ?>
                            <option value="<?php echo htmlspecialchars(date("g:i A", strtotime($time))); ?>">
                                <?php echo date("g:i A", strtotime($time)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <!-- Stylist Dropdown -->
                <p>
                    <label>Stylist:</label><br>
                    <select name="edit_stylist" id="edit_stylist">
                        <?php foreach ($hairstylists as $stylist): ?>
                            <option value="<?php echo htmlspecialchars($stylist); ?>"><?php echo htmlspecialchars($stylist); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <!-- Selected Service Dropdown -->
                <p>
                    <label>Selected Service:</label><br>
                    <select name="edit_selected_service" id="edit_selected_service">
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo htmlspecialchars($service); ?>"><?php echo htmlspecialchars($service); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
            </div>

            <!-- Right Side -->
            <div class="form-column">
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
            </div>
        </div>

        <!-- Buttons -->
        <div class="form-buttons">
            <button type="submit" name="update">Update</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </div>
    </form>
</div>

<script>
function openEditModal(id, selected_date, selected_time, stylist, selected_service, username, email, phoneNum, gender, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_selected_date').value = selected_date;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phoneNum').value = phoneNum;

    // Set the selected value for the stylist dropdown
    const stylistDropdown = document.getElementById('edit_stylist');
    for (let i = 0; i < stylistDropdown.options.length; i++) {
        if (stylistDropdown.options[i].value === stylist) {
            stylistDropdown.selectedIndex = i;
            break;
        }
    }

    // Set the selected value for the service dropdown
    const serviceDropdown = document.getElementById('edit_selected_service');
    for (let i = 0; i < serviceDropdown.options.length; i++) {
        if (serviceDropdown.options[i].value === selected_service) {
            serviceDropdown.selectedIndex = i;
            break;
        }
    }

    // Set the selected value for the gender dropdown
    const genderSelect = document.getElementById('edit_gender');
    for (let i = 0; i < genderSelect.options.length; i++) {
        if (genderSelect.options[i].value.toLowerCase() === gender.toLowerCase()) {
            genderSelect.selectedIndex = i;
            break;
        }
    }

    // Set the selected value for the status dropdown
    const statusSelect = document.getElementById('edit_status');
    for (let i = 0; i < statusSelect.options.length; i++) {
        if (statusSelect.options[i].value.toLowerCase() === status.trim().toLowerCase()) {
            statusSelect.selectedIndex = i;
            break;
        }
    }

    // Set the selected value for the time dropdown
    const timeDropdown = document.getElementById('edit_selected_time');
    console.log('selected_time:', selected_time);
    function normalizeTime(str) {
        return str.replace(/:00(\s|$)/, '').replace(/\s+/g, ' ').trim().toLowerCase();
    }
    for (let i = 0; i < timeDropdown.options.length; i++) {
        if (
            normalizeTime(timeDropdown.options[i].value) === normalizeTime(selected_time)
        ) {
            timeDropdown.selectedIndex = i;
            break;
        }
    }

    // Show the modal and overlay
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function closeModal() {
    // Hide the modal and overlay
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}

function deleteRecord() {
    const editId = document.getElementById('edit_id').value;

    if (confirm('Are you sure you want to delete this record?')) {
        fetch('info_man.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `delete=true&edit_id=${editId}`,
        })
            .then((response) => response.text())
            .then((data) => {
                alert('Record deleted successfully.');
                location.reload(); // Reload the page to reflect the changes
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Failed to delete the record.');
            });
    }
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

document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#edit_selected_date", {
        dateFormat: "Y-m-d", // Format to match your database
        minDate: "today", // Prevent selecting past dates
        defaultDate: new Date(), // Set the default date to today
        disableMobile: true // Force the custom calendar on mobile devices
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('edit_phoneNum');

    phoneInput.addEventListener('input', function () {
        // Allow only 11 digits
        if (phoneInput.value.length > 11) {
            phoneInput.value = phoneInput.value.slice(0, 11); // Trim to 11 digits
        }
    });

    phoneInput.addEventListener('keypress', function (event) {
        // Prevent non-numeric input
        if (!/[0-9]/.test(event.key)) {
            event.preventDefault();
        }
    });
});

function validateForm() {
    const email = document.getElementById('edit_email').value.trim();
    const phone = document.getElementById('edit_phoneNum').value.trim();

    // Strict email pattern: something@something.something (e.g., user@gmail.com)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    // Phone: exactly 11 digits
    const phoneRegex = /^\d{11}$/;

    let isValid = true;

    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address (e.g., user@gmail.com).");
        isValid = false;
    }

    if (!phoneRegex.test(phone)) {
        alert("Phone number must be exactly 11 digits.");
        isValid = false;
    }

    return isValid; // Return true if both validations pass
}

function updateStatusBasedOnTime() {
    const dateInput = document.getElementById('edit_selected_date');
    const timeSelect = document.getElementById('edit_selected_time');
    const statusSelect = document.getElementById('edit_status');

    if (!dateInput || !timeSelect || !statusSelect) return;

    const selectedDate = dateInput.value;
    const selectedTime = timeSelect.value;

    if (!selectedDate || !selectedTime) return;

    // Combine date and time into a Date object
    const selectedDateTime = new Date(selectedDate + 'T' + selectedTime);
    // Add 15 minutes grace period
    selectedDateTime.setMinutes(selectedDateTime.getMinutes() + 15);

    const now = new Date();

    // If the appointment (with grace period) is still in the future, set status to Scheduled
    if (selectedDateTime > now) {
        statusSelect.value = "Scheduled";
    }
}

// Listen for changes on date and time fields
document.addEventListener('DOMContentLoaded', function () {
    const dateInput = document.getElementById('edit_selected_date');
    const timeSelect = document.getElementById('edit_selected_time');

    if (dateInput) dateInput.addEventListener('change', updateStatusBasedOnTime);
    if (timeSelect) timeSelect.addEventListener('change', updateStatusBasedOnTime);
});

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

document.getElementById('searchName').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const table = document.querySelector('.table-container table');
    const trs = table.querySelectorAll('tbody tr');
    // Name is the 5th column (index 4, since index starts at 0)
    trs.forEach(tr => {
        const nameCell = tr.children[4];
        if (nameCell && nameCell.textContent.toLowerCase().includes(filter)) {
            tr.style.display = '';
        } else {
            tr.style.display = 'none';
        }
    });
});
</script>
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
``` 