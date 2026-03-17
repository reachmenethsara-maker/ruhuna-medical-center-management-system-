<?php
include 'db.php'; 

// -------------------- Delete Doctor --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM doctor WHERE doctor_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Update Doctor --------------------
if(isset($_POST['update'])){
    $id          = $_POST['doctor_id'];
    $Doctor_name = $_POST['Doctor_name'];
    $email       = $_POST['email'];
    $start_date  = $_POST['start_date'];
    $speciality  = $_POST['speciality'];
    $status      = $_POST['status'];

    // Automatically set updated_by and updated_date
    $updated_by   = $_SESSION['user_id'];
    $updated_date = date('Y-m-d');

    $sql = "UPDATE doctor SET
            Doctor_name='$Doctor_name',
            email='$email',
            start_date='$start_date',
            speciality='$speciality',
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
    $Doctor_name = $_POST['Doctor_name'];
    $email       = $_POST['email'];
    $start_date  = $_POST['start_date'];
    $speciality  = $_POST['speciality'];
    $status      = $_POST['status'];

    // Automatically set updated_by and updated_date
    $updated_by   = $_SESSION['user_id'];
    $updated_date = date('Y-m-d');

    $sql = "INSERT INTO doctor
            (Doctor_name,email,start_date,speciality,status,updated_by,updated_date)
            VALUES
            ('$Doctor_name','$email','$start_date','$speciality','$status','$updated_by','$updated_date')";
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
                 OR email LIKE '%$search%'
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
   <link rel="stylesheet" href="admindash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: Arial,sans-serif; background:#f4f6f9; margin:0; padding:0;}
        .container { width:95%; max-width:1200px; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1);}
        h2 { text-align:center; color:#0d6efd; margin-bottom:20px;}

        /* Top Bar */
        .top{
            display:flex;
            justify-content:space-between;
            flex-wrap:wrap;
            align-items:center;
            margin-bottom:20px;
            gap:10px;
        }
        .top input[type=text]{
            width:350px;
            padding:8px 12px;
            border-radius:6px;
            border:1px solid #ccc;
        }
        .top button{
            padding:8px 16px;
            border:none;
            border-radius:6px;
            background:#0d6efd;
            color:#fff;
            cursor:pointer;
        }

        /* Add/Edit Form */
        #addForm{
            display:none;
            background:#f8f9fa;
            padding:20px;
            border-radius:12px;
            margin-bottom:20px;
            box-shadow:0 3px 8px rgba(0,0,0,0.1);
        }

        .doctor-form{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
        }

        .form-group{
            display:flex;
            flex-direction:column;
        }

        .form-group label{
            font-weight:600;
            margin-bottom:5px;
        }

        .form-group input,
        .form-group select{
            padding:10px;
            border:1px solid #ccc;
            border-radius:6px;
            font-size:14px;
        }

        .add-btn{
            grid-column:span 2;
            background:#198754;
            color:white;
            padding:10px 18px;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }

        .update-btn{
            grid-column:span 2;
            background:#ffc107;
            color:black;
            padding:10px 18px;
            border:none;
            border-radius:6px;
            cursor:pointer;
        }

        .add-btn:hover{ background:#157347; }
        .update-btn:hover{ background:#e0a800; }

        /* Table */
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
            background:white;
            border-radius:8px;
            overflow:hidden;
        }
        table th{
            background:#0d6efd;
            color:white;
            padding:12px;
            font-weight:500;
        }
        table td{
            padding:10px;
            border-bottom:1px solid #eee;
        }
        table tr:hover{ background:#f1f5ff; }

        /* Table Action Buttons as Text */
        .edit-btn, .delete-btn{
            padding:6px 10px;
            border-radius:5px;
            color:white;
            text-decoration:none;
            margin-right:5px;
            font-size:13px;
        }

        .edit-btn{ background:#0d6efd; }
        .edit-btn:hover{ background:#0b5ed7; }

        .delete-btn{ background:#dc3545; }
        .delete-btn:hover{ background:#bb2d3b; }

        @media(max-width:1024px){
            .doctor-form{ grid-template-columns:1fr; }
            .top{ flex-direction:column; align-items:flex-start; }
            .top input[type=text]{width:100%;}
        }
    </style>

    <script>
        function toggleAddForm(){
            var form = document.getElementById('addForm');
            form.style.display = (form.style.display==='none') ? 'block':'none';
        }
    </script>
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="main">
    <?php include("topbar.php"); ?>

<div class="container">
<h2>Doctor Management</h2>

<!-- Top Bar -->
<div class="top">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
    <button onclick="toggleAddForm()">+ Add Doctor</button>
</div>

<!-- Add/Edit Form -->
<div id="addForm" <?php if($edit_doctor) echo 'style="display:block;"'; ?>>
<form method="POST" class="doctor-form">

<input type="hidden" name="doctor_id" value="<?= $edit_doctor['doctor_id'] ?? ''; ?>">

<div class="form-group">
<label>Doctor Name</label>
<input type="text" name="Doctor_name" value="<?= $edit_doctor['Doctor_name'] ?? ''; ?>" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" value="<?= $edit_doctor['email'] ?? ''; ?>" required>
</div>

<div class="form-group">
<label>Start Date</label>
<input type="date" name="start_date" value="<?= $edit_doctor['start_date'] ?? ''; ?>">
</div>

<div class="form-group">
<label>Speciality</label>
<input type="text" name="speciality" value="<?= $edit_doctor['speciality'] ?? ''; ?>">
</div>

<div class="form-group">
<label>Status</label>
<select name="status">
<option value="Active" <?= ($edit_doctor['status']??'')=='Active'?'selected':'' ?>>Active</option>
<option value="Inactive" <?= ($edit_doctor['status']??'')=='Inactive'?'selected':'' ?>>Inactive</option>
</select>
</div>

<?php if($edit_doctor): ?>
<button type="submit" name="update" class="update-btn">Update Doctor</button>
<?php else: ?>
<button type="submit" name="save" class="add-btn">Add Doctor</button>
<?php endif; ?>

</form>
</div>

<!-- Doctor Table -->
<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Start Date</th>
<th>Speciality</th>
<th>Status</th>
<th>Actions</th>
</tr>

<?php if(mysqli_num_rows($result)>0): ?>
<?php while($row=mysqli_fetch_assoc($result)): ?>
<tr>
<td><?= $row['Doctor_name']; ?></td>
<td><?= $row['email']; ?></td>
<td><?= $row['start_date']; ?></td>
<td><?= $row['speciality']; ?></td>
<td><?= $row['status']; ?></td>
<td>
<a class="edit-btn" href="?edit=<?= $row['doctor_id']; ?>">Edit</a>
<a class="delete-btn" href="?delete=<?= $row['doctor_id']; ?>" onclick="return confirm('Delete this doctor?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="6" style="text-align:center;">No doctors found</td>
</tr>
<?php endif; ?>
</table>

</div>
</div>
</body>
</html>