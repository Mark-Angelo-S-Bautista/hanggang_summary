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
    $id = intval($_POST['id']);
    $new_value = trim($_POST['new_value']);

    // Check if this is a hairstylist and if a new image is uploaded
    $option = $pdo->prepare("SELECT * FROM options WHERE id = :id");
    $option->execute([':id' => $id]);
    $row = $option->fetch(PDO::FETCH_ASSOC);

    $updateImageSQL = '';
    $params = [':new_value' => $new_value, ':id' => $id];

    if ($row && $row['option_type'] === 'hairstylists' && isset($_FILES['stylist_image']) && $_FILES['stylist_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'img/';
        $imageFileName = uniqid('stylist_') . '_' . basename($_FILES['stylist_image']['name']);
        $uploadFile = $uploadDir . $imageFileName;
        move_uploaded_file($_FILES['stylist_image']['tmp_name'], $uploadFile);
        $updateImageSQL = ', image = :image';
        $params[':image'] = $imageFileName;
    }

    $stmt = $pdo->prepare("UPDATE options SET option_value = :new_value $updateImageSQL WHERE id = :id");
    $stmt->execute($params);
    $message = "Option updated successfully.";
}

// ----- Process Add Action -------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'add') {
    $option_type = $_POST['option_type'];
    $new_value = trim($_POST['new_value']);
    $imageFileName = null;

    if ($option_type === 'hairstylists' && isset($_FILES['stylist_image']) && $_FILES['stylist_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'img/';
        $imageFileName = uniqid('stylist_') . '_' . basename($_FILES['stylist_image']['name']);
        $uploadFile = $uploadDir . $imageFileName;
        move_uploaded_file($_FILES['stylist_image']['tmp_name'], $uploadFile);
    }

    if (!empty($new_value)) {
        if ($option_type === 'hairstylists') {
            $stmt = $pdo->prepare("INSERT INTO options (option_type, option_value, image) VALUES (:option_type, :new_value, :image)");
            $stmt->execute([':option_type' => $option_type, ':new_value' => $new_value, ':image' => $imageFileName]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO options (option_type, option_value) VALUES (:option_type, :new_value)");
            $stmt->execute([':option_type' => $option_type, ':new_value' => $new_value]);
        }
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
    <link rel="stylesheet" href="styles/reports_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <!-- Using Flatpickr stylesheet if needed -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Styles for the settings CRUD interface */
        .settings_form_container {
            max-width: 500px; /* Limit the width of the form */
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
        .settings_form_container a.delete-btn {
            display: inline-block;
            padding: 4px 10px;
            background: #ff5b5b;
            color: #fff !important;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-left: 5px;
            transition: background 0.2s;
        }
        .settings_form_container a.delete-btn:hover {
            background: #e04848;
            color: #fff !important;
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
        .sidebar a.active {
            background-color: #e04848; /* Darker red for active */
            color: #fff;
            border-left: 5px solid #fff;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(255,91,91,0.15); /* Optional: subtle shadow */
            transition: background 0.2s;
        }
        /* Make the container a flex row */
        .container {
            display: flex;
            min-height: 100vh;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        /* Sidebar stays fixed height and does not scroll */
        .sidebar {
            width: 250px;
            background-color: #ff5b5b;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 0;
            height: 100vh;
            flex-shrink: 0;
            position: sticky;
            left: 0;
            top: 0;
            z-index: 10;
        }

        /* Main content scrolls vertically */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start; /* Only horizontal centering */
            width: 100%;
            height: 100vh;
            overflow-y: auto;
            background: transparent;
            padding: 30px 0;
            box-sizing: border-box;
        }
        .settings_form_container th {
            position: static !important;
        }

        .add-stylist-form {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }

        .add-stylist-form .stylist-text {
            width: 50% !important;
        }

        .add-stylist-form .custom-file-label {
            position: relative;
            overflow: hidden;
            display: inline-block;
            background-color: #f35b53;
            color: #fff;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
            margin-right: 4px;
            white-space: nowrap;
        }

        .add-stylist-form .custom-file-label:hover {
            background-color: #e04848;
        }

        .add-stylist-form .stylist-file {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .add-stylist-form button {
            padding: 5px 10px;
            background-color: #f35b53;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .stylist-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }

        .stylist-update-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stylist-img-label {
            display: flex;
            align-items: center;
            margin-right: 8px;
            cursor: pointer;
        }

        .stylist-img {
            width: 38px;
            height: 38px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ccc;
            transition: box-shadow 0.2s;
            cursor: pointer;
        }

        .stylist-img:hover {
            box-shadow: 0 0 0 2px #f35b53;
        }

        .stylist-update-form .stylist-text {
            height: 38px;
            padding: 5px 8px;
            font-size: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
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

        function showFileName(input) {
            var label = input.parentNode.parentNode.querySelector('#file-name-label');
            if (input.files.length > 0) {
                label.style.display = 'none';
            } else {
                label.style.display = 'block';
                label.textContent = 'No file chosen';
            }
        }
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
                <a href="settings.php" class="active">Settings</a>
            </div>
            <div class="logout-link">
                <a href="log_out.php" id="logoutBtn">Logout</a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="main-content">
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
                            </td>
                            <td>
                                    <button type="submit">Update</button>
                                </form>
                                <a href="settings.php?action=delete&id=<?php echo $option['id']; ?>" class="delete-btn" onclick="return confirm('Delete this time?');">Delete</a>
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
                                <form method="POST" action="settings.php" class="inline_form" id="stylist-form-<?php echo $option['id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="id" value="<?php echo $option['id']; ?>">
                                    <input type="text" name="new_value" value="<?php echo htmlspecialchars($option['option_value']); ?>" class="stylist-text">
                                </form>
                            </td>
                            <td>
                                <button type="submit" form="stylist-form-<?php echo $option['id']; ?>">Update</button>
                                <a href="settings.php?action=delete&id=<?php echo $option['id']; ?>" class="delete-btn" onclick="return confirm('Delete this hairstylist?');">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <form method="POST" action="settings.php" enctype="multipart/form-data" class="add-stylist-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="option_type" value="hairstylists">
                        <input type="text" name="new_value" placeholder="Add new hairstylist" class="stylist-text">
                        <div style="display: flex; flex-direction: column;">
                            <label class="custom-file-label">
                                <input type="file" name="stylist_image" accept="image/*" class="stylist-file" onchange="showFileName(this)">
                                Choose Image
                            </label>
                            <div id="file-name-label" style="font-size:12px;color:#888; width:100%; text-align:center;">No file chosen</div>
                        </div>
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
                            </td>
                            <td>
                                    <button type="submit">Update</button>
                                </form>
                                <a href="settings.php?action=delete&id=<?php echo $option['id']; ?>" class="delete-btn" onclick="return confirm('Delete this service?');">Delete</a>
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