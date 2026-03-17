<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="mc.png" alt="Hospital Logo">
        <h2>Staff</h2>
        <div ></i></div>
    </div>
      <ul>
        <li><a href="staffdash.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="mangepatient.php"><i class="fa-solid fa-user"></i> Patient Manage</a></li>
        <li><a href="doctor_mange.php"><i class="fa-solid fa-user-doctor"></i> Doctor Manage</a></li>
        <li><a href="doctor_availability.php"><i class="fa-solid fa-clock"></i> Doctor Availability</a></li>
        <li><a href="appointment_mange.php"><i class="fa-solid fa-calendar-check"></i> Appointment Manage</a></li>
        <li><a href="staffmange.php"><i class="fa-solid fa-calendar-check"></i> Staff Manage</a></li>
        <li><a href="add_pharmacist.php"><i class="fa-solid fa-pills"></i> Pharmacist Manage</a></li>
        <li><a href="staffsettings.php"><i class="fa-solid fa-key"></i> Change Password</a></li>
        <li class="logout"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>
</div>