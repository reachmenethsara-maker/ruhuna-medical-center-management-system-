<?php session_start();
include "../db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    header("Location: ../loginpage/loginpage.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT patient_id,first_name,last_name FROM patient WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$patient_name = $row['first_name'] . " " . $row['last_name'];

// ------------------ Get patient details ------------------
$stmt = $conn->prepare("SELECT * FROM patient WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$patient_result = $stmt->get_result();
$patient = $patient_result->fetch_assoc();

// ------------------ Get medical records ------------------
// Also join doctor table to get Doctor_name
$records_stmt = $conn->prepare("
    SELECT mr.record_id, mr.visit_date, mr.diagnosis, mr.prescription, mr.file_path, d.Doctor_name
    FROM medical_record mr
    LEFT JOIN doctor d ON mr.doctor_id = d.user_id
    WHERE mr.patient_id = ?
    ORDER BY mr.visit_date DESC
");
$records_stmt->bind_param("i", $patient['patient_id']);
$records_stmt->execute();
$records_result = $records_stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Patient Profile & Medical Records</title>
    <link rel="stylesheet" href="patient_style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #f4f6f9;
            font-family: Arial;
        }

        .header {
            background: #1e3a8a;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 26px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-title {
            font-weight: bold;
            color: #1e3a8a;
        }
    </style>
</head>

<body>

    <?php include("sidebar.php"); ?>
    <div class="main">
        <?php include("topbar.php"); ?>

        <div class="container-fluid p-4">
            <div class="container mt-4">
                <div class="card p-4">
                    <div class="header">Patient Profile & Medical Records</div>

                    <div class="container mt-4">
                        <div class="card p-4">
                            <h3><?php echo $patient['first_name'] . " " . $patient['last_name']; ?></h3>

                            <table class="table table-bordered mt-3">
                                <tr>
                                    <th class="profile-title">Patient ID</th>
                                    <td><?php echo $patient['patient_id']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Gender</th>
                                    <td><?php echo $patient['gender']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Date of Birth</th>
                                    <td><?php echo $patient['date_of_birth']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Phone</th>
                                    <td><?php echo $patient['phone']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Email</th>
                                    <td><?php echo $patient['email']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Patient Type</th>
                                    <td><?php echo $patient['patient_type']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Blood Type</th>
                                    <td><?php echo $patient['blood_type']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Academic Year</th>
                                    <td><?php echo $patient['academic_yr']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Faculty</th>
                                    <td><?php echo $patient['faculty']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Accommodation Type</th>
                                    <td><?php echo $patient['accomodation_type']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Medical History</th>
                                    <td><?php echo $patient['medical_history']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Surgical History</th>
                                    <td><?php echo $patient['surgical_history']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Family History</th>
                                    <td><?php echo $patient['family_history']; ?></td>
                                </tr>
                                <tr>
                                    <th class="profile-title">Marital Status</th>
                                    <td><?php echo $patient['marital_status']; ?></td>
                                </tr>
                            </table>

                            <h4 class="mt-4">Medical Records</h4>
                            <table class="table table-bordered mt-2">
                                <tr>
                                    <th>Visit Date</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Prescription</th>
                                    <th>File</th>
                                </tr>
                                <?php if ($records_result && $records_result->num_rows > 0) { ?>
                                    <?php while ($record = $records_result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $record['visit_date']; ?></td>
                                            <td><?php echo $record['Doctor_name'] ?? "Unknown"; ?></td>
                                            <td><?php echo $record['diagnosis']; ?></td>
                                            <td><?php echo $record['prescription']; ?></td>
                                            <td>
                                                <?php
                                                if (!empty($record['file_path'])) {
                                                    ?>
                                                    <a href="../Doctor_Panel/<?php echo $record['file_path']; ?>"
                                                        target="_blank">View File</a>
                                                <?php
                                                } else {
                                                    echo "No File";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No medical records found.</td>
                                    </tr>
                                <?php } ?>
                            </table>

                            <a href="patientdash.php" class="btn btn-primary mt-2">Back to Dashboard</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</body>

</html>