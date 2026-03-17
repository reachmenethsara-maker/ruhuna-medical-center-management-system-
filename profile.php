<?php
session_start();
include("db.php");

// ---------------------------
// Check if doctor is logged in
// ---------------------------
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("You are not logged in!");
}

// ---------------------------
// Fetch doctor details
// ---------------------------
// Use INNER JOIN if every doctor has a user
$sql = "SELECT d.Doctor_name, d.speciality, u.user_name, d.email, u.user_id
        FROM doctor d
        INNER JOIN user u ON d.user_id = u.user_id
        WHERE u.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// If doctor record is missing
// if (!$doctor) {
//     die("Doctor profile not found. Please contact admin.");
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard</title>

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* =======================
    SIDEBAR
======================= */
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    top:0; left:0;
    background:#00008B;
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:20px 0;
}
.sidebar .logo { text-align:center; margin-bottom:20px; }
.sidebar .logo img { width:70px; border-radius:50%; border:2px solid #fff; margin-bottom:10px; }
.sidebar h4 { text-align:center; margin-bottom:20px; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a {
    color:#fff; text-decoration:none;
    display:flex; align-items:center;
    padding:10px 20px; border-radius:8px;
    transition:.3s;
}
.sidebar ul li a i { margin-right:10px; font-size:18px; }
.sidebar ul li a:hover, .sidebar ul li a.active { background:#0056b3; }
.sidebar .logout a { background:#ffc107; display:flex; align-items:center; padding:10px 20px; border-radius:8px; color:#000; text-decoration:none; }
.sidebar .logout a:hover { background:#e0a800; }

/* =======================
    MAIN CONTENT
======================= */
.main { margin-left:260px; padding:30px; background:#f4f6f9; min-height:100vh; }
.main h2 { margin-bottom:25px; color:#2a5298; }
.doctor-card {
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}
.doctor-card table { width:100%; }
.doctor-card table th, .doctor-card table td { padding:10px; vertical-align:middle; }
.doctor-card table th { width:200px; background:#e9f2ff; text-align:left; }

/* Responsive */
@media(max-width:768px){
    .sidebar { width:100%; height:auto; position:relative; }
    .main { margin-left:0; padding:20px; }
}
</style>
</head>
<body>


<div class="sidebar">
    <div class="logo">
        <img src="mclogo.png" alt="MC Logo">
    </div>

    <h4><b>DOCTOR PANEL</b></h4>

    <ul class="nav flex-column">
        <li><a href="doctordash.php"><i class="bi bi-house"></i> Dashboard</a></li>
        <li><a href="mypatients.php"><i class="bi bi-person-lines-fill"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="bi bi-calendar2-week"></i> Appointments</a></li>
        <li><a href="Reports.php"><i class="bi bi-journal-medical"></i> Reports</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <!-- <li><a href="profile.php" class="active"><i class="bi bi-person-circle"></i> My Profile</a></li> -->
    </ul>

    <!-- LOGOUT AT BOTTOM -->
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<!-- =======================
    MAIN CONTENT
======================= -->
<div class="main">
    <h2><i class="bi bi-person-circle"></i> My Profile</h2>

    <!-- Doctor Details Table -->
    <div class="doctor-card">
        <tbody>
    <tr>
        <th>User ID</th>
        <td><?= htmlspecialchars($doctor['user_id'] ?? 'N/A'); ?></td>
    </tr>
    <tr>
        <th>Doctor Name</th>
        <td><?= htmlspecialchars($doctor['Doctor_name'] ?? 'Not Assigned'); ?></td>
    </tr>
    <tr>
        <th>Specialty</th>
        <td><?= htmlspecialchars($doctor['speciality'] ?? 'Not Assigned'); ?></td>
    </tr>
    <tr>
        <th>Username</th>
        <td><?= htmlspecialchars($doctor['user_name'] ?? 'N/A'); ?></td>
    </tr>
    <tr>
        <th>Email</th>
        <td><?= htmlspecialchars($doctor['email'] ?? 'Not Assigned'); ?></td>
    </tr>
</tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>