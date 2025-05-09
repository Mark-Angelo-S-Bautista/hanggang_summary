<?php
require "form/form.view.php";
require "config.session.php";
require "database.php";

// Query for the settings options from the database
$query = "SELECT option_type, option_value 
          FROM options 
          WHERE option_type IN ('times', 'hairstylists', 'services')";
$stmt = $pdo->query($query);

// Initialize options arrays
$options = [
    'times' => [],
    'hairstylists' => [],
    'services' => []
];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Instead of replacing, we add each option value to the array.
    $value = trim($row['option_value']);
    $options[$row['option_type']][] = $value;
}

// Partition times into Morning and Afternoon based on "AM" and "PM"
$morning = array_filter($options['times'], function($time) {
    return strpos($time, 'AM') !== false;
});
$afternoon = array_filter($options['times'], function($time) {
    return strpos($time, 'PM') !== false;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment</title>
    <link rel="stylesheet" href="styles/form_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Function to handle button selection
        function handleButtonClick(buttonGroupClass, event) {
            const buttons = document.querySelectorAll(`.${buttonGroupClass} button`);
            buttons.forEach(button => button.classList.remove('selected')); // Remove 'selected' class from all buttons
            event.target.classList.add('selected'); // Add 'selected' class to the clicked button
        }

        // Function to handle stylist selection
        function handleStylistClick(event) {
            const stylists = document.querySelectorAll('.stylists img');
            stylists.forEach(stylist => stylist.classList.remove('selected')); // Remove 'selected' class from all stylists
            event.target.classList.add('selected'); // Add 'selected' class to the clicked stylist
        }
        // Gather stylist data
        function selectStylist(name) {
            document.getElementById('stylist').value = name;
        }
        function setTime(time) {
            document.getElementById('selected_time').value = time;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr("#selected_date", {
                dateFormat: "m/d/Y", // Format to match your database
                minDate: "today", // Prevent selecting past dates
                defaultDate: null,
                disableMobile: true, // Force the custom calendar on mobile devices
                locale: {
                    firstDayOfWeek: 1 // Start the week on Monday
                }
            });
        });
    </script>
</head>
<body>
    <form action="form/formhandler.php" method="POST">
        <div class="header">
            <img src="img/logo.png" alt="Logo">
            <h1>Arman Salon Appointments</h1>
        </div>
        <div class="container">
            <div class="form-header">
                Fill the Form to set your Appointment
            </div>
            <p>
                <?php signup_errors(); ?>
            </p>
            <div class="form-section">
                <div class="calendar">
                    <h3>Select Date</h3>
                    <div class="date-container">
                        <input type="text" name="selected_date" id="selected_date" placeholder="mm/dd/yyyy">
                        <span class="calendar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="time-section">
                    <h3>Morning</h3>
                    <div class="time-buttons" onclick="handleButtonClick('time-buttons', event)">
                        <?php foreach ($morning as $time): ?>
                            <button type="button" onclick="setTime('<?php echo htmlspecialchars($time); ?>')"><?php echo htmlspecialchars($time); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <h3>Afternoon</h3>
                    <div class="time-buttons" onclick="handleButtonClick('time-buttons', event)">
                        <?php foreach ($afternoon as $time): ?>
                            <button type="button" onclick="setTime('<?php echo htmlspecialchars($time); ?>')"><?php echo htmlspecialchars($time); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="selected_time" id="selected_time">
                </div>
            </div>
            <div class="form-section">
                <div class="stylists">
                    <h3>Choose Hair Stylist</h3>
                    <input type="hidden" name="stylist" id="stylist">
                    <?php foreach ($options['hairstylists'] as $stylist): ?>
                        <div class="stylist">
                            <img src="img/stylist_default.png" alt="<?php echo htmlspecialchars($stylist); ?>" onclick="handleStylistClick(event); selectStylist('<?php echo addslashes(htmlspecialchars($stylist)); ?>')">
                            <p><?php echo htmlspecialchars($stylist); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="service">
                    <h3>Choose Service</h3>
                    <select name="selected_service">
                        <?php foreach ($options['services'] as $service): ?>
                            <option value="<?php echo htmlspecialchars($service); ?>"><?php echo htmlspecialchars($service); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- New User Inputs -->
            <div class="user-info">
                <h3>User Information</h3>
                <div class="user-inputs">
                    <input type="text" name="username" placeholder="Full Name" required>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="tel" name="phoneNum" placeholder="Phone Number" pattern="\d{11}" maxlength="11" title="Phone number must be exactly 11 digits" required>
                    <select name="gender" required>
                        <option value="" disabled selected>Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                    </select>
                </div>
            </div>
            <div class="book-button">
                <button type="submit">Book Appointment</button>
            </div>
            <!-- Back to Index Button -->
            <div class="back-button">
                <a href="index.php" class="back-link">Back to Home</a>
            </div>
        </div>
    </form>
    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const email = document.querySelector('input[name="email"]').value.trim();
            const phone = document.querySelector('input[name="phoneNum"]').value.trim();

            // Strict email pattern: something@something.something (e.g., user@gmail.com)
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
            // Phone: exactly 11 digits
            const phoneRegex = /^\d{11}$/;

            if (!emailRegex.test(email)) {
                alert("Please enter a valid email address (e.g., user@gmail.com).");
                e.preventDefault();
            }

            if (!phoneRegex.test(phone)) {
                alert("Phone number must be exactly 11 digits.");
                e.preventDefault();
            }
        });
    </script>
</body>
</html>