<?php
session_start();
include("../db.php");

// Doctor login check
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Fetch inventory
$sql = "SELECT name, quantity FROM inventory ORDER BY name ASC";
$result = $conn->query($sql);

// Prepare data for Chart.js
$med_names = [];
$med_qty = [];
while($row = $result->fetch_assoc()){
    $med_names[] = $row['name'];
    $med_qty[] = $row['quantity'];
}

// Fetch full inventory for table
$table_sql = "SELECT name, unit, quantity, category, expire_date FROM inventory ORDER BY name ASC";
$table_result = $conn->query($table_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Medicine Stock</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Sidebar */
.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    top:0; left:0;
    background:#00008B;
    color:#fff;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:20px 0;
    box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}
.sidebar .logo { text-align:center; margin-bottom:20px; }
.sidebar .logo img { width:60px; height:60px; border-radius:50%; border:2px solid #fff; margin-bottom:10px; }
.sidebar .logo h3 { font-size:16px; color:#fff; margin:0; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { margin-bottom:10px; }
.sidebar ul li a { color:#fff; text-decoration:none; display:flex; align-items:center; padding:10px 20px; border-radius:8px; transition:.3s; }
.sidebar ul li a i { margin-right:10px; font-size:18px; }
.sidebar ul li a:hover, .sidebar ul li a.active { background:#0056b3; color:#fff; }
.sidebar .logout a { background:#ffc107; display:flex; align-items:center; padding:10px 20px; border-radius:8px; transition:.3s; color:#000; }
.sidebar .logout a:hover { background:#e0a800; color:#000; }

/* Main content */
.main { margin-left:260px; padding:30px; background:#f4f6f9; min-height:100vh; }
.main h2 { margin-bottom:25px; }

/* Table styling */
.table thead { background:#00008B; color:#fff; }
.table-hover tbody tr:hover { background:#e9f2ff; }
.badge-stock { font-size:.9em; }

/* Chart card */
.chart-card { margin-bottom:30px; padding:20px; background:#fff; border-radius:10px; box-shadow:0 3px 10px rgba(0,0,0,0.1); }

/* Responsive */
@media(max-width:768px){
    .sidebar { width:100%; height:auto; position:relative; }
    .main { margin-left:0; padding:20px; }
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        <img src="mclogo.png" alt="Logo">
        <h3>Doctor Panel</h3>
    </div>
    <ul class="menu">
        <li><a href="doctordash.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a href="mypatients.php"><i class="bi bi-people"></i> My Patients</a></li>
        <li><a href="appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a></li>
        <li><a href="reports.php"><i class="bi bi-file-earmark-medical"></i> Reports</a></li>
        <li><a href="prescription.php"class="active"><i class="bi bi-box-seam"></i> Medicine Stock</a></li>
        <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        <!-- <li><a href="profile.php"><i class="bi bi-person"></i> My Profile</a></li> -->
    </ul>
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<!-- Main Content -->
<div class="main">
    <h2><i class="bi bi-box-seam"></i> Medicine Stock</h2>

    <!-- Chart -->
    <div class="chart-card">
        <canvas id="stockChart"></canvas>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Medicine</th>
                    <th>Unit</th>
                    <th>Stock</th>
                    <th>Category</th>
                    <th>Expire Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if($table_result->num_rows > 0){
                    $count=1;
                    while($row=$table_result->fetch_assoc()){
                        $stock_class = $row['quantity'] < 10 ? 'bg-danger text-white' : 'bg-success text-white';
                        echo "<tr>
                            <td>{$count}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['unit']}</td>
                            <td><span class='badge {$stock_class} badge-stock'>{$row['quantity']}</span></td>
                            <td>{$row['category']}</td>
                            <td>".date('d M Y', strtotime($row['expire_date']))."</td>
                        </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center text-muted'>No medicines found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const ctx = document.getElementById('stockChart').getContext('2d');
const stockChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($med_names); ?>,
        datasets: [{
            label: 'Stock Quantity',
            data: <?= json_encode($med_qty); ?>,
            backgroundColor: 'rgba(0, 123, 255, 0.7)',
            borderColor: 'rgba(0, 123, 255, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive:true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Medicine Stock Overview' }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 5 }
            }
        }
    }
});
</script>
</body>
</html>