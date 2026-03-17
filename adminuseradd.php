<?php
session_start();
include('../db.php');

// Only allow patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
$staff_user_id = $_SESSION['user_id'];

// Fetch all users from role tables
$patients   = $conn->query("SELECT * FROM patient ORDER BY first_name ASC");
$staffs     = $conn->query("SELECT * FROM staff ORDER BY staff_name ASC");
$doctors    = $conn->query("SELECT * FROM doctor ORDER BY Doctor_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin - Create Users</title>
<link rel="stylesheet" href="staffpanel.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { font-family:'Poppins', sans-serif; background:#f4f6f9; }
.container { max-width:700px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 0 15px rgba(0,0,0,0.1);}
h2 { text-align:center; color:#0d6efd; margin-bottom:30px; }
label { font-weight:500; }
.form-control { border-radius:6px; padding:10px 12px; }
button[type="submit"] { border-radius:6px; background:#0d6efd; color:#fff; border:none; width:100%; padding:12px; font-size:16px; }
button[type="submit"]:hover { background:#0b5ed7; }
.option-disabled { color:#aaa; }
</style>
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="main">
<!-- <?php include("topbar.php"); ?> -->

<div class="container">
<h2>Create Username & Password</h2>
<form method="POST" action="adminsaveuser.php">

    <div class="mb-3">
        <label>Select User</label>
        <select name="role_user" class="form-select" required>
            <option value="">-- Select User --</option>

            <!-- Patients -->
            <optgroup label="Patients">
            <?php while($row = $patients->fetch_assoc()): 
                $name = $row['first_name'].' '.$row['last_name'];
                $disabled = $row['user_id'] ? 'disabled' : '';
                $status_text = $row['user_id'] ? '(Account Created)' : '(Create Account)';
            ?>
            <option value="patient_<?= $row['patient_id'] ?>" <?= $disabled; ?>>
                <?= htmlspecialchars($name).' '.$status_text ?>
            </option>
            <?php endwhile; ?>
            </optgroup>

            <!-- Staff -->
            <optgroup label="Staff">
            <?php while($row = $staffs->fetch_assoc()): 
                $name = $row['staff_name'];
                $disabled = $row['user_id'] ? 'disabled' : '';
                $status_text = $row['user_id'] ? '(Account Created)' : '(Create Account)';
            ?>
            <option value="staff_<?= $row['staff_id'] ?>" <?= $disabled; ?>>
                <?= htmlspecialchars($name).' '.$status_text ?>
            </option>
            <?php endwhile; ?>
            </optgroup>

            <!-- Doctors -->
            <optgroup label="Doctors">
            <?php while($row = $doctors->fetch_assoc()): 
                $name = $row['Doctor_name'];
                $disabled = $row['user_id'] ? 'disabled' : '';
                $status_text = $row['user_id'] ? '(Account Created)' : '(Create Account)';
            ?>
            <option value="doctor_<?= $row['doctor_id'] ?>" <?= $disabled; ?>>
                <?= htmlspecialchars($name).' '.$status_text ?>
            </option>
            <?php endwhile; ?>
            </optgroup>

        </select>
    </div>

    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
    </div>

    <button type="submit" name="create_user">Create User</button>
</form>
</div>
</div>
</body>
</html>