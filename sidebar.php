<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

<div class="logo">
<img src="mc.png">
<h3>Patient Panel</h3>
</div>

<ul>

<li class="<?= ($current_page=='patientdash.php')?'active':'' ?>">
<a href="patientdash.php">
<i class="fa-solid fa-gauge"></i> Dashboard
</a>
</li>

<li class="<?= ($current_page=='view_doctors.php')?'active':'' ?>">
<a href="view_doctors.php">
<i class="fa-solid fa-user-doctor"></i> Doctor Availability
</a>
</li>

<li class="<?= ($current_page=='book_appointment.php')?'active':'' ?>">
<a href="book_appointment.php">
<i class="fa-solid fa-calendar-plus"></i> Book Appointment
</a>
</li>

<li class="<?= ($current_page=='appointment_view.php')?'active':'' ?>">
<a href="appointment_view.php">
<i class="fa-solid fa-calendar-check"></i> My Appointments
</a>
</li>

<li class="<?= ($current_page=='profile.php')?'active':'' ?>">
<a href="profile.php">
<i class="fa-solid fa-user"></i> My Profile
</a>
</li>

<li class="<?= ($current_page=='changepwd.php')?'active':'' ?>">
<a href="changepwd.php">
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