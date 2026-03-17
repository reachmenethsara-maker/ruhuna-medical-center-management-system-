<?php
session_start();
include("../db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5){
    echo "Unauthorized";
    exit();
}

// Use GET instead of POST
if(isset($_GET['item_id'])){
    $item_id = intval($_GET['item_id']);

    if($conn->query("DELETE FROM inventory WHERE item_id=$item_id")){
        echo "<script>
                alert('Item deleted successfully ✅');
                window.location.href = 'inventorydash.php';
              </script>";
        exit();
    } else {
        $error = addslashes($conn->error);
        echo "<script>
                alert('Delete failed: $error');
                window.location.href = 'inventorydash.php';
              </script>";
        exit();
    }
}
?>