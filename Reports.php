<?php
session_start();
include "../db.php";

if (isset($_POST['add_report'])) {
    $patient_id = $_POST['patient_id'];
    $visit_date = $_POST['visit_date'];
    $diagnosis  = $_POST['diagnosis'];
    $doctor_id = $_SESSION['user_id'];
    echo $doctor_id;
    $file_path = NULL;

    // Handle file upload
    if (isset($_FILES['record_file']) && $_FILES['record_file']['error'] == 0) {
        $upload_dir = "uploads/"; // Make sure this folder exists and is writable
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $filename = time() . "_" . basename($_FILES['record_file']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['record_file']['tmp_name'], $target_file)) {
            $file_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO medical_record (doctor_id, patient_id, visit_date, diagnosis, file_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $doctor_id, $patient_id, $visit_date, $diagnosis, $file_path);

    if ($stmt->execute()) {
        echo "<script>alert('Medical record added successfully'); window.location='Reports.php';</script>";
    } else {
        echo "<script>alert('Failed to add medical record');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Sidebar */
.sidebar {
    width: 250px; height: 100vh; position: fixed; top:0; left:0;
    background:#00008B; color:#fff; display:flex; flex-direction:column;
    justify-content:space-between; padding:20px 0; box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}
.sidebar .logo { text-align:center; margin-bottom:20px; }
.sidebar .logo img { width:70px; border-radius:50%; border:2px solid #fff; margin-bottom:10px; }
.sidebar .logo h2 { font-size:16px; color:#fff; margin:0; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a { color:#fff; text-decoration:none; display:flex; align-items:center; padding:10px 20px; border-radius:8px; transition:.3s; }
.sidebar ul li a i { margin-right:10px; font-size:18px; }
.sidebar ul li a:hover, .sidebar ul li a.active { background:#0056b3; color:#fff; }
.sidebar .logout a { background:#ffc107; display:flex; align-items:center; padding:10px 20px; border-radius:8px; transition:.3s; color:#000; }
.sidebar .logout a:hover { background:#e0a800; color:#000; }

/* Main Content */
.content { margin-left:260px; padding:30px; background:#f4f6f9; min-height:100vh; }
.content h2 { margin-bottom:25px; }

/* Cards */
.card { box-shadow:0 3px 10px rgba(0,0,0,0.1); border-radius:10px; }
.card-header { font-weight:bold; }

/* Table */
.table th, .table td { vertical-align:middle; }
.table-hover tbody tr:hover { background:#e9f2ff; }

/* Buttons */
.btn i { margin-right:5px; }

/* Responsive */
@media(max-width:768px){
    .sidebar { width:100%; height:auto; position:relative; }
    .content { margin-left:0; padding:20px; }
}
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
        <li><a href="mypatients.php"><i class="bi bi-people"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="reports.php" class="active"><i class="bi bi-file-earmark-medical"></i> Reports</a></li>
        <li><a href="prescription.php"><i class="bi bi-box-seam"></i> Medicine Stock</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <!-- <li><a href="profile.php"><i class="bi bi-person"></i> My Profile</a></li> -->
    </ul>
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="content">
    <h2 class="text-primary"><i class="bi bi-file-earmark-text"></i> Patient Medical Records</h2>

    <!-- Add New Record -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Add New Medical Record</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Select Patient</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">-- Select Patient --</option>
                        <?php
                        $sql = "SELECT patient_id, first_name, last_name FROM patient";
                        $res = $conn->query($sql);
                        while($row = $res->fetch_assoc()){
                            echo "<option value='".$row['patient_id']."'>".$row['first_name']." ".$row['last_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Visit Date</label>
                    <input type="date" name="visit_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Diagnosis</label>
                    <textarea name="diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload File (PDF/Image)</label>
                    <input type="file" name="record_file" class="form-control" accept=".pdf,image/*">
                </div>
                <button type="submit" name="add_report" class="btn btn-success"><i class="bi bi-save"></i> Save Record</button>
            </form>
        </div>
    </div>

  

    <!-- List Records -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Existing Medical Records</div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Patient</th>
                        <th>Visit Date</th>
                        <th>Diagnosis</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT mr.*, p.first_name, p.last_name FROM medical_record mr JOIN patient p ON mr.patient_id=p.patient_id ORDER BY mr.record_id DESC";
                    $res = $conn->query($sql);
                    if($res->num_rows>0){
                        $counter=1;
                        while ($row = $res->fetch_assoc()){
                            echo "<tr>
                                <td>{$counter}</td>
                                <td>{$row['first_name']} {$row['last_name']}</td>
                                <td>".date('d M Y', strtotime($row['visit_date']))."</td>
                                <td>{$row['diagnosis']}</td>
                                <td>";
                            if($row['file_path']){
                                echo "<a href='".$row['file_path']."' target='_blank'><i class='bi bi-file-earmark-text'></i> View File</a>";
                            } else {
                                echo "No File";
                            }
                            echo "</td></tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted'>No records found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>