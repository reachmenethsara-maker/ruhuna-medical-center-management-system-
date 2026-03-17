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
// -------------------- Delete Patient --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM patient WHERE patient_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Update Patient --------------------
if(isset($_POST['update'])){
    $id                 = $_POST['id'];
    $first_name         = $_POST['first_name'];
    $last_name          = $_POST['last_name'];
    $gender             = $_POST['gender'];
    $dob                = $_POST['date_of_birth'];
    $phone              = $_POST['phone'];
    $email              = $_POST['email'];
    $patient_type       = $_POST['patient_type'];
    $blood_type         = $_POST['blood_type'];
    $academic_yr        = $_POST['academic_yr'];
    $faculty            = $_POST['faculty'];
    $accomodation_type  = $_POST['accomodation_type'];
    $medical_history    = $_POST['medical_history'];
    $surgical_history   = $_POST['surgical_history'];
    $family_history     = $_POST['family_history'];
    $marital_status     = $_POST['marital_status'];

    $sql = "UPDATE patient SET
            first_name='$first_name',
            last_name='$last_name',
            gender='$gender',
            date_of_birth='$dob',
            phone='$phone',
            email='$email',
            patient_type='$patient_type',
            blood_type='$blood_type',
            academic_yr='$academic_yr',
            faculty='$faculty',
            accomodation_type='$accomodation_type',
            medical_history='$medical_history',
            surgical_history='$surgical_history',
            family_history='$family_history',
            marital_status='$marital_status'
            WHERE patient_id='$id'";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Add Patient --------------------
if(isset($_POST['save'])){
    $first_name         = $_POST['first_name'];
    $last_name          = $_POST['last_name'];
    $gender             = $_POST['gender'];
    $dob                = $_POST['date_of_birth'];
    $phone              = $_POST['phone'];
    $email              = $_POST['email'];
    $patient_type       = $_POST['patient_type'];
    $blood_type         = $_POST['blood_type'];
    $academic_yr        = $_POST['academic_yr'];
    $faculty            = $_POST['faculty'];
    $accomodation_type  = $_POST['accomodation_type'];
    $medical_history    = $_POST['medical_history'];
    $surgical_history   = $_POST['surgical_history'];
    $family_history     = $_POST['family_history'];
    $marital_status     = $_POST['marital_status'];

    $sql = "INSERT INTO patient
            (first_name,last_name,gender,date_of_birth,phone,email,patient_type,blood_type,academic_yr,faculty,accomodation_type,medical_history,surgical_history,family_history,marital_status)
            VALUES
            ('$first_name','$last_name','$gender','$dob','$phone','$email','$patient_type','$blood_type','$academic_yr','$faculty','$accomodation_type','$medical_history','$surgical_history','$family_history','$marital_status')";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Search --------------------
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT * FROM patient 
              WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%'
              ORDER BY patient_id DESC";
} else {
    $query = "SELECT * FROM patient ORDER BY patient_id DESC";
}

$result = mysqli_query($conn, $query);

// -------------------- Fetch single patient for edit --------------------
$edit_patient = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = mysqli_query($conn,"SELECT * FROM patient WHERE patient_id='$id'");
    $edit_patient = mysqli_fetch_assoc($res);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Management</title>
    <link rel="stylesheet" href="staffpanel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
 /* Page background */
body{
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background:#f4f6f9;
    margin:0;
    padding:0;
}

/* Main container */
.container{
    width:95%;
    max-width:1200px;
    margin:20px auto;
    background:#ffffff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
}

/* Title */
h2{
    text-align:center;
    color:#0d6efd;
    margin-bottom:20px;
}

/* Top bar */
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
    margin-bottom:20px;
}

/* Search box */
.top-bar input[type=text]{
    width:260px;
    padding:8px 10px;
    border-radius:6px;
    border:1px solid #ccc;
}

/* Form style */
form{
    background:#f9fbfd;
    padding:15px;
    border-radius:10px;
    border:1px solid #e2e6ea;
}

form input,
form select,
form textarea{
    width:100%;
    padding:9px;
    margin:6px 0;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:14px;
}

/* Save / Update button */
form button{
    padding:9px 18px;
    background:#0d6efd;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;
}

form button:hover{
    background:#0b5ed7;
}

/* Add form hidden */
#addForm{
    display:none;
    margin-top:20px;
}

/* Horizontal scroll container */
.table-container{
    width:100%;
    overflow-x:auto;
}

/* Increase table width */
.data-table{
    width:100%;
    min-width:1000px;
    border-collapse:collapse;
}
/* Table header */
th{
    background:#0d6efd;
    color:white;
    padding:12px;
    text-align:center;
}

/* Table cells */
td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #eee;
}

