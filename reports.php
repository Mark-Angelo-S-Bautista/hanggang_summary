<?php
session_start(); // Start session at the very beginning

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

require "database.php"; // Your database connection

// Get the current month from the dropdown and the current year
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentYear = date('Y');

// Get the search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Variables to store the most frequent username, selected_service, stylist, status counts, and total appointments
$mostFrequentUsername = 'N/A'; // Initialize with default
$mostFrequentService = 'N/A';  // Initialize with default
$mostFrequentStylist = 'N/A';  // Initialize with default
$statusCounts = [
    'Cancelled' => 0,
    'Completed' => 0,
    'In Session' => 0,
];
$totalAppointments = 0;

try {
    // Query to find the most frequent username for the current year and selected month
    $stmt = $pdo->prepare("SELECT username, COUNT(username) AS count
                           FROM form_info
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year
                           GROUP BY username
                           ORDER BY count DESC
                           LIMIT 1");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) { $mostFrequentUsername = $result['username']; }

    // Query to find the most frequent selected_service for the current year and selected month
    $stmt = $pdo->prepare("SELECT selected_service, COUNT(selected_service) AS count
                           FROM form_info
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year
                           GROUP BY selected_service
                           ORDER BY count DESC
                           LIMIT 1");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) { $mostFrequentService = $result['selected_service']; }

    // Query to find the most frequent stylist for the current year and selected month
    $stmt = $pdo->prepare("SELECT stylist, COUNT(stylist) AS count
                           FROM form_info
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year
                           GROUP BY stylist
                           ORDER BY count DESC
                           LIMIT 1");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) { $mostFrequentStylist = $result['stylist']; }

    // Query to count statuses for the current year and selected month
    $stmt = $pdo->prepare("SELECT status, COUNT(*) AS count
                           FROM form_info
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year
                           GROUP BY status");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['status'];
        if (array_key_exists($status, $statusCounts)) { // Use array_key_exists for robustness
            $statusCounts[$status] = $row['count'];
        }
    }

    // Query to count the total number of appointments for the selected month and year
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total
                           FROM form_info
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) { $totalAppointments = $result['total']; }

} catch (PDOException $e) {
    error_log("Reports Page PDOException: " . $e->getMessage()); // Log actual error
    // Display generic error or handle more gracefully
    $mostFrequentUsername = 'Error';
    $mostFrequentService = 'Error';
    $mostFrequentStylist = 'Error';
    $statusCounts = array_fill_keys(array_keys($statusCounts), 'Error');
    $totalAppointments = 'Error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Arman Salon</title>
    <link rel="stylesheet" href="styles/reports_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> 
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(event) {
                    event.preventDefault(); // prevent default link behavior
                    document.getElementById('logoutModal').style.display = 'block';
                    var container = document.querySelector('.container'); // Or your main content wrapper
                    if (container) {
                        container.classList.add('blur'); // Optional: if you have a blur style
                    }
                });
            }

            function closeModal() {
                document.getElementById('logoutModal').style.display = 'none';
                var container = document.querySelector('.container');
                if (container) {
                    container.classList.remove('blur');
                }
            }

            var closeIcon = document.querySelector('#logoutModal .modal-content .close');
            if (closeIcon) {
                closeIcon.addEventListener('click', closeModal);
            }

            var cancelBtn = document.querySelector('#logoutModal .modal-content .cancel-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeModal);
            }

            // Close modal if user clicks outside of the modal content
            window.addEventListener('click', function(event) {
                var modal = document.getElementById('logoutModal');
                if (event.target === modal) {
                    closeModal();
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div>
                <h2 class="as-heading">Arman Salon</h2>
                <a href="dashboard.php">Dashboard</a>
                <a href="info_man.php">Transactions</a>
                <a href="reports.php" class="active">Reports</a>
                <a href="settings.php">Settings</a>
            </div>
            <div class="logout-link">
                <a href="log_out.php" id="logoutBtn">Logout</a>
            </div>
        </div>
        <div class="main-content">
            <div class="search-container">
                <form method="GET" action="reports.php" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="month" value="<?php echo htmlspecialchars($currentMonth); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
            <div class="content-container">
                <div class="table-container">
                    <table>
                        <thead> <tr>
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
                        </tr>
                        </thead>
                        <tbody> <?php
                        try {
                            $query = "SELECT * FROM form_info WHERE YEAR(selected_date) = :year";
                            $params = ['year' => $currentYear];

                            if (!empty($search)) {
                                $query .= " AND username LIKE :search_term"; // Use a different placeholder name
                                $params['search_term'] = "%" . $search . "%";
                            }
                            // If you want the main table to also filter by selected month:
                            // $query .= " AND MONTH(selected_date) = :month_table";
                            // $params['month_table'] = $currentMonth;

                            $query .= " ORDER BY selected_date DESC, selected_time DESC";

                            $stmt = $pdo->prepare($query);
                            $stmt->execute($params);

                            if ($stmt->rowCount() > 0) {
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars(date("M d, Y", strtotime($row['selected_date']))) . "</td>"; // Formatted date
                                    echo "<td>" . htmlspecialchars(date("h:i A", strtotime($row['selected_time']))) . "</td>"; // Formatted time
                                    echo "<td>" . htmlspecialchars($row['stylist']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['selected_service']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['phoneNum']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>No records found for the current criteria.</td></tr>";
                            }
                        } catch (PDOException $e) {
                            error_log("Reports Page Table PDOException: " . $e->getMessage());
                            echo "<tr><td colspan='10'>Error retrieving data.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="summary-container">
                    <form method="GET" action="reports.php" class="month-filter">
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <?php endif; ?>
                        <label for="month_select">Month:</label>
                        <select name="month" id="month_select" onchange="this.form.submit()">
                            <?php for ($i = 1; $i <= 12; $i++):
                                $monthValue = str_pad($i, 2, '0', STR_PAD_LEFT);
                            ?>
                                <option value="<?php echo $monthValue; ?>"
                                    <?php echo ($monthValue == $currentMonth) ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        </form>
                    <div class="summary-content">
                        <div class="summary-left">
                            <h3>Summary for <?php echo date('F Y', mktime(0, 0, 0, (int)$currentMonth, 1, (int)$currentYear)); ?></h3>
                            <p><strong>Most Frequent Customer:</strong> <?php echo htmlspecialchars($mostFrequentUsername); ?></p>
                            <p><strong>Most Frequent Service:</strong> <?php echo htmlspecialchars($mostFrequentService); ?></p>
                            <p><strong>Most Frequent Stylist:</strong> <?php echo htmlspecialchars($mostFrequentStylist); ?></p>
                        </div>
                        <div class="summary-right">
                            <h3>Status Counts for <?php echo date('F Y', mktime(0, 0, 0, (int)$currentMonth, 1, (int)$currentYear)); ?></h3>
                            <p><strong>Cancelled:</strong> <?php echo htmlspecialchars($statusCounts['Cancelled']); ?></p>
                            <p><strong>Completed:</strong> <?php echo htmlspecialchars($statusCounts['Completed']); ?></p>
                            <p><strong>In Session:</strong> <?php echo htmlspecialchars($statusCounts['In Session']); ?></p>
                            <p><strong>Total Appointments:</strong> <?php echo htmlspecialchars($totalAppointments); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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