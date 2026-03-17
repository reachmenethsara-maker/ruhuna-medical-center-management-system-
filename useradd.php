<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin - Create Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f4f6f9;
}
.container {
    max-width: 700px;
    margin: 50px auto;
    background: #fff;
    padding: 30px 35px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #0d6efd;
    margin-bottom: 30px;
}
label {
    font-weight: 500;
}
.form-control {
    border-radius: 6px;
    padding: 10px 12px;
}
button {
    border-radius: 6px;
}
button[type="submit"] {
    background: #0d6efd;
    color: #fff;
    border: none;
    width: 100%;
    padding: 12px;
    font-size: 16px;
}
button[type="submit"]:hover {
    background: #0b5ed7;
}
</style>
</head>
<body>
<div class="container">
    <h2>Create Username & Password</h2>
    <form method="POST" action="adminsaveuser.php">

        <div class="mb-3">
            <label>Select User</label>
            <select name="role_user" class="form-select" required>
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
</body>
</html>