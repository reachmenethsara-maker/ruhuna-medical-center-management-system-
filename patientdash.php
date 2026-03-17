<?php
session_start();
include("../db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn,"SELECT * FROM user WHERE user_id='$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
<title>Patient Dashboard</title>

<link rel="stylesheet" href="patient_style.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

    <div class="logo">
        <img src="mc.png">
        <h3>Patient Panel</h3>
    </div>

    <ul>

        <li class="active">
            <a href="#"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        </li>

        <li>
            <a href="view_doctors.php">
            <i class="fa-solid fa-user-doctor"></i> Doctor Availability
            </a>
        </li>

        <li>
            <a href="book_appointment.php">
            <i class="fa-solid fa-calendar-plus"></i> Book Appointment
            </a>
        </li>

        <li>
            <a href="appointment_manage.php">
            <i class="fa-solid fa-calendar-check"></i> My Appointments
            </a>
        </li>

        <li>
            <a href="profile.php">
            <i class="fa-solid fa-user"></i> My Profile
            </a>
        </li>

        <li>
            <a href="change_password.php">
            <i class="fa-solid fa-key"></i> Change Password
            </a>
        </li>

        <li class="logout">
            <a href="logout.php">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>

    </ul>

</div>


<!-- MAIN AREA -->

<div class="main">

    <!-- TOPBAR -->

    <div class="topbar">

        <h2>Patient Dashboard</h2>

        <div class="user">
            Welcome, <?php echo $user['user_name']; ?>
        </div>

    </div>


    <!-- CONTENT -->

    <div class="content">

        <h3>Welcome to Patient Dashboard</h3>

        <p>Select an option below.</p>


        <!-- DASHBOARD CARDS -->

        <div class="cards">

            <a href="book_appointment.php" class="card">
                <i class="fas fa-calendar-plus"></i>
                <h3>Book Appointment</h3>
                <p>Schedule doctor visit</p>
            </a>

            <a href="view_doctors.php" class="card">
                <i class="fas fa-user-doctor"></i>
                <h3>Doctor Availability</h3>
                <p>View doctor schedules</p>
            </a>

            <a href="appointment_manage.php" class="card">
                <i class="fas fa-calendar-check"></i>
                <h3>My Appointments</h3>
                <p>Manage your bookings</p>
            </a>

            <a href="profile.php" class="card">
                <i class="fas fa-user"></i>
                <h3>My Profile</h3>
                <p>View profile details</p>
            </a>

        </div>

    </div>

</div>

</body>
</html>