<?php
// Sidebar for Doctor Dashboard
?>
<div class="sidebar">
    <!-- Logo Section -->
    <div class="logo">
        <img src="mclogo.png" alt="MC Logo">
        <h2>Doctor Panel</h2>
    </div>

    <!-- Menu -->
    <ul class="menu">
        <li><a href="doctordash.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="mypatients.php"><i class="fas fa-users"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
        <li><a href="reports.php"><i class="fas fa-file-medical-alt"></i> Reports</a></li>
        <li><a href="prescription.php"><i class="fas fa-pills"></i> Medicine Stock</a></li>
        <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>
    </ul>

    <!-- Logout at bottom -->


<style>
/* Sidebar container */
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #1c1c1c;
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.5);
    z-index: 100;
}

/* Logo */
.sidebar .logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 8px;
}

.sidebar .logo img {
    width: 70px;
    height: auto;
    border-radius: 50%;
    border: 2px solid #00bfff;
    margin-bottom: 10px;
}

.sidebar .logo h2 {
    font-size: 15px;
    color: #00bfff;
    text-align: center;
    margin: 0;
}

/* Menu */
.sidebar ul.menu {
    list-style: none;
    padding: 0;
    width: 100%;
}

.sidebar ul.menu li {
    width: 100%;
}

.sidebar ul.menu li a {
    display: flex;
    align-items: center;
    padding: 0;
    color: #fff;
    text-decoration: none;
    font-size: 15px;
    transition: 0.3s;
}

.sidebar ul.menu li a i {
    margin-right: 10px;
    font-size: 18px;
    width: 20px;
}

/* Hover effect */
.sidebar ul.menu li a:hover {
    background-color: #00bfff;
    color: #1c1c1c;
    border-radius: 8px;
}

/* Logout */
.sidebar .logout {
    padding: 20px;
}

.sidebar .logout a {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    background-color: #e74c3c;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar .logout a i {
    margin-right: 10px;
}

.sidebar .logout a:hover {
    background-color: #c0392b;
}

/* Responsive Main Content Adjustment */
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

.main {
    margin-left: 250px; /* same as sidebar width */
    padding: 20px;
}
</style>