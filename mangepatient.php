<?php
session_start();
include 'db.php';

// Admin access check
if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1){
    header("Location: ../loginpage/loginpage.php");
    exit();
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

    $stmt = $conn->prepare("UPDATE patient SET 
        first_name=?, last_name=?, gender=?, date_of_birth=?, phone=?, email=?, 
        patient_type=?, blood_type=?, academic_yr=?, faculty=?, accomodation_type=?, 
        medical_history=?, surgical_history=?, family_history=?, marital_status=? 
        WHERE patient_id=?");
    $stmt->bind_param("sssssssssssssssi", 
        $first_name,$last_name,$gender,$dob,$phone,$email,$patient_type,
        $blood_type,$academic_yr,$faculty,$accomodation_type,$medical_history,
        $surgical_history,$family_history,$marital_status,$id);
    $stmt->execute();
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

    $stmt = $conn->prepare("INSERT INTO patient 
        (first_name,last_name,gender,date_of_birth,phone,email,patient_type,blood_type,
        academic_yr,faculty,accomodation_type,medical_history,surgical_history,family_history,marital_status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssssssssssss", 
        $first_name,$last_name,$gender,$dob,$phone,$email,$patient_type,
        $blood_type,$academic_yr,$faculty,$accomodation_type,$medical_history,
        $surgical_history,$family_history,$marital_status);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Search --------------------
$search = $_GET['search'] ?? '';
if($search){
    // First 2 letters match
    $like = $search . "%";
    $stmt = $conn->prepare("SELECT * FROM patient WHERE first_name LIKE ? OR last_name LIKE ? ORDER BY patient_id DESC");
    $stmt->bind_param("ss",$like,$like);
    $stmt->execute();
    $result = $stmt->get_result();
}else{
    $result = mysqli_query($conn,"SELECT * FROM patient ORDER BY patient_id DESC");
}

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
    <link rel="stylesheet" href="admindash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
     body { font-family: Arial,sans-serif; background:#f4f6f9; margin:0; padding:0;}
        .container { width:95%; max-width:1200px; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1);}
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
            transition:0.3s;
        }
        #addForm{
            display:none;
            background:#f8f9fa;
            padding:20px;
            border-radius:12px;
            margin-bottom:20px;
            box-shadow:0 3px 8px rgba(0,0,0,0.1);
        }
        #addForm input, #addForm select, #addForm textarea{
            width:100%;
            padding:10px;
            margin:6px 0;
            border-radius:6px;
            border:1px solid #ccc;
            box-sizing:border-box;
        }
        #addForm textarea{resize:vertical; min-height:60px;}
        #addForm button{
            margin-top:10px;
            background:#198754;
            color:white;
        }
        #addForm button[name="update"]{ background:#ffc107; }
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
            display:block;
            overflow-x:auto;
        }
        table th, table td{
            text-align:center;
            padding:12px;
            border-bottom:1px solid #ddd;
            font-size:14px;
            white-space:nowrap;
        }
        table th{
            background:#0d6efd;
            color:white;
            font-weight:500;
        }
        table tr:nth-child(even){ background:#f8f9fa; }
        table tr:hover{ background:#e2e6ea; }
        table td a{
            margin:0 4px;
            text-decoration:none;
            padding:4px 8px;
            border-radius:6px;
            color:#fff;
            font-size:13px;
        }
        table td a[href*="edit"]{ background:#0d6efd; }
        table td a[href*="delete"]{ background:#dc3545; }
        table td a:hover{ opacity:0.85; }
        @media(max-width:1024px){
            .main{ margin-left:0; padding:15px; }
            .top-bar{ flex-direction:column; align-items:flex-start; }
            .top-bar input[type=text]{width:100%;}
        }
    </style>
    <script>
        function toggleAddForm(){
            var f=document.getElementById('addForm'); 
            f.style.display=(f.style.display==='none')?'block':'none';
        }
    </script>
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="main">
<?php include("topbar.php"); ?>

<div class="container">
    <h2>Patient Management</h2>

    <div class="top">
        <form method="GET" style="flex:1;">
            <input type="text" name="search" placeholder="Search by first 2 letters..." value="<?= htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <button onclick="toggleAddForm()">+ Add Patient</button>
    </div>

    <div id="addForm" <?= $edit_patient?'style="display:block;"':''; ?>>
        <form method="POST">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <input type="hidden" name="id" value="<?= $edit_patient['patient_id'] ?? ''; ?>">
                <input type="text" name="first_name" placeholder="First Name" value="<?= $edit_patient['first_name'] ?? ''; ?>" required>
                <input type="text" name="last_name" placeholder="Last Name" value="<?= $edit_patient['last_name'] ?? ''; ?>" required>

                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= ($edit_patient['gender']??'')=='Male'?'selected':''; ?>>Male</option>
                    <option value="Female" <?= ($edit_patient['gender']??'')=='Female'?'selected':''; ?>>Female</option>
                </select>

                <input type="date" name="date_of_birth" value="<?= $edit_patient['date_of_birth'] ?? ''; ?>" required>
                <input type="text" name="phone" placeholder="Phone" value="<?= $edit_patient['phone'] ?? ''; ?>">
                <input type="email" name="email" placeholder="Email" value="<?= $edit_patient['email'] ?? ''; ?>">

                <select name="patient_type">
                    <option value="">Select Type</option>
                    <option value="Student" <?= ($edit_patient['patient_type']??'')=='Student'?'selected':''; ?>>Student</option>
                    <option value="Staff" <?= ($edit_patient['patient_type']??'')=='Staff'?'selected':''; ?>>Staff</option>
                    <option value="Visitor" <?= ($edit_patient['patient_type']??'')=='Visitor'?'selected':''; ?>>Visitor</option>
                </select>

                <select name="blood_type">
                    <option value="">Select Blood Type</option>
                    <option value="A+" <?= ($edit_patient['blood_type']??'')=='A+'?'selected':''; ?>>A+</option>
                    <option value="A-" <?= ($edit_patient['blood_type']??'')=='A-'?'selected':''; ?>>A-</option>
                    <option value="B+" <?= ($edit_patient['blood_type']??'')=='B+'?'selected':''; ?>>B+</option>
                    <option value="B-" <?= ($edit_patient['blood_type']??'')=='B-'?'selected':''; ?>>B-</option>
                    <option value="O+" <?= ($edit_patient['blood_type']??'')=='O+'?'selected':''; ?>>O+</option>
                    <option value="O-" <?= ($edit_patient['blood_type']??'')=='O-'?'selected':''; ?>>O-</option>
                    <option value="AB+" <?= ($edit_patient['blood_type']??'')=='AB+'?'selected':''; ?>>AB+</option>
                    <option value="AB-" <?= ($edit_patient['blood_type']??'')=='AB-'?'selected':''; ?>>AB-</option>
                </select>

                <input type="text" name="academic_yr" placeholder="Academic Year" value="<?= $edit_patient['academic_yr'] ?? ''; ?>">
                <input type="text" name="faculty" placeholder="Faculty" value="<?= $edit_patient['faculty'] ?? ''; ?>">

                <select name="accomodation_type">
                    <option value="">Select Accommodation</option>
                    <option value="Hostel" <?= ($edit_patient['accomodation_type']??'')=='Hostel'?'selected':''; ?>>Hostel</option>
                    <option value="Home" <?= ($edit_patient['accomodation_type']??'')=='Home'?'selected':''; ?>>Home</option>
                    <option value="Off-Campus" <?= ($edit_patient['accomodation_type']??'')=='Off-Campus'?'selected':''; ?>>Off-Campus</option>
                </select>
            </div>

            <textarea name="medical_history" placeholder="Medical History"><?= $edit_patient['medical_history'] ?? ''; ?></textarea>
            <textarea name="surgical_history" placeholder="Surgical History"><?= $edit_patient['surgical_history'] ?? ''; ?></textarea>
            <textarea name="family_history" placeholder="Family History"><?= $edit_patient['family_history'] ?? ''; ?></textarea>
            <input type="text" name="marital_status" placeholder="Marital Status" value="<?= $edit_patient['marital_status'] ?? ''; ?>">
            <button type="submit" name="<?= $edit_patient?'update':'save'; ?>"><?= $edit_patient?'Update Patient':'Add Patient'; ?></button>
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table>
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
                    <td><?= $row['patient_id']; ?></td>
                    <td><?= $row['first_name'].' '.$row['last_name']; ?></td>
                    <td><?= $row['gender']; ?></td>
                    <td><?= $row['date_of_birth']; ?></td>
                    <td><?= $row['phone']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td><?= $row['patient_type']; ?></td>
                    <td><?= $row['blood_type']; ?></td>
                    <td><?= $row['academic_yr']; ?></td>
                    <td><?= $row['faculty']; ?></td>
                    <td><?= $row['accomodation_type']; ?></td>
                    <td>
                        <a href="?edit=<?= $row['patient_id']; ?>">Edit</a> | 
                        <a href="?delete=<?= $row['patient_id']; ?>" onclick="return confirm('Delete this patient?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="12" style="text-align:center;">No patients found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>
</div>
</body>
</html>