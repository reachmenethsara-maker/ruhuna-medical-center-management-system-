<?php
session_start();
include("../db.php");
include("insidebar.php");

// Only inventory managers (role_id = 5)
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

// Check if item ID is passed
if(!isset($_GET['id'])){
    header("Location: inventory_dashboard.php");
    exit();
}

$item_id = intval($_GET['id']);
$success = '';
$error = '';

// Fetch the item safely
$stmt = $conn->prepare("SELECT * FROM inventory WHERE item_id=?");
$stmt->bind_param("i",$item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

// If item not found, set error
if(!$item){
    $error = "Item not found!";
}

// Medicine categories
$categories = ['Painkiller','Antibiotic','Vitamin','Syrup','Capsule','Tablet','Injection'];

// Handle form submission
if(isset($_POST['edit_medicine'])){

    $name = trim($_POST['name']);
    $unit = trim($_POST['unit']);
    $quantity = intval($_POST['quantity']);
    $category = trim($_POST['category']);
    $expire_date = $_POST['expire_date'];

    $stmt = $conn->prepare("UPDATE inventory SET name=?, unit=?, quantity=?, category=?, expire_date=? WHERE item_id=?");
    $stmt->bind_param("ssissi", $name, $unit, $quantity, $category, $expire_date, $item_id);

    if($stmt->execute()){
        header("Location: edit_medicine.php?id=$item_id&updated=1");
        exit();
    } else {
        $error = "Update failed!";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Medicine</title>
    <style>
        body{margin:0;font-family:Arial;background:#f4f6f9;}
        .main-content{margin-left:220px;padding:25px;}
        .card{background:#fff;padding:25px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.08);max-width:550px;}
        .card h2{margin-top:0;color:#333;}
        label{font-weight:bold;display:block;margin-bottom:6px;}
        input,select{width:100%;padding:10px;margin-bottom:15px;border-radius:6px;border:1px solid #ccc;font-size:14px;}
        .btn{padding:12px 18px;border:none;border-radius:6px;cursor:pointer;font-size:15px;}
        .update-btn{background:#28a745;color:white;}
        .update-btn:hover{background:#218838;}
        .cancel-btn{background:#6c757d;color:white;text-decoration:none;padding:12px 18px;border-radius:6px;margin-left:10px;}
        .cancel-btn:hover{background:#5a6268;}
        .success{color:green;margin-bottom:15px;font-weight:bold;}
        .error{color:red;margin-bottom:15px;font-weight:bold;}
        @media(max-width:768px){.main-content{margin-left:0;}}
    </style>
</head>
<body>

<div class="main-content">

<div class="card">

<h2>Edit Medicine</h2>

<?php if($success) echo "<div class='success'>".htmlspecialchars($success)."</div>"; ?>
<?php if($error) echo "<div class='error'>".htmlspecialchars($error)."</div>"; ?>

<?php if($item): ?>
<form method="POST">

<label>Medicine Name</label>
<input type="text" name="name" value="<?= htmlspecialchars($item['name']); ?>" required>

<label>Unit</label>
<input type="text" name="unit" value="<?= htmlspecialchars($item['unit']); ?>" required>

<label>Quantity</label>
<input type="number" name="quantity" min="0" value="<?= htmlspecialchars($item['quantity']); ?>" required>

<label>Category</label>
<select name="category">
<?php foreach($categories as $cat): 
    $selected = ($item['category'] == $cat) ? 'selected' : '';
?>
<option value="<?= htmlspecialchars($cat); ?>" <?= $selected; ?>><?= htmlspecialchars($cat); ?></option>
<?php endforeach; ?>
</select>

<label>Expire Date</label>
<input type="date" name="expire_date" value="<?= htmlspecialchars($item['expire_date']); ?>">

<button type="submit" name="edit_medicine" class="btn update-btn">
Update Medicine
</button>

<a href="inventory_dashboard.php" class="cancel-btn">Cancel</a>

</form>
<?php else: ?>
<p class="error">Item not found!</p>
<?php endif; ?>

</div>
</div>

<?php
// Simple popup alert after update
if(isset($_GET['updated']) && $_GET['updated']==1){
    echo "<script>alert('Medicine updated successfully ✅');</script>";
}
?>

</body>
</html>