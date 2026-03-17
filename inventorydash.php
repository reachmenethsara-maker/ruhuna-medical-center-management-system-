<?php
session_start();
include("../db.php");

// Sidebar include
include('insidebar.php');  // <-- path according to your folder structure

// ----------------- Ensure Pharmacist Login -----------------
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Session info
$user_id_session = $_SESSION['user_id'];
$username = $_SESSION['user_name'];

date_default_timezone_set("Asia/Colombo");
$today = date('Y-m-d');
$low_stock_limit = 200; // Low stock threshold
$out_of_stock_limit = 10; // Out of stock threshold

$success = '';
$error = '';

// Predefined categories
$categories = ['Painkiller','Antibiotic','Vitamin','Syrup','Capsule','Tablet','Injection'];

// ----------------- Add Medicine -----------------
if(isset($_POST['add_medicine'])){
    $name = trim($_POST['name']);
    $unit = trim($_POST['unit']);
    $quantity = intval($_POST['quantity']);
    $category = trim($_POST['category']);
    $expire_date = $_POST['expire_date'];
    $user_id = $user_id_session;

    if(empty($name) || empty($unit) || empty($quantity)){
        $error = "Please fill all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO inventory (name, unit, quantity, category, expire_date, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssissi",$name,$unit,$quantity,$category,$expire_date,$user_id);
        if($stmt->execute()){
            $success = "Medicine added successfully!";
        } else {
            $error = "Error adding medicine: ".$conn->error;
        }
        $stmt->close();
    }
}

// ----------------- Delete Medicine -----------------
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM inventory WHERE item_id=$id");
    header("Location: inventory_dashboard.php");
    exit();
}

// ----------------- Search -----------------
$search = '';
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
}

// ----------------- Fetch Inventory -----------------
$sql = "SELECT i.*, u.user_name,
        CASE 
            WHEN i.quantity <= $out_of_stock_limit THEN 'Out of Stock'
            WHEN i.quantity < $low_stock_limit THEN 'Low Stock'
            WHEN i.expire_date < '$today' THEN 'Expired'
            ELSE 'Available'
        END AS status
        FROM inventory i
        LEFT JOIN user u ON i.user_id = u.user_id";

if($search){
    $sql .= " WHERE i.name LIKE '%$search%' OR i.category LIKE '%$search%' OR u.user_name LIKE '%$search%'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory Dashboard</title>
<style>
/* ----------------- Sidebar ----------------- */
body { margin: 0; font-family: Arial, sans-serif; background: #f4f6f9; }
.sidebar { position: fixed; width: 220px; height: 100%; background: #001f4d; padding-top: 20px; }
.sidebar a { display: block; color: #fff; padding: 12px 20px; text-decoration: none; font-weight: bold; }
.sidebar a:hover { background: #0575e6; border-left: 4px solid #28a745; }
.sidebar a.active { background: #28a745; border-left: 4px solid #218838; }

/* ----------------- Main Content ----------------- */
.main-content { margin-left: 220px; padding: 20px; }

/* ----------------- Cards / Forms ----------------- */
.card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px; }
form label { display: block; margin-bottom: 5px; font-weight: bold; }
form input, form select { width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
button { background: #001f4d; color: #fff; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; }
button:hover { background: #218838; }

/* ----------------- Table ----------------- */
table { width: 100%; border-collapse: collapse; margin-top: 15px; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 14px; }
th { background: #f2f2f2; }
.low { background: #fff3cd; }
.out { background: #f8d7da; }
.expired { background: #d6d8d9; }
.action-btn { padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px; font-size: 12px; }
.edit-btn { background: #007bff; color: #fff; }
.delete-btn { background: #dc3545; color: #fff; }
.edit-btn:hover { background: #0069d9; }
.delete-btn:hover { background: #c82333; }
.search-box { padding: 8px; width: 250px; margin-right: 10px; }

/* ----------------- Success / Error ----------------- */
.success { color: green; font-weight: bold; margin-bottom: 15px; text-align: center; }
.error { color: red; font-weight: bold; margin-bottom: 15px; text-align: center; }

/* ----------------- Responsive ----------------- */
@media(max-width:768px){ .sidebar{width:100%;position:relative;} .main-content{margin-left:0;} }
</style>

</head>
<body>



<div class="main-content">
    <div class="card">
        <h2>Add New Medicine</h2>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">
            <label>Name*</label><input type="text" name="name" required>
            <label>Unit*</label><input type="text" name="unit" required>
            <label>Quantity*</label><input type="number" name="quantity" min="0" required>
            <label>Category</label>
            <select name="category" required>
                <option value="">--Select Category--</option>
                <?php foreach($categories as $cat){ echo "<option value='$cat'>$cat</option>"; } ?>
            </select>
            <label>Expire Date</label><input type="date" name="expire_date" min="<?php echo $today; ?>">
            <button type="submit" name="add_medicine">Add Medicine</button>
        </form>
    </div>

    <div class="card">
        <h2>Inventory List</h2>
        <form method="GET">
            <input type="text" name="search" class="search-box" placeholder="Search by name, category, added by" value="<?php echo $search; ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr>
                <th>Name</th>
                <th>Unit</th>
                <th>Quantity</th>
                <th>Category</th>
                <th>Expire Date</th>
                <th>Status</th>
                <th>Added By</th>
                <th>Actions</th>
            </tr>
            <?php
            if($result->num_rows > 0){
                while($row=$result->fetch_assoc()){
                    $class='';
                    if($row['status']=='Low Stock') $class='low';
                    if($row['status']=='Out of Stock') $class='out';
                    if($row['status']=='Expired') $class='expired';
                    echo "<tr class='$class'>
                        <td>".$row['name']."</td>
                        <td>".$row['unit']."</td>
                        <td>".$row['quantity']."</td>
                        <td>".$row['category']."</td>
                        <td>".$row['expire_date']."</td>
                        <td>".$row['status']."</td>
                        <td>".($row['user_name'] ?? 'Unknown')."</td>
                        <td>
                            <a href='edit_medicine.php?id=".$row['item_id']."' class='action-btn edit-btn'>Edit</a>
                            <a href='delete_medicine.php?item_id=".$row['item_id']."' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No Items Found</td></tr>";
            }
            ?>
        </table>
    </div>
</div>

</body>
</html>