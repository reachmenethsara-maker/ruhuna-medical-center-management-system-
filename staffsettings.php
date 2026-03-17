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

// Initialize $message variable to avoid undefined variable warning
$message = "";

// Only allow logged-in patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Determine redirect URL after password change
$redirect_url = "../Staff_Panel/staffdash.php";

if(isset($_POST['change_password'])){
    $user_id = $_SESSION['user_id'];
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if($new_password !== $confirm_password){
        $message = "New Password and Confirm Password do not match!";
    } else {
        // Get current hashed password from database
        $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user && password_verify($old_password, $user['password'])){
            // Old password correct, update with new hashed password
            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE user SET password = ? WHERE user_id = ?");
            $update->bind_param("si", $new_hashed, $user_id);
            if($update->execute()){
                $message = "Password changed successfully! Redirecting to dashboard...";
                // JavaScript redirect after 3 seconds
                echo "<p style='color:green'>{$message}</p>";
                echo "<script>
                        setTimeout(function(){
                            window.location.href = '$redirect_url';
                        }, 3000);
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
    <link rel="stylesheet" href="staffpanel.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta charset="UTF-8">
<title>Change Password</title>
<style>
body { font-family: Arial; background:#f0f0f0; }
.container { max-width:400px; margin:50px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.2);}
input { width:100%; padding:10px; margin:10px 0; }
button { width:100%; padding:10px; background:#2a5298; color:white; border:none; cursor:pointer; }
button:hover { background:#1e3c72; }
.message { color: green; margin-bottom:10px; }
.error { color:red; margin-bottom:10px; }
</style>
</head>
<body>
       <?php include('sidebar.php'); ?>

<div class="main">

<?php include('topbar.php'); ?>
<div class="container">
    
<h2>Change Password</h2>


<!-- Display message -->
<?php 
if($message){
    $class = strpos($message, 'successfully') !== false ? 'message' : 'error';
    echo "<p class='$class'>{$message}</p>";
}
?>

<form action="" method="POST">
    <input type="password" name="old_password" placeholder="Old Password" required>
    <input type="password" name="new_password" placeholder="New Password" required>
    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
    <button type="submit" name="change_password">Change Password</button>
</form>

</div>
</body>
</html>