<?php
session_start();
include("db.php");

// Get current logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch doctor info joined with user table
$sql = "SELECT d.Doctor_name, d.speciality, u.user_name
        FROM doctor d
        INNER JOIN user u ON d.user_id = u.user_id
        WHERE u.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Assign variables to avoid undefined warnings
$Doctor_name = $doctor['Doctor_name'] ?? '';
$speciality  = $doctor['speciality'] ?? '';
$username    = $doctor['user_name'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | Doctor Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="settings.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="images-removebg-preview.png">
        <img src="mclogo.png">
    </div>
    <h4><b>DOCTOR PANEL</b></h4>

    <ul class="nav flex-column">
        <li><a href="doctordash.php"><i class="bi bi-house"></i> Dashboard</a></li>
        <li><a href="mypatients.php"><i class="bi bi-person-lines-fill"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="bi bi-calendar2-week"></i> Appointments</a></li>
        <li><a href="Reports.php"><i class="bi bi-journal-medical"></i> Reports</a></li>
        <li class="active"><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>

    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<!-- Main -->
<div class="main">
<div class="content">

<h2>SETTINGS</h2>




<!-- PASSWORD SETTINGS -->
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-lock-fill me-2"></i>Change Password</h5>
    </div>

    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" name="change_password" class="btn btn-danger">
                <i class="bi bi-key-fill me-1"></i> Change Password
            </button>
        </form>
    </div>
</div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
