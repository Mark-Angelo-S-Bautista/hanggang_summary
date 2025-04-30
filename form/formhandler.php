<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require "../database.php";

    // Retrieve the date from the form submission
    $selected_date = $_POST["selected_date"];
    $selected_time = $_POST["selected_time"];
    $selected_time_12hr = date("h:i A", strtotime($selected_time));
    $stylist = $_POST["stylist"];
    $selected_service = $_POST["selected_service"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phoneNum = $_POST["phoneNum"];
    $gender = $_POST["gender"];

    try {

        require "../database.php";
        require "form.model.php";
        require "form.contrlr.php";

        $errors = [];

        //check kung may error tas lagay sa array
        if(is_fields_empty($selected_date, $selected_time, $selected_time_12hr, $stylist, $selected_service, $username, $email, $phoneNum, $gender)){
            $errors["fields_empty"] = "Select All Options and Fill all User Info";
        }

        require "../config.session.php";

        if($errors){
            $_SESSION["signup_errors"] = $errors;

            header("Location: ../form.php");
            die();
        }  

        create_user($pdo, $selected_date, $selected_time_12hr, $stylist, $selected_service, $username, $email, $phoneNum, $gender);
        $pdo = null;
        $stmt = null;

    } catch (PDOException $e) {
        die("FORMHANDLER FAILED: " . $e -> getMessage());
    }


// Save user data into session
$_SESSION['appointment'] = [
    'selected_date' => $selected_date,
    'selected_time' => $selected_time_12hr,
    'stylist' => $stylist,
    'selected_service' => $selected_service,
    'username' => $username,
    'email' => $email,
    'phoneNum' => $phoneNum,
    'gender' => $gender
];


    // Redirect to summary.php
    header("Location: ../summary.php");
    die();
}