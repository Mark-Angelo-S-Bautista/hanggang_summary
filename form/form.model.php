<?php

function user_created($pdo, $selected_date, $selected_time_12hr, $stylist, $selected_service, $username, $email, $phoneNum, $gender){
    $query = "INSERT INTO form_info (selected_date, selected_time, stylist, selected_service, username, email, phoneNum, gender) 
                VALUES (:selected_date, :selected_time, :stylist, :selected_service, :username, :email, :phoneNum, :gender);";
    $stmt = $pdo->prepare($query);

    $stmt -> bindParam(":selected_date", $selected_date);
    $stmt -> bindParam(":selected_time", $selected_time_12hr);
    $stmt -> bindParam(":stylist", $stylist);
    $stmt -> bindParam(":selected_service", $selected_service);
    $stmt -> bindParam(":username", $username);
    $stmt -> bindParam(":email", $email);
    $stmt -> bindParam(":phoneNum", $phoneNum);
    $stmt -> bindParam(":gender", $gender);

    $stmt -> execute();

    $pdo = null;
    $stmt = null;
}