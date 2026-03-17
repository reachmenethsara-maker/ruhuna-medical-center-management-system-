<?php
session_start();
include('../db.php'); // Database connection

// Only allow logged-in patients (role_id=2 assumed patient)
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

$message = "";
$redirect_url = "../Patient_Panel/patientdash.php";

if(isset($_POST['change_password'])){
    $user_id = $_SESSION['user_id'];
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if($new_password !== $confirm_password){
        $message = "New Password and Confirm Password do not match!";
    } else {
        $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user && password_verify($old_password, $user['password'])){
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $new_hashed, $user_id);
            if($update->execute()){
                $message = "Password changed successfully! Redirecting to dashboard...";
                echo "<p style='color:green'>{$message}</p>";
                echo "<script>
                        setTimeout(function(){ window.location.href = '$redirect_url'; }, 3000);
                      </script>";
                exit();
            } else {
                $message = "Database update error!";
            }
        } else {
            $message = "Old password is incorrect!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* Sidebar */
.sidebar {
    width: 220px;
    height: 100vh;
    position: fixed;
    top:0; left:0;
    background:#00008B;
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:20px 0;
}
.sidebar .logo { text-align:center; margin-bottom:20px; }
.sidebar .logo img { width:70px; height:70px; border-radius:50%; border:2px solid #fff; margin-bottom:10px; }
.sidebar .logo h2 { font-size:16px; margin:0; color:#fff; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a { color:#fff; text-decoration:none; display:flex; align-items:center; padding:10px 15px; border-radius:8px; transition:.3s; }
.sidebar ul li a i { margin-right:10px; font-size:18px; }
.sidebar ul li a:hover, .sidebar ul li a.active { background:#0056b3; }
.sidebar .logout a { background:#ffc107; display:flex; align-items:center; padding:10px 15px; border-radius:8px; color:#000; }
.sidebar .logout a:hover { background:#e0a800; }

/* Main content */
.main { margin-left:240px; padding:30px; background:#f4f6f9; min-height:100vh; }
.main h2 { margin-bottom:25px; }

/* Form container */
.container-card {
    max-width:500px;
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}
.container-card h3 { margin-bottom:20px; text-align:center; color:#2a5298; }

/* Input fields */
input { border-radius:6px; }
button { border-radius:6px; }
.message { color:green; margin-bottom:15px; text-align:center; }
.error { color:red; margin-bottom:15px; text-align:center; }

/* Responsive */
@media(max-width:768px){
    .sidebar{width:100%; height:auto; position:relative;}
    .main{margin-left:0;padding:20px;}
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="mclogo.png" alt="Logo">
        <h2>Doctor Panel</h2>
    </div>
   <ul class="menu">
        <li><a href="doctordash.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="mypatients.php"><i class="bi bi-people"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="reports.php"><i class="bi bi-file-earmark-medical"></i> Reports</a></li>
        <li><a href="prescription.php" ><i class="bi bi-box-seam"></i> Medicine Stock</a></li>
        <li><a href="settings.php"class="active"><i class="bi bi-gear"></i> Settings</a></li>
        <li><a href="profile.php"><i class="bi bi-person"></i> My Profile</a></li>
    </ul>
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<!-- Main content -->
<div class="main">
    <h2 class="mb-4"><i class="bi bi-key-fill"></i> Change Password</h2>

    <div class="container-card mx-auto p-4 shadow-sm border rounded" style="max-width:480px; background:#fff;">
        <!-- Success / Error Message -->
        <?php 
        if($message){
            $class = strpos($message, 'successfully') !== false ? 'alert alert-success text-center' : 'alert alert-danger text-center';
            echo "<div class='$class'>{$message}</div>";
        }
        ?>

        <!-- Change Password Form -->
        <h3 class="text-center mb-4" style="color:#1e3a8a; font-weight:bold;">Change Password</h3>
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Old Password</label>
                <input type="password" name="old_password" class="form-control" placeholder="Enter old password" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-primary w-100 fw-bold">
                <i class="bi bi-save"></i> Change Password
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>