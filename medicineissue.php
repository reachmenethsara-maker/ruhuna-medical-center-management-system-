<?php
session_start();
include("../db.php");
include("insidebar.php");

// Login check (Inventory / Pharmacist role example)
if(!isset($_SESSION['user_id'])){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

$success = "";
$error = "";

// Issue medicine
if(isset($_POST['issue'])){

$item_id = intval($_POST['item_id']);
$issue_qty = intval($_POST['issue_qty']);

if($issue_qty <= 0){
$error = "Invalid quantity.";
}else{

$check = $conn->prepare("SELECT quantity,name FROM inventory WHERE item_id=?");
$check->bind_param("i",$item_id);
$check->execute();
$result = $check->get_result();

if($result->num_rows > 0){

$row = $result->fetch_assoc();
$current_stock = $row['quantity'];
$medicine_name = $row['name'];

if($issue_qty > $current_stock){

$error = "Not enough stock. Current stock: ".$current_stock;

}else{

$new_stock = $current_stock - $issue_qty;

$update = $conn->prepare("UPDATE inventory SET quantity=? WHERE item_id=?");
$update->bind_param("ii",$new_stock,$item_id);
$update->execute();

$success = $medicine_name." issued successfully! Remaining stock: ".$new_stock;

}

}else{
$error = "Medicine not found.";
}

}

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Issue Medicine</title>

<style>

body{
margin:0;
font-family:Arial;
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
}

.sidebar a:hover{
background:#495057;
border-left:4px solid #28a745;
}

/* Main Content */
.main-content{
margin-left:220px;
padding:20px;
}

/* Card */
.card{
background:white;
padding:25px;
border-radius:8px;
box-shadow:0 4px 8px rgba(0,0,0,0.1);
max-width:500px;
}

/* Form */
label{
font-weight:bold;
display:block;
margin-bottom:5px;
}

select,input{
width:100%;
padding:10px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:5px;
}

/* Button */
button{
background:#001f4d;
color:white;
border:none;
padding:12px;
width:100%;
font-size:16px;
border-radius:6px;
cursor:pointer;
}

button:hover{
background:#001f5d;
}

/* Messages */

.success{
color:green;
font-weight:bold;
margin-bottom:15px;
}

.error{
color:red;
font-weight:bold;
margin-bottom:15px;
}

</style>

</head>

<body>

<div class="main-content">

<div class="card">

<h2>Issue Medicine</h2>

<?php if($success){ echo "<div class='success'>$success</div>"; } ?>
<?php if($error){ echo "<div class='error'>$error</div>"; } ?>

<form method="POST">

<label>Select Medicine</label>

<select name="item_id" required>

<option value="">-- Select Medicine --</option>

<?php

$result = $conn->query("SELECT item_id,name,quantity FROM inventory");

while($row = $result->fetch_assoc()){

echo "<option value='".$row['item_id']."'>".
$row['name']." (Stock: ".$row['quantity'].")</option>";

}

?>

</select>

<label>Quantity</label>

<input type="number" name="issue_qty" min="1" required>

<button type="submit" name="issue">Issue Medicine</button>

</form>

</div>

</div>

</body>
</html>