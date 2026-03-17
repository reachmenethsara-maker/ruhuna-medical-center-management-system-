<?php
session_start();
include('../db.php'); // Database connection

if(isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // --- Fixed Admin Login ---
    if($username === "admin" && $password === "admin123") {
        $_SESSION['user_id'] = 0;          // Admin special ID
        $_SESSION['user_name'] = "Admin";
        $_SESSION['role_id'] = 1;          // Admin role
        header("Location: /Mini_project/Admin_Panel/admindash.php");
        exit();
    }

    // --- Normal Users (Doctor / Patient / Staff / Inventory) ---
    $stmt = $conn->prepare("SELECT user_id, user_name, password, role_id FROM user WHERE user_name = ?");
    if(!$stmt){
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user){
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['role_id'] = $user['role_id'];

            // Redirect based on role
            switch($user['role_id']){
                case 2: // Doctor
                   
                    header("Location: /Mini_project/Doctor_Panel/doctordash.php");

                    break;
                case 4: // Patient
                    header("Location: /Mini_project/Patient_Panel/patientdash.php");
                    break;
                case 3: // Staff
                    header("Location: /Mini_project/Staff_Panel/staffdash.php");
                    break;
                case 5: // Pharmacist
                header("Location: /Mini_project/Inventorynew/inventorydash.php");
                 break;
                default:
                    echo "Unknown role. Contact admin.";
                    exit();
            }
            exit();
        } else {
            // Wrong password
            echo "Username or Password incorrect! <a href='/Mini_project/loginpage/loginpage.php'>Back to login</a>";
            exit();
        }
    } else {
        // Username not found
        echo "Username or Password incorrect! <a href='/Mini_project/loginpage/loginpage.php'>Back to login</a>";
        exit();
    }

} else {
    // Direct access without POST
    header("Location: /Mini_project/loginpage/loginpage.php");
    exit();
}
?>