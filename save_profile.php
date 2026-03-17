<?php
session_start();
include("../db.php");

$user_id = $_SESSION['user_id'];

$email = $_POST['email'];
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$gender = $_POST['gender'];
$nic = $_POST['nic'];
$dob = $_POST['DOB'];
$address = $_POST['addres'];
$age = $_POST['age'];
$contact = $_POST['contact_num'];
$page_name = $_POST['page_name'];

$stmt = $conn->prepare("UPDATE user SET email=?,first_name=?,last_name=?,gender=?,NIC=?,DOB=?,address=?,age=?,contact_num=? WHERE user_id=?");

$stmt->bind_param("sssssssssi", $email, $first, $last, $gender, $nic, $dob, $address, $age, $contact, $user_id);

$stmt->execute();

if ($page_name == 'doctordash') {

    header("Location: doctordash.php");
} elseif ($page_name == 'patientdash') {
    header("Location: patientdash.php");
}  elseif ($page_name == 'staffdash') {
    header("Location: staffdash.php");
}
?>