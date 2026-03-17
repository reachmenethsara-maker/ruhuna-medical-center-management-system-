<?php
session_start();
include 'db.php'; // your database connection file

// Check if admin is logged in
if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1){
    die("Access Denied. Only Admins can add patients.");
}

// Check if form is submitted
$success_message = '';
$error_message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $patient_type = $_POST['patient_type'];
    $blood_type = $_POST['blood_type'];
    $academic_yr = $_POST['academic_yr'];
    $faculty = $_POST['faculty'];
    $accomodation_type = $_POST['accomodation_type'];
    $marital_status = $_POST['marital_status'];
    $medical_history = $_POST['medical_history'];
    $surgical_history = $_POST['surgical_history'];
    $family_history = $_POST['family_history'];

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("
        INSERT INTO patients
        (first_name,last_name,gender,date_of_birth,phone,email,
        patient_type,blood_type,academic_yr,faculty,accomodation_type,
        marital_status,medical_history,surgical_history,family_history)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "sssssssssssssss",
        $first_name,$last_name,$gender,$date_of_birth,$phone,$email,
        $patient_type,$blood_type,$academic_yr,$faculty,$accomodation_type,
        $marital_status,$medical_history,$surgical_history,$family_history
    );

    if($stmt->execute()){
        $success_message = "✅ Patient Added Successfully!";
    }else{
        $error_message = "❌ Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Patient</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 500px;
            margin: 40px auto;
        }

        form {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }

        form label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 9px 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            height: 80px;
            resize: vertical;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        input[type="submit"]:hover {
            background: #0b5ed7;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .success {
            color: #198754;
        }

        .error {
            color: #dc3545;
        }

        @media (max-width: 600px) {
            .container {
                width: 90%;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align:center;color:#0d6efd;">Add New Patient</h2>

    <?php
    if($success_message){
        echo "<div class='message success'>$success_message</div>";
    }
    if($error_message){
        echo "<div class='message error'>$error_message</div>";
    }
    ?>

    <form method="POST" action="">
        <label>First Name:</label>
        <input type="text" name="first_name" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" required>

        <label>Gender:</label>
        <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label>Date of Birth:</label>
        <input type="date" name="date_of_birth">

        <label>Phone:</label>
        <input type="text" name="phone">

        <label>Email:</label>
        <input type="email" name="email">

        <label>Patient Type:</label>
        <input type="text" name="patient_type">

        <label>Blood Type:</label>
        <input type="text" name="blood_type">

        <label>Academic Year:</label>
        <input type="text" name="academic_yr">

        <label>Faculty:</label>
        <input type="text" name="faculty">

        <label>Accommodation Type:</label>
        <input type="text" name="accomodation_type">

        <label>Marital Status:</label>
        <select name="marital_status">
            <option value="Single">Single</option>
            <option value="Married">Married</option>
        </select>

        <label>Medical History:</label>
        <textarea name="medical_history"></textarea>

        <label>Surgical History:</label>
        <textarea name="surgical_history"></textarea>

        <label>Family History:</label>
        <textarea name="family_history"></textarea>

        <input type="submit" value="Add Patient">
    </form>
</div>

</body>
</html>