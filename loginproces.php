<?php
session_start();
include('../db.php');   // OK

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {

        $sql = "SELECT 
                    u.user_id,
                    u.user_name,
                    u.password,
                    r.role_name
                FROM user u
                INNER JOIN user_role ur ON u.user_id = ur.user_id
                INNER JOIN role r ON ur.role_id = r.role_id
                WHERE u.user_name = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['user_name'];
            $_SESSION['role']     = strtolower(trim($user['role_name']));

            // ---- ABSOLUTE PATH REDIRECT (FIX) ----
            switch ($_SESSION['role']) {

                case 'admin':
                    header("Location: ../Admin_panel/admindash.php");
                    break;

                case 'doctor':
                    header("Location: ../Doctor_Panel/doctordash.php");
                    break;

                case 'staff':
                    header("Location: ../Staff_Panel/staff_dash.html");
                    break;

                case 'patient':
                case 'patients':
                    header("Location: ../Patient_Panel/Patient_dash.php");
                    break;

                case 'pharmacist':
                    header("Location: ../inventory_dash.php");
                    break;

                default:
                    header("Location: login.php?error=role");
            }
            exit;

        } else {
            header("Location: login.php?error=invalid");
            exit;
        }

    } else {
        header("Location: login.php?error=empty");
        exit;
    }
}

header("Location: login.php");
exit;
?>