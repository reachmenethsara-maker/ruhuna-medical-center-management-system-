<?php
session_start();
include('../db.php');

// Only allow patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
session_start();

// සියලු session variables remove කරන්න
session_unset();

// Session destroy කරන්න
session_destroy();

// Login page එකට redirect කරන්න
header("Location: ../loginpage/loginpage.php");
exit();
?>