<?php
session_start();
include "../db.php";

// Ensure the user is a patient
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4){
    header("Location: ../loginpage/loginpage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Fetch or create patient record ---
$stmt = $conn->prepare("SELECT patient_id, first_name, last_name FROM patient WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if($patient){
    $patient_id = $patient['patient_id'];
    $patient_name = $patient['first_name'] . " " . $patient['last_name'];
} else {
    // Auto-create patient record
    $default_name = $_SESSION['user_name'] ?? "Patient";
    $stmt = $conn->prepare("INSERT INTO patient (user_id, first_name, last_name) VALUES (?, ?, ?)");
    $first_name = $default_name;
    $last_name = "";
    $stmt->bind_param("iss", $user_id, $first_name, $last_name);
    $stmt->execute();
    $patient_id = $stmt->insert_id;
    $patient_name = $first_name;
}

// --- Book new appointment ---
if(isset($_POST['book'])){
    $doctor_id = $_POST['doctor_id'] ?? null;
    $requested_date = $_POST['requested_date'] ?? null;
    $preferred_time = $_POST['preferred_time'] ?? null;

    if($doctor_id && $requested_date && $preferred_time){
        // Check if slot is already booked
        $stmt = $conn->prepare("SELECT * FROM appointment 
            WHERE doctor_id=? AND requested_date=? AND preferred_time=? 
            AND confirmation_status IN ('Pending','Confirmed')");
        $stmt->bind_param("iss", $doctor_id, $requested_date, $preferred_time);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            echo "<script>alert('This time slot is already booked.');</script>";
        } else {
            $unique_num = "APT".rand(1000,9999);
            $stmt = $conn->prepare("INSERT INTO appointment 
                (patient_id, doctor_id, requested_date, preferred_time, confirmation_status, unique_num) 
                VALUES (?, ?, ?, ?, 'Pending', ?)");
            $stmt->bind_param("iisss", $patient_id, $doctor_id, $requested_date, $preferred_time, $unique_num);
            $stmt->execute();
            echo "<script>alert('Appointment booked successfully. Ref: $unique_num');</script>";
        }
    }
}

// --- Cancel appointment ---
if(isset($_GET['cancel'])){
    $appointment_id = $_GET['cancel'];
    $stmt = $conn->prepare("DELETE FROM appointment 
        WHERE appointment_id=? AND patient_id=? AND confirmation_status='Pending'");
    $stmt->bind_param("ii", $appointment_id, $patient_id);
    $stmt->execute();
    echo "<script>alert('Appointment cancelled successfully');</script>";
}

// --- Reschedule appointment ---
if(isset($_POST['reschedule'])){
    $appointment_id = $_POST['appointment_id'];
    $new_time = $_POST['new_time'];

    $stmt = $conn->prepare("UPDATE appointment SET preferred_time=? 
        WHERE appointment_id=? AND patient_id=? AND confirmation_status='Pending'");
    $stmt->bind_param("sii", $new_time, $appointment_id, $patient_id);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        echo "<script>alert('Appointment rescheduled successfully');</script>";
    } else {
        echo "<script>alert('Cannot reschedule confirmed appointment');</script>";
    }
}

// --- Fetch doctors ---
$doctors = $conn->query("SELECT * FROM doctor");

// --- Available slots for selected doctor/date ---
$available_slots = [];
if(!empty($_POST['doctor_id']) && !empty($_POST['requested_date'])){
    $doctor_id = $_POST['doctor_id'];
    $requested_date = $_POST['requested_date'];

    $stmt = $conn->prepare("SELECT preferred_time FROM appointment 
        WHERE doctor_id=? AND requested_date=? AND confirmation_status IN ('Pending','Confirmed')");
    $stmt->bind_param("is", $doctor_id, $requested_date);
    $stmt->execute();
    $result = $stmt->get_result();

    $booked_times = [];
    while($r = $result->fetch_assoc()){
        $booked_times[] = $r['preferred_time'];
    }

    $start = strtotime("09:00");
    $end = strtotime("16:00");
    $all_slots = [];
    for($t=$start; $t<=$end; $t+=30*60){
        $all_slots[] = date("H:i",$t);
    }
    $available_slots = array_diff($all_slots, $booked_times);
}

// --- Fetch patient appointments ---
$appointments = $conn->query("SELECT a.*, d.Doctor_name 
    FROM appointment a 
    JOIN doctor d ON a.doctor_id=d.doctor_id
    WHERE a.patient_id='$patient_id'
    ORDER BY a.requested_date ASC, a.preferred_time ASC");

?>

<!DOCTYPE html>
<html>
<head>
<title>Book Appointment</title>
<link rel="stylesheet" href="patient_style.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include("sidebar.php"); ?>
<div class="main">
<?php include("topbar.php"); ?>

<div class="container p-4">
<h4 class="mb-3">Book Doctor Appointment</h4>
<form method="POST">
    <div class="mb-3">
        <label>Select Doctor</label>
        <select name="doctor_id" class="form-select" required onchange="this.form.submit()">
            <option value="">-- Select Doctor --</option>
            <?php 
            mysqli_data_seek($doctors,0);
            while($doc = mysqli_fetch_assoc($doctors)){ ?>
            <option value="<?= $doc['doctor_id']; ?>"
            <?= (isset($_POST['doctor_id']) && $_POST['doctor_id']==$doc['doctor_id'])?'selected':''; ?>>
            Dr. <?= htmlspecialchars($doc['Doctor_name']); ?>
            </option>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Appointment Date</label>
        <input type="date" name="requested_date" class="form-control" 
        value="<?= $_POST['requested_date'] ?? ''; ?>" required onchange="this.form.submit()">
    </div>

    <div class="mb-3">
        <label>Preferred Time</label>
        <select name="preferred_time" class="form-select" required>
            <option value="">-- Select Time --</option>
            <?php 
            foreach($available_slots as $slot){
                echo "<option value='$slot'>$slot</option>";
            }
            if(empty($available_slots) && !empty($_POST['doctor_id']) && !empty($_POST['requested_date'])){
                echo "<option>No available slots</option>";
            }
            ?>
        </select>
    </div>

    <div class="text-center">
        <button type="submit" name="book" class="btn btn-primary">Book Appointment</button>
    </div>
</form>

<hr>
<h4 class="mb-3">My Appointments</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ref</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while($appt = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= $appt['unique_num'] ?></td>
            <td><?= htmlspecialchars($appt['Doctor_name']) ?></td>
            <td><?= $appt['requested_date'] ?></td>
            <td><?= $appt['preferred_time'] ?></td>
            <td><?= $appt['confirmation_status'] ?></td>
            <td>
                <?php if($appt['confirmation_status']=='Pending'): ?>
                    <a href="?cancel=<?= $appt['appointment_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this appointment?')">Cancel</a>
                    
                    <form method="POST" style="display:inline-block">
                        <input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
                        <select name="new_time" class="form-select form-select-sm" style="width:auto; display:inline-block" required>
                        <?php
                            $doctor_id = $appt['doctor_id'];
                            $requested_date = $appt['requested_date'];
                            $stmt2 = $conn->prepare("SELECT preferred_time FROM appointment WHERE doctor_id=? AND requested_date=? AND confirmation_status IN ('Pending','Confirmed')");
                            $stmt2->bind_param("is", $doctor_id, $requested_date);
                            $stmt2->execute();
                            $res2 = $stmt2->get_result();
                            $booked_times = [];
                            while($r2 = $res2->fetch_assoc()) $booked_times[] = $r2['preferred_time'];

                            $start = strtotime("09:00");
                            $end = strtotime("16:00");
                            $all_slots = [];
                            for($t = $start; $t <= $end; $t += 30*60) $all_slots[] = date("H:i",$t);

                            $available_slots = array_diff($all_slots,$booked_times);
                            foreach($available_slots as $slot){
                                echo "<option value='$slot'>$slot</option>";
                            }
                        ?>
                        </select>
                        <button type="submit" name="reschedule" class="btn btn-warning btn-sm">Reschedule</button>
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
</div>

</body>
</html>