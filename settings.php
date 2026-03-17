<?php
session_start();
include('../db.php'); // Database connection
include('insidebar.php'); 

$message = "";

// Only allow logged-in doctors/patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

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
                echo "<div class='message'>Password changed successfully! Redirecting...</div>";
                echo "<script>setTimeout(function(){ window.location.href='$redirect_url'; }, 2500);</script>";
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
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ---------- Layout ---------- */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
}
.main-container {
    display: flex;
    min-height: 100vh;
}
/* ---------- Sidebar ---------- */
.sidebar {
    width: 220px;
    background: #001f4d;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding-top: 20px;
    position: fixed;
    height: 100%;
}
.sidebar .logo {
    text-align: center;
    padding: 20px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}
.sidebar .logo img {
    max-width: 100px;
    margin-bottom: 10px;
}
.sidebar .logo h4 {
    margin: 0;
}
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}
.sidebar ul li a {
    display: block;
    color: #ffffffcc;
    padding: 12px 20px;
    text-decoration: none;
    border-left: 4px solid transparent;
    border-radius: 0 20px 20px 0;
    margin-bottom: 5px;
    transition: all 0.3s ease;
}
.sidebar ul li a:hover {
    background: #002766;
    color: #fff;
    border-left: 4px solid #28a745;
}
.sidebar ul li a.active {
    background: #28a745;
    color: #fff;
    border-left: 4px solid #218838;
}
.sidebar .logout a {
    display: block;
    background: #c82333;
    color: #fff;
    text-align: center;
    padding: 12px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    margin: 15px;
}
.sidebar .logout a:hover {
    background: #bd2130;
}

/* ---------- Content ---------- */
.content {
    margin-left: 220px;
    flex-grow: 1;
    padding: 40px;
}
.content header {
    font-size: 24px;
    font-weight: bold;
    color: #001f4d;
    margin-bottom: 30px;
}
.card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    max-width: 500px;
    margin: auto;
}

/* ---------- Form ---------- */
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
}
button {
    width: 100%;
    padding: 12px;
    background: #001f4d;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    background: #004080;
}
.message { color: green; font-weight: bold; text-align: center; margin-bottom: 15px; }
.error { color: red; font-weight: bold; text-align: center; margin-bottom: 15px; }
.password-wrapper { position: relative; }
.password-wrapper i {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
}
@media(max-width:768px){
    .sidebar { width: 100%; position: relative; height: auto; }
    .content { margin-left: 0; padding: 20px; }
}
</style>
</head>
<body>
<div class="main-container">
  

    <!-- Content -->
    <div class="content">
        <header>Change Password</header>
        <div class="card">
            <?php 
            if($message){
                $class = strpos($message, 'successfully') !== false ? 'message' : 'error';
                echo "<div class='$class'>{$message}</div>";
            }
            ?>
            <form action="" method="POST">
                <div class="password-wrapper">
                    <input type="password" name="old_password" placeholder="Old Password" required>
                </div>
                <div class="password-wrapper">
                    <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
                    <i class="fa fa-eye" id="toggleNew"></i>
                </div>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                    <i class="fa fa-eye" id="toggleConfirm"></i>
                </div>
                <button type="submit" name="change_password">Change Password</button>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
const toggleNew = document.getElementById('toggleNew');
const newPassword = document.getElementById('new_password');
toggleNew.addEventListener('click', ()=>{
    newPassword.type = newPassword.type === "password" ? "text" : "password";
});

const toggleConfirm = document.getElementById('toggleConfirm');
const confirmPassword = document.getElementById('confirm_password');
toggleConfirm.addEventListener('click', ()=>{
    confirmPassword.type = confirmPassword.type === "password" ? "text" : "password";
});
</script>
</body>
</html>