<?php
session_start();
include('../db.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin'){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

if(isset($_POST['create_user'])){
    $role_user = $_POST['role_user'];
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Split role_user into role type and id
    list($type, $id) = explode('_', $role_user);

    // Determine role_id
    switch($type){
        case 'doctor':
            $role_id = 2;
            $table = 'doctor';
            $user_id_field = 'doctor_id';
            break;
        case 'patient':
            $role_id = 4;
            $table = 'patient';
            $user_id_field = 'patient_id';
            break;
        case 'staff':
            $role_id = 3;
            $table = 'staff';
            $user_id_field = 'staff_id';
            break;
        case 'Pharmacist':
            $role_id = 5;
            $table = 'inventory';
            $user_id_field = 'prescription_id';
            break;
        default:
            die("Invalid role selected.");
    }

    // Insert into user table
    $stmt = $conn->prepare("INSERT INTO user (user_name, password, role_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password, $role_id);
    if($stmt->execute()){
        $user_id = $conn->insert_id;

        // Update respective table to link with user_id
        $update_stmt = $conn->prepare("UPDATE $table SET user_id=? WHERE $user_id_field=?");
        $update_stmt->bind_param("ii", $user_id, $id);
        $update_stmt->execute();

        echo "User created successfully!";
        echo "<br><a href='useradd.php'>Back</a>";
    } else {
        echo "Error: ".$conn->error;
    }
}
?>