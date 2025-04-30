<?php
function signup_errors(){
    if(isset($_SESSION["signup_errors"])){
        $errors = $_SESSION["signup_errors"];

        foreach($errors as $error){
            echo $error . "<br>";
        }
        unset($_SESSION["signup_errors"]);
    }
}