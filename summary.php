<?php
require "config.session.php"; // Make sure session_start() is included
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Summary</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/summary_style.css"> 
</head>
<body>

<!-- TOP RED BAR -->
<div class="top-bar">
    <img src="img/logo.png" alt="Logo" class="logo"> 
    <h1 class="bar-title">Arman Salon</h1>
</div>

<!-- MAIN CONTAINER -->
<div class="container">
    <div class="content-box">
        <div class="summary-left">
    <?php
    if (isset($_SESSION['appointment'])) {
        $appointment = $_SESSION['appointment'];
    ?>
    
    <!-- Red Box for Appointment Schedule and Date/Time -->
    <div class="sched-box">
        <p class="sched">Appointment Schedule</p>
        <div style="text-align: center;">
            <span style="font-weight: bold; color: #ff5b5b;">
                <?php
                    echo date("F j, Y g:i a", strtotime($appointment['selected_date'] . ' ' . $appointment['selected_time']));
                ?>
            </span>
        </div>
    </div>

    <!-- Name, Phone Number, Service, Stylist outside red box -->
    <p><strong>Name:</strong> <?php echo htmlspecialchars($appointment['username']); ?></p>
    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($appointment['phoneNum']); ?></p>
    <p><strong>Service:</strong> <?php echo htmlspecialchars($appointment['selected_service']); ?></p>
    <p><strong>Stylist:</strong> <?php echo htmlspecialchars($appointment['stylist']); ?></p>
    
    <?php
    } else {
        echo "<p>No appointment data found.</p>";
    }
    ?>
</div>


        <div class="reminders-right">
            <h2 class="reminder">Reminders</h2>
            <ul>
                <li>Please arrive 10 minutes before your appointment.</li>
                <li>Wearing a face mask is mandatory.</li>
                <li>Cancellation must be done 24 hours in advance.</li>
                <li>No late arrivals beyond 15 minutes allowed.</li>
            </ul>
            <div class="back-home">
        <a href="index.php" class="btn-home">Back to Home</a>
    </div>
        </div>
    </div>

    
</div>

</body>
</html>
