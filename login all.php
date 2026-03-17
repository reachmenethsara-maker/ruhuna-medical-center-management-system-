<?php
session_start();
include('db.php');

if(isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL to check username and password in database
    $sql = "SELECT u.user_id, u.user_name, r.role_name 
            FROM user u
            JOIN user_role ur ON u.user_id = ur.user_id
            JOIN role r ON ur.role_id = r.role_id
            WHERE u.user_name = ? AND u.password = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user_name'];
        $_SESSION['role'] = $user['role_name'];

        // Redirect based on role
        if($user['role_name'] == 'Admin') {
            header("Location: admindash.php"); // admin panel
            exit();
        } elseif($user['role_name'] == 'Doctor') {
            header("Location: doctor_dash.php"); // doctor panel
            exit();
        } elseif($user['role_name'] == 'Staff') {
            header("Location: staff_dash.php"); // staff panel
            exit();
        } elseif($user['role_name'] == 'Patients') {
            header("Location: patient_dash.php"); // patient panel
            exit();
        } elseif($user['role_name'] == 'pharmacist') {
            header("Location: inventory_dash.php"); // inventory panel
            exit();
        } else {
            echo "Role not recognized!";
        }

    } else {
        // Invalid login
        echo "Invalid username or password!";
    }
} else {
    echo "Please enter username and password!";
}
?>
