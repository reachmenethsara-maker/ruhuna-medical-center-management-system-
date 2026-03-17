<?php
session_start();
include('../db.php');

// Only allow patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
session_start();

session_unset();


session_destroy();


header("Location: ../loginpage/loginpage.php");
exit();
?>