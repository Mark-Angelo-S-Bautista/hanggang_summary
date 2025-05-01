<?php
require "form/form.view.php";
require "config.session.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment</title>
    <link rel="stylesheet" href="styles/form_styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
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
        // para dun sa stylist para makuha yung data
        function selectStylist(name) {
        document.getElementById('stylist').value = name;
        }
        function setTime(time) {
            document.getElementById('selected_time').value = time;
        }
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
                    <input type="date" name="selected_date">
                </div>
                <div class="time-section">
                    <h3>Morning</h3>
                    <div class="time-buttons" onclick="handleButtonClick('time-buttons', event)">
                        <button type="button" onclick="setTime('9:00 AM')">9:00 AM</button>
                        <button type="button" onclick="setTime('10:00 AM')">10:00 AM</button>
                        <button type="button" onclick="setTime('11:00 AM')">11:00 AM</button>
                    </div>
                    <h3>Afternoon</h3>
                    <div class="time-buttons" onclick="handleButtonClick('time-buttons', event)">
                        <button type="button" onclick="setTime('1:00 PM')">1:00 PM</button>
                        <button type="button" onclick="setTime('2:00 PM')">2:00 PM</button>
                        <button type="button" onclick="setTime('3:00 PM')">3:00 PM</button>
                        <button type="button" onclick="setTime('4:00 PM')">4:00 PM</button>
                        <button type="button" onclick="setTime('5:00 PM')">5:00 PM</button>
                    </div>
                    <input type="hidden" name="selected_time" id="selected_time">
                </div>
            </div>
            <div class="form-section">
                <div class="stylists">
                    <h3>Choose Hair Stylist</h3>
                    <input type="hidden" name="stylist" id="stylist">
                    <div class="stylist">
                        <img src="img/stylist1.png" alt="Stylist 1" onclick="handleStylistClick(event); selectStylist('Jasmine')">
                        <p>Jasmine</p>
                    </div>
                    <div class="stylist">
                        <img src="img/stylist2.png" alt="Stylist 2" onclick="handleStylistClick(event); selectStylist('Joenel')">
                        <p>Joenel</p>
                    </div>
                    <div class="stylist">
                        <img src="img/stylist3.png" alt="Stylist 3" onclick="handleStylistClick(event); selectStylist('Mark')">
                        <p>Mark</p>
                    </div>
                </div>
                <div class="service">
                    <h3>Choose Service</h3>
                    <select name="selected_service">
                        <option value="Hair Spa">Hair Spa - $40</option>
                        <option value="Shampoo">Shampoo - $20</option>
                    </select>
                </div>
            </div>
            <!-- New User Inputs -->
            <div class="user-info">
                <h3>User Information</h3>
                <div class="user-inputs">
                    <input type="text" name="username" placeholder="Full Name">
                    <input type="email" name="email" placeholder="Email Address">
                    <input type="tel" name="phoneNum" placeholder="Phone Number">
                    <select name="gender">
                        <option value="" disabled selected>Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="prefer not to say">Prefer not to say</option>
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
</body>
</html>