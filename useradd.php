<?php
session_start();
include('../db.php');

// Ensure only Admin access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin'){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Fetch all users
$patients = $conn->query("SELECT * FROM patient ORDER BY first_name ASC");
$staffs = $conn->query("SELECT * FROM staff ORDER BY staff_name ASC");
$doctors = $conn->query("SELECT * FROM doctor ORDER BY Doctor_name ASC");
$inventories = $conn->query("SELECT * FROM inventory ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin - Create Users</title>
<style>
/* Keep your CSS here */
</style>
</head>
<body>
<div class="container">
<h2>Create Username & Password</h2>
<form method="POST" action="saveuser.php">

<label>Select User:</label>
<select name="role_user" required>
    <option value="">-- Select User --</option>

    <optgroup label="Patients">
        <?php while($row = $patients->fetch_assoc()): ?>
        <option value="patient_<?= $row['patient_id'] ?>">
            <?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>
        </option>
        <?php endwhile; ?>
    </optgroup>

    <optgroup label="Staff">
        <?php while($row = $staffs->fetch_assoc()): ?>
        <option value="staff_<?= $row['staff_id'] ?>">
            <?= htmlspecialchars($row['staff_name']) ?>
        </option>
        <?php endwhile; ?>
    </optgroup>

    <optgroup label="Doctors">
        <?php while($row = $doctors->fetch_assoc()): ?>
        <option value="doctor_<?= $row['doctor_id'] ?>">
            <?= htmlspecialchars($row['Doctor_name']) ?>
        </option>
        <?php endwhile; ?>
    </optgroup>

    <optgroup label="Inventory">
        <?php while($row = $inventories->fetch_assoc()): ?>
        <option value="inventory_<?= $row['prescription_id'] ?>">
            <?= htmlspecialchars($row['name']) ?>
        </option>
        <?php endwhile; ?>
    </optgroup>

</select>

<label>Username:</label>
<input type="text" name="username" placeholder="Enter username" required>

<label>Password:</label>
<input type="password" name="password" placeholder="Enter password" required>

<button type="submit" name="create_user">Create User</button>
</form>
</div>
</body>
</html>