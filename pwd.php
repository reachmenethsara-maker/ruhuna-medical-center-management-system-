<?php
session_start();
include('../db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
 $message = ""; // <-- initialize here
$redirect_url = "#";
switch($_SESSION['role_id']){
    case 1: $redirect_url = "../Admin_Panel/admindash.php"; break;
    case 2: $redirect_url = "../Doctor_Panel/doctordash.php"; break;
    case 3: $redirect_url = "../Patient_Panel/patientdash.php"; break;
    case 4: $redirect_url = "../Staff_Panel/staffdash.php"; break;
    case 5: $redirect_url = "../Inventory_Panel/inventorydash.php"; break;
}

if(isset($_POST['change_password'])){
    $user_id = $_SESSION['user_id'];
    $old = trim($_POST['old_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    if($new !== $confirm){
        $message = "New and Confirm Password do not match!";
    } else {
        $stmt = $conn->prepare("SELECT password FROM user WHERE user_id=?");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if($user && password_verify($old,$user['password'])){
            $new_hash = password_hash($new, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE user SET password=? WHERE user_id=?");
            $update->bind_param("si",$new_hash,$user_id);
            if($update->execute()){
                echo "<p style='color:green'>Password changed successfully! Redirecting...</p>";
                echo "<script>
                        setTimeout(function(){ window.location.href = '$redirect_url'; }, 3000);
                      </script>";
                exit();
            } else {
                $message = "Database error!";
            }
        } else {
            $message = "Old password is incorrect!";
        }
    }
}
?>