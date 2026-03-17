<?php
session_start();

// Admin access check
if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1){
    header("Location: /Mini_project/loginpage/loginpage.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="admindash.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<?php include("sidebar.php"); ?>

<div class="main">

    <?php include("topbar.php"); ?>

    <!-- Dashboard Cards -->
    <div class="cards">

        <a href="mangepatient.php" class="card blue">
            <i class="fas fa-users"></i>
            <h2>Patients</h2>
            <p>Manage Patients</p>
        </a>

        <a href="doctor_mange.php" class="card green">
            <i class="fas fa-user-doctor"></i>
            <h2>Doctors</h2>
            <p>Manage Doctors</p>
        </a>

        <a href="appointment_mange.php" class="card orange">
            <i class="fas fa-calendar-check"></i>
            <h2>Appointments</h2>
            <p>Manage Appointments</p>
        </a>

        <a href="add_pharmacist.php" class="card red">
            <i class="fas fa-pills"></i>
            <h2>Inventory</h2>
            <p>Manage Stock</p>
        </a>

    </div>

</div>

</body>
</html>