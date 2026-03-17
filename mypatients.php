<?php
session_start();
include '../db.php';

// Doctor login check
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Fetch patients
$sql = "SELECT * FROM patient ORDER BY patient_id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Patients | Doctor Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* Sidebar */
.sidebar { width:250px;height:100vh;position:fixed;top:0;left:0;background:#00008B;color:#fff;display:flex;flex-direction:column;justify-content:space-between;padding:20px 0;box-shadow:2px 0 10px rgba(0,0,0,0.3);}
.sidebar .logo{text-align:center;margin-bottom:20px;}
.sidebar .logo img{width:70px;height:70px;border-radius:50%;border:2px solid #fff;margin-bottom:10px;}
.sidebar .logo h2{font-size:16px;margin:0;color:#fff;}
.sidebar ul{list-style:none;padding:0;}
.sidebar ul li{margin-bottom:10px;}
.sidebar ul li a{color:#fff;text-decoration:none;display:flex;align-items:center;padding:10px 20px;border-radius:8px;transition:.3s;}
.sidebar ul li a i{margin-right:10px;font-size:18px;}
.sidebar ul li a:hover,.sidebar ul li a.active{background:#0056b3;color:#fff;}
.sidebar .logout a{background:#ffc107;display:flex;align-items:center;padding:10px 20px;border-radius:8px;color:#000;transition:.3s;}
.sidebar .logout a:hover{background:#e0a800;}

/* Main content */
.main{margin-left:260px;padding:30px;background:#f4f6f9;min-height:100vh;}
.main h2{margin-bottom:20px;}
.card{border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.1);}
.card-header{font-weight:bold;background:#fff;}
.table thead{background:#00008B;color:#fff;}
.table-hover tbody tr:hover{background:#e9f2ff;}
#searchInput{max-width:400px;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="mclogo.png" alt="Logo">
        <h2>Doctor Panel</h2>
    </div>
    <ul class="menu">
        <li><a href="doctordash.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="#" class="active"><i class="bi bi-people"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="reports.php"><i class="bi bi-file-medical"></i> Reports</a></li>
        <li><a href="medicine_stock.php"><i class="bi bi-box-seam"></i> Medicine Stock</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <!-- <li><a href="profile.php"><i class="bi bi-person"></i> My Profile</a></li> -->
    </ul>
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main">
<h2><i class="bi bi-people"></i> My Patients</h2>

<div class="card">
<div class="card-header">Patients List</div>
<div class="card-body">
<input type="text" id="searchInput" class="form-control mb-3" placeholder="Search by name or phone">
<div class="table-responsive">
<table class="table table-hover table-bordered align-middle">
<thead>
<tr>
<th>Patient ID</th>
<th>Name</th>
<th>Phone</th>
<th>Patient Type</th>
<th class="text-end">Action</th>
</tr>
</thead>
<tbody>
<?php if($result->num_rows>0): ?>
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['patient_id']; ?></td>
<td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
<td><?= htmlspecialchars($row['phone']); ?></td>
<td><?= htmlspecialchars($row['patient_type']); ?></td>
<td class="text-end">
<button class="btn btn-sm btn-outline-primary view-btn"
    data-id="<?= $row['patient_id']; ?>"
    data-first="<?= htmlspecialchars($row['first_name']); ?>"
    data-last="<?= htmlspecialchars($row['last_name']); ?>"
    data-gender="<?= htmlspecialchars($row['gender']); ?>"
    data-dob="<?= htmlspecialchars($row['date_of_birth']); ?>"
    data-phone="<?= htmlspecialchars($row['phone']); ?>"
    data-email="<?= htmlspecialchars($row['email']); ?>"
    data-type="<?= htmlspecialchars($row['patient_type']); ?>"
    data-blood="<?= htmlspecialchars($row['blood_type']); ?>"
    data-academic="<?= htmlspecialchars($row['academic_yr']); ?>"
    data-faculty="<?= htmlspecialchars($row['faculty']); ?>"
    data-accomodation="<?= htmlspecialchars($row['accomodation_type']); ?>"
    data-medical="<?= htmlspecialchars($row['medical_history']); ?>"
    data-surgical="<?= htmlspecialchars($row['surgical_history']); ?>"
    data-family="<?= htmlspecialchars($row['family_history']); ?>"
    data-marital="<?= htmlspecialchars($row['marital_status']); ?>"
>
<i class="bi bi-eye"></i> View
</button>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5" class="text-center py-4">No patients found</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>

<!-- VIEW & UPDATE MODAL -->
<div class="modal fade" id="viewPatientModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Patient Details</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<form id="updatePatientForm">
<input type="hidden" name="patient_id" id="patient_id">

<div class="row">
<div class="col-md-6 mb-2"><label>First Name</label><input type="text" class="form-control" id="first_name" name="first_name"></div>
<div class="col-md-6 mb-2"><label>Last Name</label><input type="text" class="form-control" id="last_name" name="last_name"></div>
<div class="col-md-6 mb-2"><label>Gender</label><input type="text" class="form-control" id="gender" name="gender"></div>
<div class="col-md-6 mb-2"><label>Date of Birth</label><input type="date" class="form-control" id="dob" name="dob"></div>
<div class="col-md-6 mb-2"><label>Phone</label><input type="text" class="form-control" id="phone" name="phone"></div>
<div class="col-md-6 mb-2"><label>Email</label><input type="email" class="form-control" id="email" name="email"></div>
<div class="col-md-6 mb-2"><label>Patient Type</label><input type="text" class="form-control" id="patient_type" name="patient_type"></div>
<div class="col-md-6 mb-2"><label>Blood Type</label><input type="text" class="form-control" id="blood_type" name="blood_type"></div>
<div class="col-md-6 mb-2"><label>Academic Year</label><input type="text" class="form-control" id="academic_yr" name="academic_yr"></div>
<div class="col-md-6 mb-2"><label>Faculty</label><input type="text" class="form-control" id="faculty" name="faculty"></div>
<div class="col-md-6 mb-2"><label>Accommodation Type</label><input type="text" class="form-control" id="accomodation_type" name="accomodation_type"></div>
<div class="col-md-12 mb-2"><label>Medical History</label><textarea class="form-control" id="medical_history" name="medical_history" rows="2"></textarea></div>
<div class="col-md-12 mb-2"><label>Surgical History</label><textarea class="form-control" id="surgical_history" name="surgical_history" rows="2"></textarea></div>
<div class="col-md-12 mb-2"><label>Family History</label><textarea class="form-control" id="family_history" name="family_history" rows="2"></textarea></div>
<div class="col-md-6 mb-2"><label>Marital Status</label><input type="text" class="form-control" id="marital_status" name="marital_status"></div>
</div>

<div class="text-end mt-3">
<button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
</div>
</form>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// View patient
document.querySelectorAll('.view-btn').forEach(btn=>{
    btn.addEventListener('click', function(){
        const fields = ['id','first','last','gender','dob','phone','email','type','blood','academic','faculty','accomodation','medical','surgical','family','marital'];
        fields.forEach(f=>{
            const el = document.getElementById({
                'id':'patient_id',
                'first':'first_name',
                'last':'last_name',
                'gender':'gender',
                'dob':'dob',
                'phone':'phone',
                'email':'email',
                'type':'patient_type',
                'blood':'blood_type',
                'academic':'academic_yr',
                'faculty':'faculty',
                'accomodation':'accomodation_type',
                'medical':'medical_history',
                'surgical':'surgical_history',
                'family':'family_history',
                'marital':'marital_status'
            }[f]);
            el.value = this.dataset[f];
        });
        new bootstrap.Modal(document.getElementById('viewPatientModal')).show();
    });
});

// Update patient
document.getElementById('updatePatientForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    fetch('update_patient.php',{
        method:'POST',
        body:formData
    }).then(res=>res.json())
    .then(data=>{
        alert(data.message);
        if(data.success) location.reload();
    });
});
</script>

</body>
</html>
<?php $conn->close(); ?>