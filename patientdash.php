<?php
session_start();
include "../db.php";

// Only patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Initialize message
$status_msg = '';

if(isset($_GET['status'])){
    if($_GET['status'] == 'Confirmed'){
        $status_msg = "Your appointment has been Confirmed ✅";
    } elseif($_GET['status'] == 'Cancelled'){
        $status_msg = "Your appointment has been Cancelled ❌";
    }
}

// Fetch patient details
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name FROM patient WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();

$patient_name = "Guest";

if($row = $result->fetch_assoc()){
    $patient_name = $row['first_name']." ".$row['last_name'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>

<link rel="stylesheet" href="patient_style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.dashboard-card {
    border:none;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
    transition:0.3s;
}
.dashboard-card:hover { transform: translateY(-5px); }
.dashboard-card h5 { font-weight:bold; }
.dashboard-card p { font-size:0.9rem; }
</style>
</head>
<body>

<!-- Sidebar -->
<?php include("sidebar.php"); ?>

<div class="main">

<!-- Topbar -->
<?php include("topbar.php"); ?>

<div class="container-fluid p-4">
<h3 class="mb-4"><i class="fa-solid fa-gauge"></i> Patient Dashboard</h3>

<div class="row g-4">
    <div class="col-md-3">
        <a href="book_appointment.php" class="card dashboard-card text-center p-4">
            <i class="fas fa-calendar-plus fa-2x text-primary"></i>
            <h5 class="mt-3">Book Appointment</h5>
            <p class="text-muted">Schedule doctor visit</p>
        </a>
    </div>
    <div class="col-md-3">
        <a href="view_doctors.php" class="card dashboard-card text-center p-4">
            <i class="fas fa-user-doctor fa-2x text-success"></i>
            <h5 class="mt-3">Doctor Availability</h5>
            <p class="text-muted">View doctor schedules</p>
        </a>
    </div>
    <div class="col-md-3">
        <a href="appointment_view.php" class="card dashboard-card text-center p-4">
            <i class="fas fa-calendar-check fa-2x text-warning"></i>
            <h5 class="mt-3">My Appointments</h5>
            <p class="text-muted">Manage your bookings</p>
        </a>
    </div>
    <div class="col-md-3">
        <a href="profile.php" class="card dashboard-card text-center p-4">
            <i class="fas fa-user fa-2x text-info"></i>
            <h5 class="mt-3">My Profile</h5>
            <p class="text-muted">View profile details</p>
        </a>
    </div>
</div>
</div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Display popup if status exists -->
<?php if($status_msg): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    Swal.fire({
        icon: 'info',
        title: 'Appointment Status',
        text: '<?= addslashes($status_msg) ?>',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>

</body>
</html>