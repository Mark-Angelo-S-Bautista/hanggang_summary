<?php

function is_fields_empty($selected_date, $selected_time, $selected_time_12hr, $stylist, $selected_service, $username, $email, $phoneNum, $gender){
    if(empty($selected_date) || empty($selected_time) || empty($selected_time_12hr) || empty($stylist) || empty($selected_service)
        || empty($username) || empty($email) || empty($phoneNum) || empty($gender)){
            return true;
    }
    else{
        return false;
    }
}

function create_user($pdo, $selected_date, $selected_time_12hr, $stylist, $selected_service, $username, $email, $phoneNum, $gender){
    if(user_created($pdo, $selected_date, $selected_time_12hr, $stylist, $selected_service, $username, $email, $phoneNum, $gender)){
        return true;
    }
    else{
        return false;
    }
}

    
    
    
    
    
    
    
    
    