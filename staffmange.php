<?php
session_start();
include('../db.php');

// Only allow patients
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
$staff_user_id = $_SESSION['user_id'];

// Fetch full name from patient table if exists
$stmt = $conn->prepare("SELECT staff_name FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $staff_user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

$staff_name = '';

if(isset($staff) && $staff){
    $staff_name = $staff['staff_name'];
} else {
    $staff_name = $_SESSION['user_name']; 
}

/// -------------------- Delete Staff --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM staff WHERE staff_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Update Staff --------------------
if(isset($_POST['update'])){
    $id = $_POST['staff_id'];
    $staff_name = $_POST['staff_name'];
    $email = $_POST['email']; // Added
    $position = $_POST['position'];
    $department = $_POST['department'];
    $join_date = $_POST['join_date'];
    $status = $_POST['status'];
    $updated_by = $admin_id; // Make sure $admin_id is defined
    $updated_date = date('Y-m-d');

    $sql = "UPDATE staff SET
            staff_name='$staff_name',
            email='$email',
            position='$position',
            department='$department',
            join_date='$join_date',
            status='$status',
            updated_by='$updated_by',
            updated_date='$updated_date'
            WHERE staff_id='$id'";
    mysqli_query($conn,$sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Add Staff --------------------
if(isset($_POST['save'])){
    $staff_name = $_POST['staff_name'];
    $email = $_POST['email']; // Added
    $position = $_POST['position'];
    $department = $_POST['department'];
    $join_date = $_POST['join_date'];
    $status = $_POST['status'];
    $updated_by = $admin_id; // Make sure $admin_id is defined
    $updated_date = date('Y-m-d');

    $sql = "INSERT INTO staff 
            (staff_name, email, position, department, join_date, status, updated_by, updated_date) 
            VALUES ('$staff_name','$email','$position','$department','$join_date','$status','$updated_by','$updated_date')";
    mysqli_query($conn,$sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Search Staff --------------------
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT * FROM staff 
              WHERE staff_name LIKE '%$search%' 
                 OR email LIKE '%$search%' 
                 OR position LIKE '%$search%' 
                 OR department LIKE '%$search%' 
                 OR status LIKE '%$search%'
              ORDER BY staff_id DESC";
} else {
    $query = "SELECT * FROM staff ORDER BY staff_id DESC";
}

$result = mysqli_query($conn, $query);

// -------------------- Edit Staff --------------------
$edit_staff = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = mysqli_query($conn,"SELECT * FROM staff WHERE staff_id='$id'");
    $edit_staff = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html>
<head>
      <link rel="stylesheet" href="staffpanel.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Staff Management</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
        .container { width: 95%; max-width: 1200px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #0d6efd; margin-bottom: 20px; }

        /* Top bar */
        .top-bar { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .top-bar input[type=text] { width: 300px; padding: 8px; border-radius: 6px; border: 1px solid #ccc; }
        .top-bar button { padding: 8px 15px; border:none; border-radius:6px; background: #198754; color:white; cursor:pointer; }
        .top-bar button:hover { opacity: 0.9; }

        /* Add/Edit Form */
        #addForm { display: none; margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #f8f9fa; }
        #addForm input, #addForm select { width: 100%; padding: 10px; margin: 6px 0; border-radius:6px; border:1px solid #ccc; box-sizing:border-box; }
        #addForm button { margin-top: 10px; padding: 10px 15px; border:none; border-radius:6px; cursor:pointer; color:white; }
        #addForm button[name="save"]{ background:#198754; }
        #addForm button[name="update"]{ background:#ffc107; color:#212529; }

        /* Staff Table */
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
            table-layout:auto;
        }
        table th,
        table td{
            text-align:center;
            padding:12px;
            border-bottom:1px solid #ddd;
            font-size:14px;
        }
        table th{
            background:#0d6efd;
            color:white;
            font-weight:500;
        }
        table tr:nth-child(even){
            background:#f8f9fa;
        }
        table tr:hover{
            background:#e2e6ea;
        }

        /* Action Buttons */
        table td a{
            margin:0 4px;
            text-decoration:none;
            padding:5px 10px;
            border-radius:6px;
            color:#fff;
            font-size:13px;
            display:inline-block;
        }
        table td a[href*="edit"]{ background:#0d6efd; }
        table td a[href*="delete"]{ background:#dc3545; }
        table td a:hover{ opacity:0.85; }

        /* Responsive */
        @media(max-width:1024px){
            .top-bar{ flex-direction: column; align-items: flex-start; }
            .top-bar input[type=text]{ width: 100%; }
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
    <h2>Staff Management</h2>

    <!-- Top Bar -->
    <div class="top-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search staff..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <button onclick="toggleAddForm()">+ Add Staff</button>
    </div>

    <!-- Add/Edit Form -->
    <div id="addForm" <?php if($edit_staff) echo 'style="display:block;"'; ?>>
        <form method="POST">
                <input type="hidden" name="staff_id" value="<?php echo $edit_staff['staff_id'] ?? ''; ?>">

    <div class="form-group">
        <label>Staff Name</label>
        <input type="text" name="staff_name" 
        value="<?php echo $edit_staff['staff_name'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" 
        value="<?php echo $edit_staff['email'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
        <label>Position</label>
        <input type="text" name="position" 
        value="<?php echo $edit_staff['position'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
        <label>Department</label>
        <input type="text" name="department" 
        value="<?php echo $edit_staff['department'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
        <label>Join Date</label>
        <input type="date" name="join_date" 
        value="<?php echo $edit_staff['join_date'] ?? ''; ?>" required>
    </div>

    <div class="form-group">
        <label>Status</label>
        <input type="text" name="status" 
        value="<?php echo $edit_staff['status'] ?? ''; ?>">
    </div>
            
            <?php if($edit_staff): ?>
                <button type="submit" name="update">Update Staff</button>
            <?php else: ?>
                <button type="submit" name="save">Add Staff</button>
            <?php endif; ?>
        </form>
    </div>

    <!-- Staff Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th> <!-- Added -->
            <th>Position</th>
            <th>Department</th>
            <th>Join Date</th>
            <th>Status</th>
            <th>Updated By</th>
            <th>Updated Date</th>
            <th>Actions</th>
        </tr>
        <?php if(mysqli_num_rows($result)>0): ?>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['staff_id']; ?></td>
                <td><?= $row['staff_name']; ?></td>
                <td><?= $row['email']; ?></td> <!-- Display email -->
                <td><?= $row['position']; ?></td>
                <td><?= $row['department']; ?></td>
                <td><?= $row['join_date']; ?></td>
                <td><?= $row['status']; ?></td>
                <td><?= $row['updated_by']; ?></td>
                <td><?= $row['updated_date']; ?></td>
                <td>
                    <a href="?edit=<?= $row['staff_id']; ?>">Edit</a>
                    <a href="?delete=<?= $row['staff_id']; ?>" onclick="return confirm('Delete this staff?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" style="text-align:center;">No staff found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</div>
</body>
</html>