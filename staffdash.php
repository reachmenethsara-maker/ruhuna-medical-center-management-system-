<?php
session_start();
include('../db.php');

// Staff login check
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../loginpage/loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================
   Check Staff Profile
========================= */

$doc = $conn->prepare("SELECT * FROM user WHERE user_id=?");
$doc->bind_param("i", $user_id);
$doc->execute();
$result = $doc->get_result();
$row = $result->fetch_assoc();

$show_popup = false;

// Check if any important fields are empty
if (
    empty($row['email']) &&
    empty($row['first_name']) &&
    empty($row['last_name']) &&
    empty($row['gender']) &&
    empty($row['NIC']) &&
    empty($row['DOB']) &&
    empty($row['address']) &&
    empty($row['age']) &&
    empty($row['contact_num'])
) {
    $show_popup = true;
}

// Fetch staff name
$stmt = $conn->prepare("SELECT staff_name FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

$staff_name = $staff['staff_name'] ?? $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard</title>

<link rel="stylesheet" href="staffpanel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main">
<?php include('topbar.php'); ?>

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
        <h2>Pharmacist</h2>
        <p>Manage Pharmacist</p>
    </a>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($show_popup) { ?>
<script>
window.onload = function () {
    var myModal = new bootstrap.Modal(document.getElementById('profileModal'));
    myModal.show();
};
</script>
<?php } ?>
</body>
</html>