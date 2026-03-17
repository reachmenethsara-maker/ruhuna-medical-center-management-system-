<?php
// Get current page file name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="logo">
        <img src="mc.png" alt="Hospital Logo">
        <h2>ADMIN</h2>
    </div>
    <ul>
        <li><a href="admindash.php" class="<?= $current_page=='admindash.php'?'active':'' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="mangepatient.php" class="<?= $current_page=='mangepatient.php'?'active':'' ?>"><i class="fa-solid fa-user"></i> Patient Manage</a></li>
        <li><a href="doctor_mange.php" class="<?= $current_page=='doctor_mange.php'?'active':'' ?>"><i class="fa-solid fa-user-doctor"></i> Doctor Manage</a></li>
        <li><a href="staffmange.php" class="<?= $current_page=='staffmange.php'?'active':'' ?>"><i class="fa-solid fa-user-tie"></i> Staff Manage</a></li>
        <li><a href="doctor_availability.php" class="<?= $current_page=='doctor_availability.php'?'active':'' ?>"><i class="fa-solid fa-clock"></i> Doctor Availability</a></li>
        <li><a href="appointment_mange.php" class="<?= $current_page=='appointment_mange.php'?'active':'' ?>"><i class="fa-solid fa-calendar-check"></i> Appointment Manage</a></li>
        <li><a href="add_pharmacist.php" class="<?= $current_page=='add_pharmacist.php'?'active':'' ?>"><i class="fa-solid fa-pills"></i> Pharmacist Manage</a></li>
        <li ><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>