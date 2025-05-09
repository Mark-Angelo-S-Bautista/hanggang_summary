<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: admin_login.php");
    exit();
}

// Send no-cache headers
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

require "database.php";

// ----- Process Delete Action via GET -------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM options WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: settings.php"); // Refresh page
    exit();
}

// ----- Process Update Action -------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    // Update an existing option
    $id = intval($_POST['id']);
    $new_value = trim($_POST['new_value']);
    $stmt = $pdo->prepare("UPDATE options SET option_value = :new_value WHERE id = :id");
    $stmt->execute([':new_value' => $new_value, ':id' => $id]);
    $message = "Option updated successfully.";
}

// ----- Process Add Action -------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $option_type = $_POST['option_type'];
    $new_value = trim($_POST['new_value']);
    if (!empty($new_value)) {
        $stmt = $pdo->prepare("INSERT INTO options (option_type, option_value) VALUES (:option_type, :new_value)");
        $stmt->execute([':option_type' => $option_type, ':new_value' => $new_value]);
        $message = "New option added successfully.";
    } else {
        $message = "Please enter a value.";
    }
}

// Function to fetch options by type
function getOptions($pdo, $type) {
    $stmt = $pdo->prepare("SELECT * FROM options WHERE option_type = :type ORDER BY id ASC");
    $stmt->execute([':type' => $type]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$timesOptions = getOptions($pdo, 'times');
$hairstylistsOptions = getOptions($pdo, 'hairstylists');
$servicesOptions = getOptions($pdo, 'services');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Arman Salon</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <!-- Using Flatpickr stylesheet if needed -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="styles/reports_styles.css">
    <style>
        /* Styles for the settings CRUD interface */
        .settings_form_container {
            max-width: 800px; /* Limit the width of the form */
            width: 100%; /* Allow it to shrink on smaller screens */
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
        }
        .settings_form_container h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        .option_section {
            margin-bottom: 30px;
        }
        .option_section h3 {
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        form.inline_form {
            display: inline;
        }
        .settings_form_container input[type="text"] {
            padding: 5px;
            width: 60%;
        }
        .settings_form_container button {
            padding: 5px 10px;
            background-color: #f35b53;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            color: green;
            text-align: center;
            margin-bottom: 15px;
        }
        /* Modal Styles (for logout) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            overflow: auto;
        }
        .modal-content {
            background: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            position: relative;
        }
        .modal-content .close {
            position: absolute;
            top: 8px;
            right: 12px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.getElementById('logoutModal').style.display = 'block';
                    var container = document.querySelector('.container');
                    if (container) {
                        container.classList.add('blur');
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
        <!-- Sidebar -->
        <div class="sidebar">
            <div>
                <h2 class="as-heading">Arman Salon</h2>
                <a href="dashboard.php">Dashboard</a>
                <a href="info_man.php">Transactions</a>
                <a href="reports.php">Reports</a>
                <a href="settings.php">Settings</a>
            </div>
            <div class="logout-link">
                <a href="log_out.php" id="logoutBtn">Logout</a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main_content">
            <div class="settings_form_container">
                <h2>Manage Form Options</h2>
                <?php if (!empty($message)): ?>
                    <p class="message"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <!-- TIMES SECTION -->
                <div class="option_section">
                    <h3>Available Times</h3>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach (getOptions($pdo, 'times') as $option): ?>
                        <tr>
                            <td><?php echo $option['id']; ?></td>
                            <td>
                                <form method="POST" action="settings.php" class="inline_form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $option['id']; ?>">
                                    <input type="text" name="new_value" value="<?php echo htmlspecialchars($option['option_value']); ?>">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="settings.php?action=delete&id=<?php echo $option['id']; ?>" onclick="return confirm('Delete this time?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <form method="POST" action="settings.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="option_type" value="times">
                        <input type="text" name="new_value" placeholder="Add new time">
                        <button type="submit">Add Time</button>
                    </form>
                </div>

                <!-- HAIRSTYLISTS SECTION -->
                <div class="option_section">
                    <h3>Hairstylists</h3>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Hairstylist</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach (getOptions($pdo, 'hairstylists') as $option): ?>
                        <tr>
                            <td><?php echo $option['id']; ?></td>
                            <td>
                                <form method="POST" action="settings.php" class="inline_form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $option['id']; ?>">
                                    <input type="text" name="new_value" value="<?php echo htmlspecialchars($option['option_value']); ?>">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="settings.php?action=delete&id=<?php echo $option['id']; ?>" onclick="return confirm('Delete this hairstylist?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <form method="POST" action="settings.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="option_type" value="hairstylists">
                        <input type="text" name="new_value" placeholder="Add new hairstylist">
                        <button type="submit">Add Hairstylist</button>
                    </form>
                </div>

                <!-- SERVICES SECTION -->
                <div class="option_section">
                    <h3>Services</h3>
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Actions</th>
                        </tr>
                        <?php foreach (getOptions($pdo, 'services') as $option): ?>
                        <tr>
                            <td><?php echo $option['id']; ?></td>
                            <td>
                                <form method="POST" action="settings.php" class="inline_form">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $option['id']; ?>">
                                    <input type="text" name="new_value" value="<?php echo htmlspecialchars($option['option_value']); ?>">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="settings.php?action=delete&id=<?php echo $option['id']; ?>" onclick="return confirm('Delete this service?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <form method="POST" action="settings.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="option_type" value="services">
                        <input type="text" name="new_value" placeholder="Add new service">
                        <button type="submit">Add Service</button>
                    </form>
                </div>
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