/* Zebra rows */
tr:nth-child(even){
    background:#f8f9fb;
}

/* Hover row */
tr:hover{
    background:#eef4ff;
}

/* Table style */
.data-table th{
    background:#0d6efd;
    color:white;
    padding:12px;
    text-align:center;
}

.data-table td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

/* Buttons same row */
.action-cell{
    white-space:nowrap;
}

/* Buttons */
.action-btn{
    padding:6px 12px;
    border-radius:5px;
    text-decoration:none;
    font-size:13px;
    color:white;
    margin:2px;
    display:inline-block;
}

/* Edit button */
.edit-btn{
    background:#198754;
}

.edit-btn:hover{
    background:#157347;
}

/* Delete button */
.delete-btn{
    background:#dc3545;
}

.delete-btn:hover{
    background:#bb2d3b;
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
    <?php include('sidebar.php'); ?>

<div class="main">

<?php include('topbar.php'); ?>
   
<div class="container">
    <h2>Patient Management</h2>

   
  

    <!-- Add/Edit Form -->
    <div id="addForm" 
    
    <?php if($edit_patient) echo 'style="display:block;"'; ?>>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_patient['patient_id'] ?? ''; ?>">
            <input type="text" name="first_name" placeholder="First Name" value="<?php echo $edit_patient['first_name'] ?? ''; ?>" required>
            <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $edit_patient['last_name'] ?? ''; ?>" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php if(($edit_patient['gender']??'')=='Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if(($edit_patient['gender']??'')=='Female') echo 'selected'; ?>>Female</option>
            </select>
            <input type="date" name="date_of_birth" value="<?php echo $edit_patient['date_of_birth'] ?? ''; ?>" required>
            <input type="text" name="phone" placeholder="Phone" value="<?php echo $edit_patient['phone'] ?? ''; ?>">
            <input type="email" name="email" placeholder="Email" value="<?php echo $edit_patient['email'] ?? ''; ?>">
            <input type="text" name="patient_type" placeholder="Patient Type" value="<?php echo $edit_patient['patient_type'] ?? ''; ?>">
            <input type="text" name="blood_type" placeholder="Blood Type" value="<?php echo $edit_patient['blood_type'] ?? ''; ?>">
            <input type="text" name="academic_yr" placeholder="Academic Year" value="<?php echo $edit_patient['academic_yr'] ?? ''; ?>">
            <input type="text" name="faculty" placeholder="Faculty" value="<?php echo $edit_patient['faculty'] ?? ''; ?>">
            <input type="text" name="accomodation_type" placeholder="Accommodation Type" value="<?php echo $edit_patient['accomodation_type'] ?? ''; ?>">
            <textarea name="medical_history" placeholder="Medical History"><?php echo $edit_patient['medical_history'] ?? ''; ?></textarea>
            <textarea name="surgical_history" placeholder="Surgical History"><?php echo $edit_patient['surgical_history'] ?? ''; ?></textarea>
            <textarea name="family_history" placeholder="Family History"><?php echo $edit_patient['family_history'] ?? ''; ?></textarea>
            <input type="text" name="marital_status" placeholder="Marital Status" value="<?php echo $edit_patient['marital_status'] ?? ''; ?>">
            <?php if($edit_patient): ?>
                <button type="submit" name="update">Update Patient</button>
            <?php else: ?>
                <button type="submit" name="save">Add Patient</button>
            <?php endif; ?>
        </form>
    </div>

   <div class="table-container">
    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>DOB</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Patient Type</th>
            <th>Blood Type</th>
            <th>Academic Yr</th>
            <th>Faculty</th>
            <th>Accommodation</th>
            <th>Actions</th>
        </tr>
        <?php if(mysqli_num_rows($result)>0): ?>
            <?php while($row=mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['patient_id']; ?></td>
                <td><?php echo $row['first_name'].' '.$row['last_name']; ?></td>
                <td><?php echo $row['gender']; ?></td>
                <td><?php echo $row['date_of_birth']; ?></td>
                <td><?php echo $row['phone']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['patient_type']; ?></td>
                <td><?php echo $row['blood_type']; ?></td>
                <td><?php echo $row['academic_yr']; ?></td>
                <td><?php echo $row['faculty']; ?></td>
                <td><?php echo $row['accomodation_type']; ?></td>
             <td>

<a href="?edit=<?php echo $row['patient_id']; ?>" class="action-btn edit-btn">
Edit
</a>

<a href="?delete=<?php echo $row['patient_id']; ?>" 
class="action-btn delete-btn"
onclick="return confirm('Delete this patient?')">
Delete
</a>

</td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="12" style="text-align:center;">No patients found.</td></tr>
        <?php endif; ?>
    </table>
        </div>
</div>
</body>
</html>