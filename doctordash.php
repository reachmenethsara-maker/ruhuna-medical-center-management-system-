<?php
session_start();
include('../db.php');

// Doctor login check
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../loginpage/loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// /* =========================
//    Check Doctor Profile
// ========================= */

// $doc = $conn->prepare("SELECT * FROM user WHERE user_id=?");
// $doc->bind_param("i", $user_id);
// $doc->execute();
// $result = $doc->get_result();
// $row = $result->fetch_assoc();

// $show_popup = false;

// if (
//     $row['email'] == NULL &&
//     $row['first_name'] == NULL &&
//     $row['last_name'] == NULL &&
//     $row['gender'] == NULL &&
//     $row['NIC'] == NULL &&
//     $row['DOB'] == NULL &&
//     $row['address'] == NULL &&
//     $row['age'] == NULL &&
//     $row['contact_num'] == NULL
// ) {
//     $show_popup = true;


/* =========================
   Get Doctor Details
========================= */

$stmt = $conn->prepare("SELECT Doctor_name, speciality FROM doctor WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();

$doctor_name = $doctor['Doctor_name'] ?? $_SESSION['user_name'];
$doctor_speciality = $doctor['speciality'] ?? 'Not assigned';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /*======================
    SIDEBAR
=======================*/
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #00008B;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }

        /* Logo section */
        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .logo img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-bottom: 10px;
        }

        .sidebar .logo h2 {
            font-size: 16px;
            color: #fff;
            margin: 0;
        }

        /* Menu links */
        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            transition: .3s;
        }

        .sidebar ul li a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #0056b3;
            color: #fff;
        }

        /* Logout button */
        .sidebar .logout a {
            background: #ffc107;
            display: flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            transition: .3s;
            color: #000;
        }

        .sidebar .logout a:hover {
            background: #e0a800;
            color: #000;
        }

        /*======================
    MAIN CONTENT
=======================*/
        .main {
            margin-left: 260px;
            padding: 30px;
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* Doctor info card */
        .doctor-info-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .doctor-info-card h2 {
            margin-bottom: 5px;
        }

        .doctor-info-card p {
            margin: 0;
            color: #555;
        }

        /* CARDS SECTION */
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .cards .card {
            flex: 1 1 250px;
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: .3s;
            cursor: pointer;
        }

        .cards .card i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #fff;
            padding: 15px;
            border-radius: 50%;
            background: #00008B;
        }

        .cards .card.blue i {
            background: #007bff;
        }

        .cards .card.green i {
            background: #28a745;
        }

        .cards .card.orange i {
            background: #fd7e14;
        }

        .cards .card h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .cards .card p {
            color: #555;
        }

        .cards .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Responsive */
        @media(max-width:768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main {
                margin-left: 0;
                padding: 20px;
            }

            .cards {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!--======================
    SIDEBAR
=======================-->
    <div class="sidebar">
        <div class="logo">
            <img src="mclogo.png" alt="Logo">
            <h2>Doctor Panel</h2>
        </div>
        <ul class="menu">
            <li><a href="doctordash.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a href="mypatients.php"><i class="bi bi-people"></i> My Patients</a></li>
            <li><a href="appointments.php"><i class="bi bi-calendar-check"></i> Appointments</a></li>
            <li><a href="Reports.php"><i class="bi bi-file-medical"></i> Reports</a></li>
            <li><a href="prescription.php"><i class="bi bi-box-seam"></i> Medicine Stock</a></li>
            <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
            <!-- <li><a href="profile.php"><i class="bi bi-person"></i> My Profile</a></li> -->
        </ul>
        <div class="logout">
            <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>

    <!--======================
    MAIN CONTENT
=======================-->
    <div class="main">

        <!-- DOCTOR INFO CARD -->
        <div class="doctor-info-card">
            <h2>Welcome Dr. <?= htmlspecialchars($doctor_name); ?></h2>
            <p>Specialty: <?= htmlspecialchars($doctor_speciality); ?></p>
            <p class="hospital">University Medical Center</p>
        </div>

        <!-- FUNCTIONAL CARDS -->
        <div class="cards">
            <div class="card blue" onclick="location.href='mypatients.php'">
                <i class="fas fa-users"></i>
                <h2>My Patients</h2>
                <p>Manage Patients</p>
            </div>
            <div class="card green" onclick="location.href='appointments.php'">
                <i class="fas fa-user-doctor"></i>
                <h2>Appointments</h2>
                <p>Manage Appointments</p>
            </div>
            <div class="card orange" onclick="location.href='reports.php'">
                <i class="fas fa-file-medical-alt"></i>
                <h2>Reports</h2>
                <p>View & Manage Medical Reports</p>
            </div>
        </div>

    </div>

    <!-- Profile Completion Popup -->
    <div class="modal fade" id="profileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Complete Your Profile</h5>
                </div>

                <div class="modal-body">

                    <form action="save_profile.php" method="POST">

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <input type="hidden" name="page_name" value="doctordash">

                        <div class="mb-2">
                            <label>First Name</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Last Name</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>NIC</label>
                            <input type="text" name="nic" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>DOB</label>
                            <input type="date" name="DOB" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Address</label>
                            <input type="text" name="addres" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Age</label>
                            <input type="number" name="age" class="form-control">
                        </div>

                        <div class="mb-2">
                            <label>Contact Number</label>
                            <input type="text" name="contact_num" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Save Profile</button>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <?php if ($show_popup) { ?>

        <script>

            window.onload = function () {
                var myModal = new bootstrap.Modal(document.getElementById('profileModal'));
                myModal.show();
            }

        </script>

    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>