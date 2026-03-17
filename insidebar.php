<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Panel Sidebar</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ----------------- Sidebar ----------------- */
.sidebar {
    position: fixed;
    width: 220px;
    height: 100%;
    background: #001f4d; /* Dark Blue background */
    padding-top: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.3s ease;
}

/* Logo / Top Section */
.sidebar .logo {
    text-align: center;
    padding: 20px 10px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}
.sidebar .logo img {
    max-width: 100px;
    margin-bottom: 10px;
}
.sidebar .logo h4 {
    color: #fff;
    font-weight: bold;
    margin: 0;
}

/* Navigation Links */
.sidebar .nav {
    margin-top: 20px;
    flex-grow: 1;
}
.sidebar .nav li {
    list-style: none;
}
.sidebar .nav li a {
    display: block;
    color: #ffffffcc; /* slightly lighter for inactive */
    padding: 12px 25px;
    text-decoration: none;
    font-weight: 500;
    border-left: 4px solid transparent;
    border-radius: 0 20px 20px 0;
    transition: all 0.3s ease;
}
.sidebar .nav li a:hover {
    background: #002766; /* lighter blue on hover */
    color: #fff;
    border-left: 4px solid #28a745; /* green highlight on hover */
}
.sidebar .nav li a.active {
    background: #0d6efd; /* bright blue for active page */
    color: #fff;
    border-left: 4px solid #0b5ed7;
}

/* Logout Button */
.sidebar .logout {
    padding: 15px;
    margin-top: auto;
}
.sidebar .logout a {
    display: block;
    background: #dc3545; /* red logout button */
    color: #fff;
    text-align: center;
    padding: 12px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.sidebar .logout a:hover {
    background: #bd2130;
}

/* Body content shift */
body {
    margin-left: 220px;
    transition: margin-left 0.3s ease;
}

/* Responsive */
@media(max-width:768px){
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    body {
        margin-left: 0;
    }
}
</style>
</head>
<body>

<div class="sidebar">
    <!-- Logo -->
    <div class="logo">
        <img src="logo.png" alt="Doctor Panel Logo"> <!-- Replace with your logo -->
        <h4>Pharmasist Panel</h4>
    </div>

    <!-- Navigation Links -->
    <ul class="nav flex-column">
        <li><a href="inventorychart.php" class="<?= ($current_page=='inventorychart.php') ? 'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="inventorydash.php" class="<?= ($current_page=='inventorydash.php') ? 'active':'' ?>"><i class="bi bi-plus-square"></i> Add Medicine</a></li>
        <li><a href="medicineissue.php" class="<?= ($current_page=='medicineissue.php') ? 'active':'' ?>"><i class="bi bi-box-arrow-up"></i> Issue Medicine</a></li>
        <li><a href="settings.php" class="<?= ($current_page=='settings.php') ? 'active':'' ?>"><i class="bi bi-gear"></i> Settings</a></li>
    </ul>

    <!-- Logout -->
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

</body>
</html>