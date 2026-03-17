<?php
include 'db.php'; // Your database connection

// -------------------- Delete Doctor --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM doctor WHERE doctor_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Update Doctor --------------------
if(isset($_POST['update'])){
    $id             = $_POST['doctor_id'];
    $Doctor_name    = $_POST['Doctor_name'];
    $available_date = $_POST['available_date'];
    $start_date     = $_POST['start_date'];
    $end_date       = $_POST['end_date'];
    $speciality     = $_POST['speciality'];
    $availability_id= $_POST['availability_id'];
    $status         = $_POST['status'];
    $updated_by     = $_POST['updated_by'];
    $updated_date   = $_POST['updated_date'];

    $sql = "UPDATE doctor SET
            Doctor_name='$Doctor_name',
            available_date='$available_date',
            start_date='$start_date',
            end_date='$end_date',
            speciality='$speciality',
            availability_id='$availability_id',
            status='$status',
            updated_by='$updated_by',
            updated_date='$updated_date'
            WHERE doctor_id='$id'";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Add Doctor --------------------
if(isset($_POST['save'])){
    $Doctor_name    = $_POST['Doctor_name'];
    $available_date = $_POST['available_date'];
    $start_date     = $_POST['start_date'];
    $end_date       = $_POST['end_date'];
    $speciality     = $_POST['speciality'];
    $availability_id= $_POST['availability_id'];
    $status         = $_POST['status'];
    $updated_by     = $_POST['updated_by'];
    $updated_date   = $_POST['updated_date'];

    $sql = "INSERT INTO doctor
            (Doctor_name,available_date,start_date,end_date,speciality,availability_id,status,updated_by,updated_date)
            VALUES
            ('$Doctor_name','$available_date','$start_date','$end_date','$speciality','$availability_id','$status','$updated_by','$updated_date')";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Search --------------------
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT * FROM doctor 
              WHERE Doctor_name LIKE '%$search%' 
              ORDER BY doctor_id DESC";
} else {
    $query = "SELECT * FROM doctor ORDER BY doctor_id DESC";
}

$result = mysqli_query($conn, $query);

// -------------------- Fetch single doctor for edit --------------------
$edit_doctor = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = mysqli_query($conn,"SELECT * FROM doctor WHERE doctor_id='$id'");
    $edit_doctor = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Management</title>
    <style>
        body { font-family: Arial,sans-serif; background:#f4f6f9; margin:0; padding:0;}
        .container { width:95%; max-width:1200px; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1);}
        h2 { text-align:center; color:#0d6efd;}
        form input, form select, form textarea { padding:8px; margin:5px 0; width:100%; border-radius:5px; border:1px solid #ccc; }
        form button { padding:8px 15px; background:#0d6efd; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        form button:hover { background:#0b5ed7; }
        .top-bar { display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:15px; }
        .top-bar input[type=text]{ width:300px; padding:6px; }
        #addForm { display:none; margin-top:20px; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table, th, td { border:1px solid #ccc; }
        th { background:#0d6efd; color:#fff; padding:10px; }
        td { padding:10px; }
        tr:nth-child(even){ background:#f2f2f2; }
    </style>
    <script>
        function toggleAddForm(){
            var form = document.getElementById('addForm');
            form.style.display = (form.style.display==='none') ? 'block':'none';
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Doctor Management</h2>

    <!-- Top Bar: Search + Add -->
    <div class="top-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <button onclick="toggleAddForm()">+ Add Doctor</button>
    </div>

    <!-- Add/Edit Form -->
    <div id="addForm" <?php if($edit_doctor) echo 'style="display:block;"'; ?>>
        <form method="POST">
            <input type="hidden" name="doctor_id" value="<?php echo $edit_doctor['doctor_id'] ?? ''; ?>">
            <input type="text" name="Doctor_name" placeholder="Doctor Name" value="<?php echo $edit_doctor['Doctor_name'] ?? ''; ?>" required>
            <input type="date" name="available_date" placeholder="Available Date" value="<?php echo $edit_doctor['available_date'] ?? ''; ?>">
            <input type="date" name="start_date" placeholder="Start Date" value="<?php echo $edit_doctor['start_date'] ?? ''; ?>">
            <input type="date" name="end_date" placeholder="End Date" value="<?php echo $edit_doctor['end_date'] ?? ''; ?>">
            <input type="text" name="speciality" placeholder="Speciality" value="<?php echo $edit_doctor['speciality'] ?? ''; ?>">
            <input type="text" name="availability_id" placeholder="Availability ID" value="<?php echo $edit_doctor['availability_id'] ?? ''; ?>">
            <input type="text" name="status" placeholder="Status" value="<?php echo $edit_doctor['status'] ?? ''; ?>">
            <input type="number" name="updated_by" placeholder="Updated By" value="<?php echo $edit_doctor['updated_by'] ?? ''; ?>">
            <input type="date" name="updated_date" placeholder="Updated Date" value="<?php echo $edit_doctor['updated_date'] ?? ''; ?>">
            <?php if($edit_doctor): ?>
                <button type="submit" name="update">Update Doctor</button>
            <?php else: ?>
                <button type="submit" name="save">Add Doctor</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Doctors Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Available Date</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Speciality</th>
            <th>Availability ID</th>
            <th>Status</th>
            <th>Updated By</th>
            <th>Updated Date</th>
            <th>Actions</th>
        </tr>
        <?php if(mysqli_num_rows($result)>0): ?>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['doctor_id']; ?></td>
                <td><?php echo $row['Doctor_name']; ?></td>
                <td><?php echo $row['available_date']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td><?php echo $row['speciality']; ?></td>
                <td><?php echo $row['availability_id']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['updated_by']; ?></td>
                <td><?php echo $row['updated_date']; ?></td>
                <td>
                    <a href="?edit=<?php echo $row['doctor_id']; ?>">Edit</a> | 
                    <a href="?delete=<?php echo $row['doctor_id']; ?>" onclick="return confirm('Delete this doctor?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="11" style="text-align:center;">No doctors found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>