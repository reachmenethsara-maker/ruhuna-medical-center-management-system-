<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>

    <link rel="stylesheet" href="Doctor_dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="images-removebg-preview.png" alt="uni logo">
        <img src="mclogo.png" alt="MC Logo">
    </div>

    <h4><b>DOCTOR PANEL</b></h4>

    <ul class="nav flex-column">
          <li><a href="doctordash.php"><i class="bi bi-house"></i> Dashboard</a></li>
          <li><a class="active" href="mypatients.php"><i class="bi bi-person-lines-fill"></i> My Patients</a></li>
          <li><a href="appointments.php"><i class="bi bi-calendar2-week"></i> Appointments</a></li>
          <li><a href="Reports.php"><i class="bi bi-journal-medical"></i> Reports</a></li>
          <li><a href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        </ul>

    <!-- LOGOUT AT BOTTOM -->
    <div class="logout">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>
</div>

<!-- MAIN -->
<div class="main">
    <!-- DOCTOR INFO CARD -->
<div class="doctor-info-card">
    <img src="doctor.jpg" alt="Doctor Photo" class="doctor-photo">

    <div class="doctor-details">
        <h5>Dr. John Perera</h5>
        <p class="position">Senior Medical Officer</p>
        <p class="hospital">University Medical Center</p>
    </div>
</div>


    <!-- BACKGROUND SLIDER + CENTERED CARDS -->
    <div class="background-slider">
        <div class="cards">

            <div class="card blue">
                <i class="fas fa-users"></i>
                <h2>My Patients</h2>
                <p>Manage Patients</p>
            </div>

            <div class="card green">
                <i class="fas fa-user-doctor"></i>
                <h2>My Appointment</h2>
                <p>Appointment Management</p>
            </div>

            <div class="card orange">
                <i class="fas fa-file-medical-alt"></i>
                <h2>Report Management</h2>
                <p>View and Manage Medical Reports</p>
            </div>

        </div>
    </div>

</div>

</body>
</html>
