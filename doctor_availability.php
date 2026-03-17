<?php
session_start();
include("../db.php");

// UPDATE DOCTOR AVAILABILITY
if(isset($_POST['update'])){

    $doctor_id = $_POST['doctor_id'];
    $available_date = $_POST['available_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];

    $sql = "UPDATE doctor SET 
            available_date=?,
            start_time=?,
            end_time=?,
            status=?
            WHERE doctor_id=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi",$available_date,$start_time,$end_time,$status,$doctor_id);
    $stmt->execute();

    echo "<script>alert('Doctor availability updated');window.location='doctor_availability.php';</script>";
}

// GET DOCTOR LIST
$doctors = mysqli_query($conn,"SELECT * FROM doctor ORDER BY Doctor_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="admindash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Doctor Availability</title>

<style>
body{
font-family: Arial,sans-serif;
background:#f4f6f9;
margin:0;
padding:0;
}

.container{
width:95%;
max-width:1200px;
margin:20px auto;
background:#fff;
padding:20px;
border-radius:10px;
box-shadow:0 0 12px rgba(0,0,0,0.1);
}

h2{
text-align:center;
color:#0d6efd;
margin-bottom:20px;
}

table{
width:100%;
border-collapse:collapse;
overflow-x:auto;
}

table th, table td{
text-align:center;
padding:12px;
border-bottom:1px solid #ddd;
font-size:14px;
white-space:nowrap;
}

table th{
background:#0d6efd;
color:white;
font-weight:500;
}

table tr:nth-child(even){
background:#f8f9fa;
}

table tr:hover{
background:#e2e6ea;
}

form input,
form select{
padding:8px;
width:100%;
border-radius:5px;
border:1px solid #ccc;
}

form button{
padding:8px 15px;
background:#0d6efd;
color:#fff;
border:none;
border-radius:5px;
cursor:pointer;
}

form button:hover{
background:#0b5ed7;
}
/* Status dropdown button style */

.status-select{
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;
    padding:6px 12px;
    border-radius:20px;
    border:none;
    font-size:13px;
    font-weight:600;
    text-align:center;
    cursor:pointer;
    color:#fff;
}

/* Active button */

.status-active{
    background:#198754;
}

/* Inactive button */

.status-inactive{
    background:#dc3545;
}

/* Update Button Style */

.update-btn{
    background:#0d6efd;
    color:white;
    border:none;
    padding:8px 16px;
    border-radius:6px;
    font-size:13px;
    cursor:pointer;
    transition:0.3s;
}

/* Hover Effect */

.update-btn:hover{
    background:#0b5ed7;
    transform:scale(1.05);
}

/* Active Click Effect */

.update-btn:active{
    transform:scale(0.95);
}
</style>

</head>

<body>
<?php include("sidebar.php"); ?>

<div class="main">
<?php include("topbar.php"); ?>

<div class="container">

<h2>Manage Doctor Availability</h2>

<table>
<tr>
<th>Doctor Name</th>
<th>Speciality</th>
<th>Available Date</th>
<th>Start Time</th>
<th>End Time</th>
<th>Status</th>
<th>Update</th>
</tr>

<?php while($row = mysqli_fetch_assoc($doctors)) { ?>
<tr>
<form method="POST">
<td>
Dr. <?= htmlspecialchars($row['Doctor_name']); ?>
</td>

<td>
<?= htmlspecialchars($row['speciality']); ?>
</td>

<td>
<input type="date" name="available_date" value="<?= $row['available_date']; ?>">
</td>

<td>
<input type="time" name="start_time" value="<?= $row['start_time'] ?? '09:00'; ?>">
</td>

<td>
<input type="time" name="end_time" value="<?= $row['end_time'] ?? '16:00'; ?>">
</td>

<td>
<select name="status" class="status-select <?= ($row['status']=="Active") ? 'status-active' : 'status-inactive'; ?>">
<option value="Active" <?= ($row['status']=="Active") ? 'selected' : ''; ?>>Active</option>
<option value="Inactive" <?= ($row['status']=="Inactive") ? 'selected' : ''; ?>>Inactive</option>
</select>
</td>

<td>
<input type="hidden" name="doctor_id" value="<?= $row['doctor_id']; ?>">
<button type="submit" name="update" class="update-btn">Update</button>
</td>
</form>
</tr>
<?php } ?>

</table>

</div>
</div>
</body>
</html>