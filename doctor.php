<?php
session_start();

if (!isset($_SESSION['doctor'])) {
    header("Location: doctor_login.php");
    exit();
}
?>
