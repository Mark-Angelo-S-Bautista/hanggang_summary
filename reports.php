<?php
require "database.php";

// Get the current month from the dropdown and the current year
$currentMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$currentYear = date('Y');

// Get the search query
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Variables to store the most frequent username, selected_service, stylist, status counts, and total appointments
$mostFrequentUsername = '';
$mostFrequentService = '';
$mostFrequentStylist = '';
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
    $mostFrequentUsername = $result ? $result['username'] : 'N/A';

    // Query to find the most frequent selected_service for the current year and selected month
    $stmt = $pdo->prepare("SELECT selected_service, COUNT(selected_service) AS count 
                           FROM form_info 
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year 
                           GROUP BY selected_service 
                           ORDER BY count DESC 
                           LIMIT 1");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $mostFrequentService = $result ? $result['selected_service'] : 'N/A';

    // Query to find the most frequent stylist for the current year and selected month
    $stmt = $pdo->prepare("SELECT stylist, COUNT(stylist) AS count 
                           FROM form_info 
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year 
                           GROUP BY stylist 
                           ORDER BY count DESC 
                           LIMIT 1");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $mostFrequentStylist = $result ? $result['stylist'] : 'N/A';

    // Query to count statuses for the current year and selected month
    $stmt = $pdo->prepare("SELECT status, COUNT(*) AS count 
                           FROM form_info 
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year 
                           GROUP BY status");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['status'];
        if (isset($statusCounts[$status])) {
            $statusCounts[$status] = $row['count'];
        }
    }

    // Query to count the total number of appointments for the selected month and year
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total 
                           FROM form_info 
                           WHERE MONTH(selected_date) = :month AND YEAR(selected_date) = :year");
    $stmt->execute(['month' => $currentMonth, 'year' => $currentYear]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalAppointments = $result ? $result['total'] : 0;
} catch (PDOException $e) {
    $mostFrequentUsername = 'Error';
    $mostFrequentService = 'Error';
    $mostFrequentStylist = 'Error';
    $totalAppointments = 'Error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="styles/reports_styles.css">
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
                <a href="info_man.php" class="header-button">Information Management</a>
                <a href="reports.php" class="header-button active">Reports</a>
                <a href="settings.php" class="header-button">Settings</a>
                <a href="logout.php" class="header-button">Logout</a>
            </div>
        </div>
    </div>
    <div class="search-container">
        <form method="GET" action="" style="display: flex; align-items: center;">
            <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="content-container">
        <div class="table-container">
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
                </tr>
                <?php
                try {
                    // Query to fetch data from the form_info table for the current year
                    $query = "SELECT * FROM form_info WHERE YEAR(selected_date) = :year";
                    $params = ['year' => $currentYear];

                    if (!empty($search)) {
                        $query .= " AND username LIKE :search";
                        $params['search'] = "%$search%";
                    }

                    $stmt = $pdo->prepare($query);
                    $stmt->execute($params);

                    // Loop through the results and display each row in the table
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['selected_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['selected_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['stylist']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['selected_service']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phoneNum']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='10'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </table>
        </div>
        <div class="summary-container">
            <form method="GET" action="" class="month-filter">
                <select name="month">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $currentMonth) ? 'selected' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <button type="submit">Filter</button>
            </form>
            <div class="summary-content">
                <div class="summary-left">
                    <h3>Summary</h3>
                    <p><strong>Most Frequent Username:</strong> <?php echo htmlspecialchars($mostFrequentUsername); ?></p>
                    <p><strong>Most Frequent Selected Service:</strong> <?php echo htmlspecialchars($mostFrequentService); ?></p>
                    <p><strong>Most Frequent Stylist:</strong> <?php echo htmlspecialchars($mostFrequentStylist); ?></p>
                </div>
                <div class="summary-right">
                    <h3>Status Counts</h3>
                    <p><strong>Cancelled:</strong> <?php echo $statusCounts['Cancelled']; ?></p>
                    <p><strong>Completed:</strong> <?php echo $statusCounts['Completed']; ?></p>
                    <p><strong>In Session:</strong> <?php echo $statusCounts['In Session']; ?></p>
                    <p><strong>Total Appointments:</strong> <?php echo $totalAppointments; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>