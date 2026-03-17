<?php
session_start();
include("../db.php");
include('insidebar.php');

if(isset($_POST['issue'])){

$item_id = $_POST['item_id'];
$issue_qty = $_POST['issue_qty'];

$check = $conn->query("SELECT quantity FROM inventory WHERE item_id=$item_id");
$row = $check->fetch_assoc();

$current_stock = $row['quantity'];

if($issue_qty > $current_stock){

echo "Not enough stock";

}else{

$conn->query("UPDATE inventory 
SET quantity = quantity - $issue_qty 
WHERE item_id=$item_id");

echo "Medicine Issued Successfully";

}

}
?>

<h2>Issue Medicine</h2>

<form method="POST">

<label>Select Medicine</label>

<select name="item_id">

<?php

$result = $conn->query("SELECT item_id,name,quantity FROM inventory");

while($row=$result->fetch_assoc()){

echo "<option value='".$row['item_id']."'>
".$row['name']." (Stock: ".$row['quantity'].")
</option>";

}

?>

</select>

<label>Quantity</label>
<input type="number" name="issue_qty" required>

<button type="submit" name="issue">Issue Medicine</button>

</form>