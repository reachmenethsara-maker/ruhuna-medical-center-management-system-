<?php
session_start();
include("../db.php");

// // Only allow admin (role_id = 1)
// if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
//     header("Location: ../loginpage/loginpage.php");
//     exit();
// }

/* ---------------- ADD PHARMACIST ---------------- */
if(isset($_POST['add_pharmacist'])){
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $address    = trim($_POST['address']);
    $phone      = trim($_POST['phone']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role_id = 5; // Pharmacist role

    // Insert into user table
    $stmt = $conn->prepare("INSERT INTO user (user_name,password,role_id) VALUES (?,?,?)");
    $stmt->bind_param("ssi",$username,$hashed_password,$role_id);
    $stmt->execute();
    $user_id = $stmt->insert_id;

    // Insert into pharmacist table
    $stmt2 = $conn->prepare("INSERT INTO pharmacist (user_id,first_name,last_name,email,address,phone) VALUES (?,?,?,?,?,?)");
    $stmt2->bind_param("isssss",$user_id,$first_name,$last_name,$email,$address,$phone);
    $stmt2->execute();

    header("Location: add_pharmacist.php");
    exit;
}

/* ---------------- EDIT PHARMACIST ---------------- */
$edit_pharmacist = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT pharmacist.*, user.user_name FROM pharmacist JOIN user ON pharmacist.user_id=user.user_id WHERE pharmacist_id='$id'");
    $edit_pharmacist = $res->fetch_assoc();
}

if(isset($_POST['update_pharmacist'])){
    $id         = $_POST['pharmacist_id'];
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $address    = trim($_POST['address']);
    $phone      = trim($_POST['phone']);
    $username   = trim($_POST['username']);
    $password   = trim($_POST['password']);

    // Update user table
    if($password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE user SET user_name=?, password=? WHERE user_id=(SELECT user_id FROM pharmacist WHERE pharmacist_id=?)");
        $stmt->bind_param("ssi", $username, $hashed_password, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("UPDATE user SET user_name=? WHERE user_id=(SELECT user_id FROM pharmacist WHERE pharmacist_id=?)");
        $stmt->bind_param("si", $username, $id);
        $stmt->execute();
    }

    // Update pharmacist table
    $stmt2 = $conn->prepare("UPDATE pharmacist SET first_name=?, last_name=?, email=?, address=?, phone=? WHERE pharmacist_id=?");
    $stmt2->bind_param("sssssi",$first_name,$last_name,$email,$address,$phone,$id);
    $stmt2->execute();

    header("Location: add_pharmacist.php");
    exit;
}

/* ---------------- DELETE PHARMACIST ---------------- */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM pharmacist WHERE pharmacist_id='$id'");
    header("Location: add_pharmacist.php");
    exit;
}

/* ---------------- FETCH PHARMACISTS ---------------- */
$result = $conn->query("SELECT pharmacist.*,user.user_name FROM pharmacist JOIN user ON pharmacist.user_id=user.user_id ORDER BY pharmacist_id DESC");

?>

<!DOCTYPE html>
<html>
<head>
<title>Pharmacist Management</title>
<link rel="stylesheet" href="admindash.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body{font-family:'Poppins',sans-serif; background:#f4f6f9;}
.container{width:95%; max-width:1200px; margin:20px auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 12px rgba(0,0,0,0.1);}
h2{text-align:center; color:#0d6efd;}
.top{display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;}
.top button{background:#0d6efd; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;}
#addForm{display:none; background:#f8f9fa; padding:20px; border-radius:10px; margin-bottom:20px;}
#addForm input{width:100%; padding:10px; margin:6px 0; border-radius:6px; border:1px solid #ccc;}
#addForm button{background:#198754; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer;}
table{width:100%; border-collapse:collapse;}
table th, table td{padding:12px; text-align:center; border-bottom:1px solid #ddd;}
table th{background:#0d6efd; color:white;}
table tr:nth-child(even){background:#f8f9fa;}
.edit-btn{background:#0d6efd; color:white; padding:5px 10px; border-radius:5px; text-decoration:none;}
.delete-btn{background:#dc3545; color:white; padding:5px 10px; border-radius:5px; text-decoration:none;}
</style>
</head>
<body>

<?php include("sidebar.php"); ?>
<div class="main">
<?php include("topbar.php"); ?>

<div class="container">
<h2>Pharmacist Management</h2>

<div class="top">
<button onclick="toggleAddForm()">+ Add Pharmacist</button>
</div>

<!-- ADD / EDIT FORM -->
<div id="addForm" style="<?= $edit_pharmacist ? 'display:block;' : '' ?>">
<form method="POST">
<input type="hidden" name="pharmacist_id" value="<?= $edit_pharmacist['pharmacist_id'] ?? '' ?>">
<input type="text" name="first_name" placeholder="First Name" required value="<?= $edit_pharmacist['first_name'] ?? '' ?>">
<input type="text" name="last_name" placeholder="Last Name" required value="<?= $edit_pharmacist['last_name'] ?? '' ?>">
<input type="email" name="email" placeholder="Email" required value="<?= $edit_pharmacist['email'] ?? '' ?>">
<input type="text" name="address" placeholder="Address" value="<?= $edit_pharmacist['address'] ?? '' ?>">
<input type="text" name="phone" placeholder="Phone" required value="<?= $edit_pharmacist['phone'] ?? '' ?>">
<input type="text" name="username" placeholder="Username" required value="<?= $edit_pharmacist['user_name'] ?? '' ?>">
<input type="password" name="password" placeholder="Password ">
<button type="submit" name="<?= $edit_pharmacist ? 'update_pharmacist' : 'add_pharmacist' ?>">
<?= $edit_pharmacist ? 'Update Pharmacist' : 'Add Pharmacist' ?>
</button>
</form>
</div>

<!-- TABLE -->
<table>
<tr>
<th>ID</th>
<th>First Name</th>
<th>Last Name</th>
<th>Email</th>
<th>Phone</th>
<th>Username</th>
<th>Actions</th>
</tr>
<?php while($row = $result->fetch_assoc()){ ?>
<tr>
<td><?= $row['pharmacist_id'] ?></td>
<td><?= $row['first_name'] ?></td>
<td><?= $row['last_name'] ?></td>
<td><?= $row['email'] ?></td>
<td><?= $row['phone'] ?></td>
<td><?= $row['user_name'] ?></td>
<td>
<a class="edit-btn" href="?edit=<?= $row['pharmacist_id'] ?>">Edit</a>
<a class="delete-btn" href="?delete=<?= $row['pharmacist_id'] ?>" onclick="return confirm('Delete this pharmacist?')">Delete</a>
</td>
</tr>
<?php } ?>
</table>

</div>
</div>

<script>
function toggleAddForm(){
    var form = document.getElementById("addForm");
    form.style.display = (form.style.display==="none") ? "block" : "none";
}
</script>

</body>
</html>