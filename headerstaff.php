<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include('../db.php');

$staff_user_id = $_SESSION['user_id'] ?? 0;

// Fetch staff name
$stmt = $conn->prepare("SELECT staff_name FROM staff WHERE user_id = ?");
$stmt->bind_param("i", $staff_user_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$staff_name = $staff['staff_name'] ?? $_SESSION['user_name'] ?? 'Staff';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Sidebar */
.sidebar {
    position: fixed;
    left: 0; top: 0;
    width: 220px;
    height: 100%;
    background: rgba(15,23,42,0.95);
    color: #fff;
    backdrop-filter: blur(12px);
    transition: width 0.3s;
    overflow: hidden;
}
.sidebar.collapsed { width: 70px; }
.sidebar .logo {
    display: flex; align-items: center; gap: 10px;
    padding: 15px 20px; background: rgba(13,110,253,0.95);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    border-radius: 0 0 12px 12px;
}
.sidebar .logo img { width: 45px; height: 45px; border-radius: 8px; object-fit: cover; border:2px solid #fff; }
.sidebar .logo h2 { font-size: 20px; font-weight:700; margin:0; color:#fff; }
.sidebar ul { list-style:none; padding:0; margin:0; }
.sidebar ul li a {
    display:flex; align-items:center; gap:12px; padding:15px 20px; color:#fff;
    text-decoration:none; font-size:15px; transition:0.3s;
}
.sidebar ul li a:hover { background: rgba(13,110,253,0.8); }
.sidebar ul li a i { width:20px; text-align:center; }
.sidebar ul li.logout a { position:absolute; bottom:0; width:100%; border-top:1px solid rgba(255,255,255,0.2); }

/* Main content */
.main { margin-left:220px; padding:20px; transition: margin-left 0.3s; }
.sidebar.collapsed ~ .main { margin-left:70px; }

/* Topbar */
.topbar {
    display:flex; justify-content:space-between; align-items:center;
    padding:15px 20px; background:#fff; border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-bottom:20px;
}
.topbar h3 { margin:0; color:#0d6efd; }
.topbar .user h3 { margin:0; font-size:16px; font-weight:500; color:#333; }
.topbar .top-right { display:flex; align-items:center; gap:15px; }
.add-user-btn { background:#28a745; color:#fff; padding:8px 14px; border-radius:8px; font-size:14px; text-decoration:none; transition:0.3s; }
.add-user-btn:hover { background:#218838; }
.admin-name { display:flex; align-items:center; gap:6px; font-weight:500; color:#0d6efd; }

/* Toggle */
.toggle-btn { position:absolute; top:15px; right:-15px; background:#0d6efd; border-radius:50%; padding:8px; cursor:pointer; color:#fff; font-size:14px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
.toggle-btn:hover { background:#0b5ed7; }

</style>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="mc.png" alt="Hospital Logo">
        <h2>Staff</h2>
        <div class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
    </div>
      <ul>
        <li><a href="staffdash.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
        <li><a href="staffmangepatient.php"><i class="fa-solid fa-user"></i> Patient Manage</a></li>
        <li><a href="doctor mange.php"><i class="fa-solid fa-user-doctor"></i> Doctor Manage</a></li>
        <li><a href="doctor_availability.php"><i class="fa-solid fa-clock"></i> Doctor Availability</a></li>
        <li><a href="appointment_mange.php"><i class="fa-solid fa-calendar-check"></i> Appointment Manage</a></li>
        <li><a href="add_pharmacist.php"><i class="fa-solid fa-pills"></i> Pharmacist Manage</a></li>
        <li><a href="staffsettings.php"><i class="fa-solid fa-key"></i> Change Password</a></li>
        <li class="logout.php"><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>
</div>

<div class="main">
    <div class="topbar">
        <h3>Hospital Staff Dashboard</h3>
        <div class="top-right">
            <div class="user"><h3>Welcome <?= htmlspecialchars($staff_name); ?></h3></div>
            <a href="useradd.php" class="add-user-btn"><i class="fa-solid fa-user-plus"></i> Add User</a>
            <span class="admin-name"><i class="fa-solid fa-user"></i> Staff</span>
        </div>
    </div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('collapsed');
}
</script>