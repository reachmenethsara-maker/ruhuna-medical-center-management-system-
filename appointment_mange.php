<?php
include 'db.php'; // your database connection

// -------------------- Delete Appointment --------------------
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM appointment WHERE appointment_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Confirm Appointment --------------------
if(isset($_GET['confirm'])){
    $id = $_GET['confirm'];
    $confirmation_by = 1; // Admin/Staff ID who confirms
    $confirmation_date = date('Y-m-d H:i:s');

    mysqli_query($conn, "UPDATE appointment 
                         SET confirmation_status='Confirmed', 
                             confirmation_by='$confirmation_by', 
                             confirmation_date='$confirmation_date' 
                         WHERE appointment_id='$id'");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// -------------------- Fetch Appointments --------------------
$search = "";
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $query = "SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last,
                     d.Doctor_name AS doctor_name
              FROM appointment a
              LEFT JOIN patient p ON a.patient_id = p.patient_id
              LEFT JOIN doctor d ON a.doctor_id = d.doctor_id
              WHERE p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%'
              ORDER BY a.requested_date DESC";
} else {
    $query = "SELECT a.*, p.first_name AS patient_first, p.last_name AS patient_last,
                     d.Doctor_name AS doctor_name
              FROM appointment a
              LEFT JOIN patient p ON a.patient_id = p.patient_id
              LEFT JOIN doctor d ON a.doctor_id = d.doctor_id
              ORDER BY a.requested_date DESC";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Appointment Management</title>
     <link rel="stylesheet" href="admindash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding:0; }
        .container { width:95%; max-width:1200px; margin:20px auto; background:#fff; padding:25px; border-radius:12px; box-shadow:0 0 15px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#0d6efd; margin-bottom:20px; }

        /* Top bar */
        .top-bar { display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:20px; }
        .top-bar input[type=text] { width:300px; padding:8px; border-radius:6px; border:1px solid #ccc; }
        .top-bar button { padding:8px 15px; border:none; border-radius:6px; background:#198754; color:white; cursor:pointer; }
        .top-bar button:hover{ opacity:0.9; }

        /* Table */
        table { width:100%; border-collapse:collapse; margin-top:10px; display:block; overflow-x:auto; }
        table th, table td { text-align:center; padding:12px; border-bottom:1px solid #ddd; white-space: nowrap; }
        table th { background:#0d6efd; color:white; font-weight:500; }
        table tr:nth-child(even){ background:#f8f9fa; }
        table tr:hover{ background:#e2e6ea; }
        table td a { margin:0 4px; text-decoration:none; padding:4px 8px; border-radius:6px; color:#fff; font-size:13px; }
        table td a[href*="edit"]{ background:#0d6efd; }
        table td a[href*="delete"]{ background:#dc3545; }
        table td a[href*="confirm"]{ background:#198754; }
        table td a:hover{ opacity:0.85; }

        @media(max-width:1024px){
            .top-bar{ flex-direction: column; align-items:flex-start; }
            .top-bar input[type=text]{ width:100%; }
        }
    </style>
</head>
<body>
     <?php include("sidebar.php"); ?>

     <div class="main">

    <?php include("topbar.php"); ?>
    
<div class="container">
    <h2>Appointment Management</h2>

    <!-- Search Form -->
    <form method="GET" class="mb-3 top-bar">
        <input type="text" name="search" placeholder="Search Patient Name..." value="<?= htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Appointments Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Requested Date</th>
                <th>Preferred Time</th>
                <th>Status</th>
                <th>Confirmed By</th>
                <th>Confirmation Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if(mysqli_num_rows($result) > 0): 
            $counter = 1;
            while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $counter++; ?></td>
                <td><?= htmlspecialchars($row['patient_first'].' '.$row['patient_last']); ?></td>
                <td><?= htmlspecialchars($row['doctor_name']); ?></td>
                <td><?= date('d M Y', strtotime($row['requested_date'])); ?></td>
                <td><?= htmlspecialchars($row['preferred_time']); ?></td>
                <td><?= htmlspecialchars($row['confirmation_status']); ?></td>
                <td><?= htmlspecialchars($row['confirmation_by']); ?></td>
                <td><?= $row['confirmation_date'] ? date('d M Y H:i', strtotime($row['confirmation_date'])) : '-'; ?></td>
                <td>
                    <?php if($row['confirmation_status'] !== 'Confirmed'): ?>
                        <a href="?confirm=<?= $row['appointment_id']; ?>">Confirm</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $row['appointment_id']; ?>" onclick="return confirm('Delete this appointment?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr>
                <td colspan="9" class="text-center">No appointments found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>