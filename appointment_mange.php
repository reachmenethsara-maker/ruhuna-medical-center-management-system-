<?php
session_start();
include('../db.php');

// Only allow patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
$staff_user_id = $_SESSION['user_id'];

// Fetch full name from patient table if exists
$stmt = $conn->prepare("SELECT staff_name FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $staff_user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$staff_name = '';

if(isset($staff) && $staff){
    $staff_name = $staff['staff_name'];
} else {
    $staff_name = $_SESSION['user_name']; 
}

// -------------------- Delete Appointment --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM appointment WHERE appointment_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Confirm Appointment --------------------
if(isset($_GET['confirm'])){
    $id = $_GET['confirm'];
    $confirmation_by = 1; // Admin/Staff ID who confirms
    $confirmation_date = date('Y-m-d H:i:s');

    mysqli_query($conn, "UPDATE appointment 
                         SET confirmation_status='Confirmed', 
                             confirmation_by='$confirmation_by', 
                             confirmation_date='$confirmation_date' 
                         WHERE appointment_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Fetch Appointments --------------------
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last,
                     d.Doctor_name AS doctor_name
              FROM appointment a
              LEFT JOIN patient p ON a.patient_id = p.patient_id
              LEFT JOIN doctor d ON a.doctor_id = d.doctor_id
              WHERE p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%'
              ORDER BY a.requested_date DESC";
} else {
    $query = "SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last,
                     d.Doctor_name AS doctor_name
              FROM appointment a
              LEFT JOIN patient p ON a.patient_id = p.patient_id
              LEFT JOIN doctor d ON a.doctor_id = d.doctor_id
              ORDER BY a.requested_date DESC";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Management</title>
    
    <link rel="stylesheet" href="staffpanel.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    /* Table container scroll */
.table-container{
width:100%;
overflow-x:auto;
}

/* Table style */
.data-table{
width:100%;
min-width:1100px;
border-collapse:collapse;
font-size:14px;
}

/* Header */
.data-table th{
background:#0d6efd;
color:white;
padding:12px;
text-align:center;
}

/* Cells */
.data-table td{
padding:10px;
text-align:center;
border-bottom:1px solid #ddd;
}

/* Row hover */
.data-table tr:hover{
background:#f1f6ff;
}

/* Keep buttons same line */
.action-cell{
white-space:nowrap;
}

/* Buttons */
.action-btn{
padding:6px 12px;
border-radius:5px;
text-decoration:none;
font-size:13px;
color:white;
margin:2px;
display:inline-block;
}

/* Accept button */
.accept-btn{
background:#198754;
}

.accept-btn:hover{
background:#157347;
}

/* Delete button */
.delete-btn{
background:#dc3545;
}

.delete-btn:hover{
background:#bb2d3b;
}
</style>
 
</head>
<body>
       <?php include('sidebar.php'); ?>

<div class="main">

<?php include('topbar.php'); ?>

<div class="container mt-4">
    <h4>Appointment Management</h4>
    

    <!-- Search Form -->
    <form method="GET" class="mb-3 d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Search Patient Name..." value="<?= htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Appointments Table -->
    <div class="table-container">

<table class="data-table">

<thead>
<tr>
<th>#</th>
<th>Patient Name</th>
<th>Doctor Name</th>
<th>Requested Date</th>
<th>Preferred Time</th>
<th>Status</th>
<th>Confirmed By</th>
<th>Confirmation Date</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php if(mysqli_num_rows($result) > 0): 
$counter = 1;
while($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?= $counter++; ?></td>

<td><?= htmlspecialchars($row['patient_first'].' '.$row['patient_last']); ?></td>

<td><?= htmlspecialchars($row['doctor_name']); ?></td>

<td><?= date('d M Y', strtotime($row['requested_date'])); ?></td>

<td><?= htmlspecialchars($row['preferred_time']); ?></td>

<td><?= htmlspecialchars($row['confirmation_status']); ?></td>

<td><?= htmlspecialchars($row['confirmation_by']); ?></td>

<td><?= $row['confirmation_date'] ? date('d M Y ', strtotime($row['confirmation_date'])) : '-'; ?></td>

<td class="action-cell">

<?php if($row['confirmation_status'] !== 'Confirmed'): ?>

<a href="?confirm=<?= $row['appointment_id']; ?>" class="action-btn accept-btn">
Accept
</a>

<?php endif; ?>

<a href="?delete=<?= $row['appointment_id']; ?>"
onclick="return confirm('Delete this appointment?')"
class="action-btn delete-btn">
Delete
</a>

</td>

</tr>

<?php endwhile; else: ?>

<tr>
<td colspan="9" style="text-align:center;">No appointments found.</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>
</div>
</body>
</html>