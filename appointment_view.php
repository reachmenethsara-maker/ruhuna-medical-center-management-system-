<?php
session_start();
include "../db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4){
header("Location: ../loginpage/loginpage.php");
exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT patient_id,first_name,last_name FROM patient WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result=$stmt->get_result();
$row=$result->fetch_assoc();

$patient_name=$row['first_name']." ".$row['last_name'];

// get patient_id
$stmt = $conn->prepare("SELECT patient_id FROM patient WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$patient_id = $row['patient_id'];

// get appointments
$sql = "SELECT a.unique_num,a.requested_date,a.preferred_time,
        a.confirmation_status,a.confirmation_date,
        d.Doctor_name,d.speciality
        FROM appointment a
        JOIN doctor d ON a.doctor_id=d.doctor_id
        WHERE a.patient_id=?
        ORDER BY a.requested_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>

<title>My Appointments</title>

<link rel="stylesheet" href="patient_style.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

body{
background:#f4f7fb;
font-family:Arial;
}

.header{
background:linear-gradient(90deg,#1e3c72,#2a5298);
color:white;
padding:20px;
text-align:center;
font-size:26px;
font-weight:bold;
}

.card{
border:none;
border-radius:15px;
box-shadow:0 4px 15px rgba(0,0,0,0.1);
}

.table thead{
background:#1e3c72;
color:white;
}

.table td{
vertical-align:middle;
}

.badge-pending{
background:#ffc107;
color:black;
padding:6px 12px;
border-radius:20px;
}

.badge-confirm{
background:#28a745;
padding:6px 12px;
border-radius:20px;
}

.ref{
font-weight:bold;
color:#2a5298;
}

.no-data{
text-align:center;
font-size:18px;
color:gray;
padding:30px;
}

</style>

</head>

<body>
    <!-- SIDEBAR -->
<?php include("sidebar.php"); ?>

<div class="main">

<!-- TOPBAR -->
<?php include("topbar.php"); ?>

<!-- CONTENT AREA -->

<div class="container-fluid p-4">





<div class="container mt-4">

<div class="card p-4">

<h4 class="mb-3">
<i class="fa-solid fa-list"></i> Appointment History
</h4>

<div class="table-responsive">

<table class="table table-hover table-bordered">

<thead>
<tr>
<th>Reference</th>
<th>Doctor</th>
<th>Speciality</th>
<th>Date</th>
<th>Time</th>
<th>Status</th>
<th>Confirmation Date</th>
</tr>
</thead>

<tbody>

<?php

if($result->num_rows==0){
echo "<tr><td colspan='7' class='no-data'>No appointments found</td></tr>";
}

while($row=$result->fetch_assoc()){

?>

<tr>

<td class="ref">
<i class="fa-solid fa-hashtag"></i>
<?= $row['unique_num'] ?>
</td>

<td>
<i class="fa-solid fa-user-doctor"></i>
<?= htmlspecialchars($row['Doctor_name']) ?>
</td>

<td>
<?= htmlspecialchars($row['speciality']) ?>
</td>

<td>
<i class="fa-solid fa-calendar"></i>
<?= $row['requested_date'] ?>
</td>

<td>
<i class="fa-solid fa-clock"></i>
<?= $row['preferred_time'] ?>
</td>

<td>

<?php

if($row['confirmation_status']=="Pending"){
echo "<span class='badge-pending'>Pending</span>";
}
else{
echo "<span class='badge-confirm'>Confirmed</span>";
}

?>

</td>

<td>
<?= $row['confirmation_date'] ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>