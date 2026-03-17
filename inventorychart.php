<?php
session_start();
include("../db.php");
include("insidebar.php");

// Only allow inventory/pharmacist users
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Fetch total medicines
$total_medicines_result = $conn->query("SELECT COUNT(*) as total FROM inventory");
$total_medicines = $total_medicines_result->fetch_assoc()['total'];

// Fetch low stock medicines (<50)
$low_stock_result = $conn->query("SELECT COUNT(*) as low_stock FROM inventory WHERE quantity < 50");
$low_stock = $low_stock_result->fetch_assoc()['low_stock'];

// Fetch medicine stock for bar chart
$stock_result = $conn->query("SELECT name, quantity FROM inventory");
$medicine_names = [];
$medicine_qty = [];
while($row = $stock_result->fetch_assoc()){
    $medicine_names[] = $row['name'];
    $medicine_qty[] = $row['quantity'];
}

// Fetch category-wise stock for pie chart
$category_result = $conn->query("SELECT category, SUM(quantity) as total FROM inventory GROUP BY category");
$categories = [];
$category_totals = [];
while($row = $category_result->fetch_assoc()){
    $categories[] = $row['category'];
    $category_totals[] = $row['total'];
}

// Convert to JSON for Chart.js
$names_json = json_encode($medicine_names);
$qty_json = json_encode($medicine_qty);
$cat_json = json_encode($categories);
$cat_totals_json = json_encode($category_totals);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory Dashboard Visualization</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{
    margin:0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background:#f4f6f9;
}

/* Sidebar */
.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:220px;
    height:100%;
    background:#001f4d;
    padding-top:20px;
}
.sidebar a{
    display:block;
    color:white;
    padding:12px 20px;
    text-decoration:none;
    font-weight:bold;
    margin-bottom:5px;
    border-radius:5px;
}
.sidebar a:hover{
    background:#495057;
}
.sidebar a.active{
    background:#28a745;
}

/* Main Content */
.main-content{
    margin-left:220px;
    padding:20px;
}

/* Header */
header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
header h2{
    margin:0;
    color:#333;
}
header span{
    font-size:14px;
    color:#555;
}

/* Cards */
.cards{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
    margin-bottom:30px;
}
.card{
    flex:1 1 200px;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
    text-align:center;
}
.card h3{
    margin:10px 0;
    color:#333;
}
.card p{
    font-size:24px;
    font-weight:bold;
    margin:0;
}

/* Chart container */
.chart-container{
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
    margin-bottom:30px;
}

/* Responsive */
@media(max-width:768px){
    .main-content{
        margin-left:0;
    }
    .cards{
        flex-direction:column;
    }
}
</style>
</head>
<body>



<div class="main-content">
    <header>
        <h2>Inventory Visualization</h2>
        <h1><span>Welcome, <?php echo $_SESSION['user_name']; ?> (Pharmacist)</span></h1>
    </header>

    <!-- Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Medicines</h3>
            <p><?php echo $total_medicines; ?></p>
        </div>
        <div class="card">
            <h3>Low Stock (&lt;50)</h3>
            <p><?php echo $low_stock; ?></p>
        </div>
    </div>

    <!-- Bar Chart -->
    <div class="chart-container">
        <h3>Medicine Stock</h3>
        <canvas id="stockChart"></canvas>
    </div>


<script>
// Bar Chart
var ctx = document.getElementById('stockChart').getContext('2d');
var stockChart = new Chart(ctx, {
    type:'bar',
    data:{
        labels: <?php echo $names_json; ?>,
        datasets:[{
            label:'Quantity',
            data: <?php echo $qty_json; ?>,
            backgroundColor: <?php 
                echo json_encode(array_map(function($q){
                    if($q<50) return 'rgba(220,53,69,0.7)';
                    if($q<200) return 'rgba(255,193,7,0.7)';
                    return 'rgba(40,167,69,0.7)';
                }, $medicine_qty));
            ?>,
            borderColor:'#333',
            borderWidth:1
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{display:true},
            title:{display:true,text:'Current Medicine Stock'}
        },
        scales:{
            y:{beginAtZero:true,title:{display:true,text:'Quantity'}},
            x:{title:{display:true,text:'Medicine'}}
        }
    }
});

// Pie Chart
var ctx2 = document.getElementById('categoryChart').getContext('2d');
var categoryChart = new Chart(ctx2,{
    type:'pie',
    data:{
        labels: <?php echo $cat_json; ?>,
        datasets:[{
            data: <?php echo $cat_totals_json; ?>,
            backgroundColor:[
                'rgba(40,167,69,0.7)',
                'rgba(255,193,7,0.7)',
                'rgba(220,53,69,0.7)',
                'rgba(0,123,255,0.7)',
                'rgba(108,117,125,0.7)',
                'rgba(23,162,184,0.7)',
                'rgba(255,0,255,0.7)'
            ],
            borderColor:'#fff',
            borderWidth:1
        }]
    },
    options:{
        responsive:true,
        plugins:{title:{display:true,text:'Stock by Category'}}
    }
});
</script>

</body>
</html>