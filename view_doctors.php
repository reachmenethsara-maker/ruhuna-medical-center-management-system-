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

$doctor = mysqli_query($conn,"SELECT doctor_id, Doctor_name, speciality, available_date, start_time, end_time, status FROM doctor

ORDER BY Doctor_name ASC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Doctor Availability</title>

<link rel="stylesheet" href="patient_style.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

.page-title{
font-size:22px;
font-weight:600;
margin-bottom:20px;
}

.doctor-card{
background:white;
border-radius:12px;
padding:20px;
box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.table thead{
background:#1e3a8a;
color:white;
}

.table td{
vertical-align:middle;
}

.badge-special{
background:#0d6efd;
}

</style>

</head>

<body>

<?php include("sidebar.php"); ?>

<div class="main">

<?php include("topbar.php"); ?>

<div class="container p-4">

<div class="doctor-card">

<div class="page-title">
<i class="fa-solid fa-user-doctor"></i> Available Doctors
</div>

<div class="table-responsive">

<table class="table table-hover">

<thead>
<tr>
<th>Doctor</th>
<th>Speciality</th>
<th>Date</th>
<th>Start Time</th>
<th>End Time</th>
<th>status</th>
</tr>
</thead>

<tbody>

<?php
if(mysqli_num_rows($doctor)>0){

while($doc=mysqli_fetch_assoc($doctor)){
?>

<tr>

<td>
<i class="fa-solid fa-user-doctor text-primary"></i>
Dr. <?= htmlspecialchars($doc['Doctor_name']) ?>
</td>

<td>
<span class="badge badge-special">
<?= htmlspecialchars($doc['speciality']) ?>
</span>
</td>

<td><?= htmlspecialchars($doc['available_date']) ?></td>

<td><?= htmlspecialchars($doc['start_time']) ?></td>

<td><?= htmlspecialchars($doc['end_time']) ?></td>
<td>
<?php 
if(isset($doc['status'])){
    if($doc['status'] == 'Active'){
        echo '<span class="badge bg-success">Active</span>';
    } else {
        echo '<span class="badge bg-danger">Inactive</span>';
    }
} else {
    echo '<span class="badge bg-secondary">Unknown</span>';
}
?>
</td>

</tr>

<?php
}

}else{
?>

<tr>
<td colspan="5" class="text-center text-muted">
No Doctors Available
</td>
</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

</body>
</html>