<?php
session_start();
include "../db.php";

// Assume patient_id stored in session after login
$patient_id = $_SESSION['patient_id'];

if(isset($_POST['book'])){

    $doctor_id = $_POST['doctor_id'];
    $requested_date = $_POST['requested_date'];
    $preferred_time = $_POST['preferred_time'];

    $unique_num = rand(1000,9999);

    $sql = "INSERT INTO appointment 
            (patient_id, doctor_id, requested_date, preferred_time, confirmation_status, unique_num)
            VALUES 
            ('$patient_id','$doctor_id','$requested_date','$preferred_time','Pending','$unique_num')";

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('Appointment Requested Successfully');</script>";
    }else{
        echo "Error: ".mysqli_error($conn);
    }
}

// Fetch doctors
$doctors = mysqli_query($conn,"SELECT * FROM doctor");
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Appointment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

<h2>Book Doctor Appointment</h2>

<form method="POST">

<div class="mb-3">
<label>Select Doctor</label>
<select name="doctor_id" class="form-control" required>

<option value="">-- Select Doctor --</option>

<?php while($doc = mysqli_fetch_assoc($doctors)){ ?>

<option value="<?= $doc['doctor_id']; ?>">
<?= $doc['Doctor_name']; ?>
</option>

<?php } ?>

</select>
</div>

<div class="mb-3">
<label>Appointment Date</label>
<input type="date" name="requested_date" class="form-control" required>
</div>

<div class="mb-3">
<label>Preferred Time</label>
<input type="time" name="preferred_time" class="form-control" required>
</div>

<button type="submit" name="book" class="btn btn-primary">
Book Appointment
</button>

</form>

</div>

</body>
</html>