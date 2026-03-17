<?php
include 'db.php'; // database connection

// -------------------- Delete Staff --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM staff WHERE staff_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Update Staff --------------------
if(isset($_POST['update'])){
    $id         = $_POST['staff_id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $contact    = $_POST['contact'];
    $user_id    = $_POST['user_id'];

    $sql = "UPDATE staff SET
            first_name='$first_name',
            last_name='$last_name',
            email='$email',
            contact='$contact',
            user_id='$user_id'
            WHERE staff_id='$id'";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Add Staff --------------------
if(isset($_POST['save'])){
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $contact    = $_POST['contact'];
    $user_id    = $_POST['user_id'];

    $sql = "INSERT INTO staff (first_name, last_name, email, contact, user_id)
            VALUES ('$first_name','$last_name','$email','$contact','$user_id')";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Search --------------------
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT * FROM staff 
              WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' 
              ORDER BY staff_id DESC";
} else {
    $query = "SELECT * FROM staff ORDER BY staff_id DESC";
}

$result = mysqli_query($conn, $query);

// -------------------- Fetch single staff for edit --------------------
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
    <title>Staff Management</title>
    <style>
        body { font-family: Arial,sans-serif; background:#f4f6f9; margin:0; padding:0;}
        .container { width:95%; max-width:1200px; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1);}
        h2 { text-align:center; color:#198754;}
        form input, form select, form textarea { padding:8px; margin:5px 0; width:100%; border-radius:5px; border:1px solid #ccc; }
        form button { padding:8px 15px; background:#198754; color:#fff; border:none; border-radius:5px; cursor:pointer; }
        form button:hover { background:#157347; }
        .top-bar { display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:15px; }
        .top-bar input[type=text]{ width:300px; padding:6px; }
        #addForm { display:none; margin-top:20px; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        table, th, td { border:1px solid #ccc; }
        th { background:#198754; color:#fff; padding:10px; }
        td { padding:10px; }
        tr:nth-child(even){ background:#f2f2f2; }
        a { text-decoration:none; color:#0d6efd; }
        a:hover { text-decoration:underline; }
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
    <h2>Staff Management</h2>

    <!-- Top Bar: Search + Add -->
    <div class="top-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <button onclick="toggleAddForm()">+ Add Staff</button>
    </div>

    <!-- Add/Edit Form -->
    <div id="addForm" <?php if($edit_staff) echo 'style="display:block;"'; ?>>
        <form method="POST">
            <input type="hidden" name="staff_id" value="<?php echo $edit_staff['staff_id'] ?? ''; ?>">
            <input type="text" name="Staff_name" placeholder="Staff Name" value="<?php echo $edit_staff['staff_name'] ?? ''; ?>" required>
            <input type="text" name="Position" placeholder="Position" value="<?php echo $edit_staff['position'] ?? ''; ?>" required>
            <input type="text" name="Department" placeholder="Department" value="<?php echo $edit_staff['department'] ?? ''; ?>">
            <input type="number" name="user_id" placeholder="User ID" value="<?php echo $edit_staff['user_id'] ?? ''; ?>">
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
            <th>Position</th>
            <th>Department</th>
            <th>User ID</th>
            <th>Actions</th>
        </tr>
        <?php if(mysqli_num_rows($result)>0): ?>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['staff_id']; ?></td>
                <td><?php echo $row['staff_name']; ?></td>
                <td><?php echo $row['position']; ?></td>
                <td><?php echo $row['department'];?></td> 
                <td><?php echo $row['user_id']; ?></td>
                <td>
                    <a href="?edit=<?php echo $row['staff_id']; ?>">Edit</a> | 
                    <a href="?delete=<?php echo $row['staff_id']; ?>" onclick="return confirm('Delete this staff?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No staff found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>