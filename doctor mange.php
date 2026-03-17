<?php
session_start();
include '../db.php';

$staff_user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT staff_name FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $staff_user_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

$staff_name = $staff['staff_name'] ?? $_SESSION['user_name'];


// Delete Doctor
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM doctor WHERE doctor_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch single doctor for edit
$edit_doctor = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = mysqli_query($conn,"SELECT * FROM doctor WHERE doctor_id='$id'");
    $edit_doctor = mysqli_fetch_assoc($res);
}

// Update Doctor
if(isset($_POST['update'])){
    $id           = $_POST['doctor_id'];
    $Doctor_name  = $_POST['Doctor_name'];
    $email        = $_POST['email'];
    $start_date   = $_POST['start_date'];
    $speciality   = $_POST['speciality'];
    $status       = $_POST['status'];
    $updated_date = $_POST['updated_date'];

    $sql = "UPDATE doctor SET
            Doctor_name='$Doctor_name',
            email='$email',
            start_date='$start_date',
            speciality='$speciality',
            status='$status',
            updated_date='$updated_date',
            updated_by='$logged_user_id'
            WHERE doctor_id='$id'";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Add Doctor
if(isset($_POST['save'])){
    $Doctor_name  = $_POST['Doctor_name'];
    $email        = $_POST['email'];
    $start_date   = $_POST['start_date'];
    $speciality   = $_POST['speciality'];
    $status       = $_POST['status'];
    $updated_date = $_POST['updated_date'] ?: date('Y-m-d');

    $sql = "INSERT INTO doctor
            (Doctor_name, email, start_date, speciality, status, updated_by, updated_date, user_id)
            VALUES
            ('$Doctor_name', '$email', '$start_date', '$speciality', '$status', '$logged_user_id', '$updated_date', '$logged_user_id')";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Fetch Doctors
$query = "SELECT doctor_id, Doctor_name, email, start_date, speciality, status, updated_date FROM doctor ORDER BY doctor_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Management Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="staffpanel.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body>

<?php include('sidebar.php'); ?>

<div class="main">

<?php include('topbar.php'); ?>



    <div class="container">
        <h2>Doctor Management</h2>

        <!-- Form -->
        <form method="POST">
            <input type="hidden" name="doctor_id" value="<?php echo $edit_doctor['doctor_id'] ?? ''; ?>">
            <input type="text" name="Doctor_name" placeholder="Doctor Name" value="<?php echo $edit_doctor['Doctor_name'] ?? ''; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $edit_doctor['email'] ?? ''; ?>" required>
            <input type="date" name="start_date" placeholder="Start Date" value="<?php echo $edit_doctor['start_date'] ?? ''; ?>" required>
            <input type="text" name="speciality" placeholder="Speciality" value="<?php echo $edit_doctor['speciality'] ?? ''; ?>" required>
            <input type="text" name="status" placeholder="Status" value="<?php echo $edit_doctor['status'] ?? ''; ?>" required>
            <input type="date" name="updated_date" placeholder="Updated Date" value="<?php echo $edit_doctor['updated_date'] ?? date('Y-m-d'); ?>" required>
            <?php if($edit_doctor): ?>
                <button type="submit" name="update">Update Doctor</button>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="margin-left:10px;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="save">Add Doctor</button>
            <?php endif; ?>
        </form>

        <!-- Table -->
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Start Date</th>
                <th>Speciality</th>
                <th>Status</th>
                <th>Updated Date</th>
                <th>Actions</th>
            </tr>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['Doctor_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['speciality']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['updated_date']; ?></td>
                    <td class="actions">
                        <a href="?edit=<?php echo $row['doctor_id']; ?>">Edit</a> | 
                        <a href="?delete=<?php echo $row['doctor_id']; ?>" onclick="return confirm('Delete this doctor?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;">No doctors found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
}
</script>

</body>
</html>