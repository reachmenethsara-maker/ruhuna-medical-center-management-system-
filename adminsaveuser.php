<?php
session_start();
include('../db.php');

if(isset($_POST['create_user'])){
    $role_user = $_POST['role_user'];
    $username  = trim($_POST['username']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check role_user selected
    if(empty($role_user)){
        die("Please select a user.");
    }

    // Split value
    list($type, $id) = explode('_', $role_user);

    // Set table and role_id
    switch($type){
        case 'doctor':
            $role_id = 2; $table='doctor'; $id_field='doctor_id'; break;
        case 'patient':
            $role_id = 4; $table='patient'; $id_field='patient_id'; break;
        case 'staff':
            $role_id = 3; $table='staff'; $id_field='staff_id'; break;
        default:
            die("Invalid role.");
    }

    // Check username already exists
    $check = $conn->prepare("SELECT user_id FROM user WHERE user_name=?");
    $check->bind_param("s",$username);
    $check->execute();
    $res_check = $check->get_result();
    if($res_check->num_rows > 0){
        die("<h3 style='color:red;'>Username already exists!</h3><a href='adminuseradd.php'>Back</a>");
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO user (user_name,password,role_id) VALUES (?,?,?)");
    $stmt->bind_param("ssi",$username,$password,$role_id);
    if($stmt->execute()){
        $user_id = $conn->insert_id;

        // Update role table
        $update = $conn->prepare("UPDATE $table SET user_id=? WHERE $id_field=?");
        $update->bind_param("ii",$user_id,$id);
        $update->execute();

        echo "<h3 style='color:green;'>User created successfully!</h3>";
        echo "<a href='adminuseradd.php'>Back</a>";
    } else {
        echo "Error: ".$conn->error;
    }
}
?>