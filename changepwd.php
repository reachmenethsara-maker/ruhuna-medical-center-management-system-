<?php
session_start();
include "../db.php";

// Ensure only patients can access
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch current user info
$stmt = $conn->prepare("SELECT user_name FROM user WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if(isset($_POST['update_account'])){

    $new_user_name = trim($_POST['user_name']);
    $old_password  = trim($_POST['old_password']);
    $new_password  = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if($new_password !== $confirm_password){
        $message = "New Password and Confirm Password do not match!";
    } else {

        // Check old password
        $stmt = $conn->prepare("SELECT password FROM user WHERE user_id=?");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user_pass = $res->fetch_assoc();

        if($user_pass && password_verify($old_password, $user_pass['password'])){

            $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);

            // Update username + password
            $update = $conn->prepare("UPDATE user SET user_name=?, password=? WHERE user_id=?");
            $update->bind_param("ssi", $new_user_name, $new_hashed, $user_id);
            if($update->execute()){
                $message = "Account updated successfully!";
                $_SESSION['user_name'] = $new_user_name; // Update session
            } else {
                $message = "Database error: Could not update account!";
            }

        } else {
            $message = "Old password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Account</title>
<link rel="stylesheet" href="patient_style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{background:#f2f2f2;}
.card-box{
max-width:450px;
margin:80px auto;
padding:30px;
background:white;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
</style>
</head>

<body>
<?php include("sidebar.php"); ?>

<div class="card-box">

<h3 class="text-center mb-3">Update Account</h3>

<?php if($message){ ?>
<div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
<?php } ?>

<form method="POST">

<input type="text" name="user_name" class="form-control mb-3" 
placeholder="Username" value="<?= htmlspecialchars($user['user_name']); ?>" required>

<input type="password" name="old_password" class="form-control mb-3"
placeholder="Old Password" required>

<input type="password" name="new_password" class="form-control mb-3"
placeholder="New Password" required>

<input type="password" name="confirm_password" class="form-control mb-3"
placeholder="Confirm New Password" required>

<button type="submit" name="update_account" class="btn btn-primary w-100">
Update Account
</button>

</form>

</div>
</body>
</html>