<?php
session_start();
include('dbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {

        $sql = "SELECT u.user_id, u.user_name, u.password, r.role_name
                FROM user u
                LEFT JOIN user_role ur ON u.user_id = ur.user_id
                LEFT JOIN role r ON ur.role_id = r.role_id
                WHERE u.user_name = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['user_name'];
            $_SESSION['role']     = trim($user['role_name']);

            switch (strtolower($_SESSION['role'])) {
                case 'admin':
                    header("Location: Admin_panel/admindash.php");
                    break;

                case 'doctor':
                    header("Location: Doctor_Panel/doctordash.php");
                    break;

                case 'staff':
                    header("Location: staff_dash.php");
                    break;

                case 'patient':
                    header("Location: patient_dash.php");
                    break;

                case 'pharmacist':
                    header("Location: inventory_dash.php");
                    break;

                default:
                    header("Location: login.php?error=role");
            }
            exit;

        } else {
            header("Location: login.php?error=invalid");
            exit;
        }
    }
}
header("Location: login.php");
exit;
?>