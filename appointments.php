<?php
session_start();
include '../db.php';

// Check login
if(!isset($_SESSION['user_id'])){
    header("Location: ../loginpage/loginpage.php");
    exit();
}
$doctor_id = $_SESSION['user_id'];

// Actions: confirm, cancel, delete
if(isset($_GET['confirm'])){
    $stmt = $conn->prepare("UPDATE appointment SET confirmation_status='Confirmed', confirmation_by=?, confirmation_date=NOW() WHERE appointment_id=?");
    $stmt->bind_param("ii",$doctor_id,$_GET['confirm']);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']); exit();
}
if(isset($_GET['cancel'])){
    $stmt = $conn->prepare("UPDATE appointment SET confirmation_status='Cancelled', confirmation_by=?, confirmation_date=NOW() WHERE appointment_id=?");
    $stmt->bind_param("ii",$doctor_id,$_GET['cancel']);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']); exit();
}
if(isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM appointment WHERE appointment_id=?");
    $stmt->bind_param("i",$_GET['delete']);
    $stmt->execute();
    header("Location: ".$_SERVER['PHP_SELF']); exit();
}

// Fetch Appointments
$status_filter = $_GET['status'] ?? 'All';
$search = $_GET['search'] ?? '';

$where = "1=1";
if($status_filter != 'All') $where .= " AND a.confirmation_status='$status_filter'";
if($search) $where .= " AND (p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%')";

$query = "SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last, d.Doctor_name AS doctor_name
          FROM appointment a
          JOIN patient p ON a.patient_id=p.patient_id
          JOIN doctor d ON a.doctor_id=d.doctor_id
          WHERE $where
          ORDER BY a.requested_date DESC";

$result = mysqli_query($conn,$query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Appointments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Sidebar */
/* Sidebar container with blue theme */
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top:0; left:0;
    background:#00008B; /* Bootstrap primary blue */
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}

/* Logo section */
.sidebar .logo { 
    text-align:center; 
    margin-bottom:20px; 
}
.sidebar .logo img { 
    width:70px; 
    border-radius:50%; 
    border:2px solid #fff; 
    margin-bottom:10px; 
}
.sidebar .logo h2 { 
    font-size:16px; 
    color:#fff; 
    margin:0; 
}

/* Menu list */
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a { 
    color:#fff; 
    text-decoration:none; 
    display:flex; 
    align-items:center; 
    padding:10px 20px; 
    border-radius:8px; 
    transition:.3s; 
}
.sidebar ul li a i { 
    margin-right:10px; 
    font-size:18px; 
}

/* Hover effect: lighter blue */
.sidebar ul li a:hover { 
    background:#0056b3; /* Darker blue on hover */
    color:#fff; 
}

/* Logout button */
.sidebar .logout a { 
    background:#ffc107; /* Yellow logout for contrast */
    display:flex; 
    align-items:center; 
    padding:10px 20px; 
    border-radius:8px; 
    transition:.3s; 
    color:#000;
}
.sidebar .logout a:hover { 
    background:#e0a800; 
    color:#000; 
}

/* Main content adjustment */
.main { margin-left:260px; padding:30px; }
.main h2 { margin-bottom:20px; }

/* Table */
.table th, .table td { vertical-align:middle; }
.badge { font-size:.9em; }

/* Tabs */
.nav-tabs .nav-link.active { font-weight:bold; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <img src="mclogo.png" alt="Logo">
        <h2>Doctor Panel</h2>
    </div>
    <ul class="menu">
        <li><a href="doctordash.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="mypatients.php"><i class="bi bi-people"></i> My Patients</a></li>
        <li><a href="appointments.php"class="active"><i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="reports.php"><i class="bi bi-file-earmark-medical"></i> Reports</a></li>
        <li><a href="prescription.php"><i class="bi bi-box-seam"></i> Medicine Stock</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <!-- <li><a href="profile.php"><i class="bi bi-person"></i> My Profile</a></li> -->
    </ul>
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<div class="main">
    <h2>My Appointments</h2>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3">
        <?php $tabs = ['All','Pending','Confirmed','Cancelled']; ?>
        <?php foreach($tabs as $tab): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($status_filter==$tab)?'active':'' ?>" href="?status=<?= $tab ?>"><?= $tab ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Search -->
    <form method="GET" class="d-flex mb-3">
        <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">
        <input type="text" name="search" class="form-control me-2" placeholder="Search Patient..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary">Search</button>
    </form>

    <!-- Appointments Table -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if($result->num_rows>0):
            $count=1;
            while($row=$result->fetch_assoc()): ?>
            <tr>
                <td><?= $count++; ?></td>
                <td><?= htmlspecialchars($row['patient_first'].' '.$row['patient_last']); ?></td>
                <td><?= htmlspecialchars($row['doctor_name']); ?></td>
                <td><?= date('d M Y', strtotime($row['requested_date'])); ?></td>
                <td><?= htmlspecialchars($row['preferred_time']); ?></td>
                <td>
                    <?php
                        if($row['confirmation_status']=='Pending') echo '<span class="badge bg-warning">Pending</span>';
                        if($row['confirmation_status']=='Confirmed') echo '<span class="badge bg-success">Confirmed</span>';
                        if($row['confirmation_status']=='Cancelled') echo '<span class="badge bg-danger">Cancelled</span>';
                    ?>
                </td>
                <td>
                    <?php if($row['confirmation_status']=='Pending'): ?>
                        <a href="?confirm=<?= $row['appointment_id'] ?>" class="btn btn-success btn-sm">Accept</a>
                        <a href="?cancel=<?= $row['appointment_id'] ?>" class="btn btn-warning btn-sm">Cancel</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $row['appointment_id'] ?>" onclick="return confirm('Delete appointment?')" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" class="text-center text-muted">No appointments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>