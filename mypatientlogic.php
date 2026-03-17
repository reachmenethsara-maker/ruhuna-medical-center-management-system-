<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header("Location: doctor_login.php");
    exit();
}

include 'db.php'; // database connection
$username = $_SESSION['doctor'];

// Get doctor id and full name
$stmt = $conn->prepare("SELECT id, full_name FROM doctors WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
$doctor = $res->fetch_assoc();
$stmt->close();

if (!$doctor) {
    session_destroy();
    header("Location: doctor_login.php");
    exit();
}

$doctor_id = (int)$doctor['id'];
$doctor_name = $doctor['full_name'] ?? $username;

// Handle search and filter
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$dept = isset($_GET['department']) ? trim($_GET['department']) : '';

// Build query
$sql = "SELECT id, first_name, last_name, department, phone, last_visit FROM patients WHERE doctor_id = ?";
$params = [$doctor_id];
$types = "i";

if ($q !== '') {
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= "ssss";
}

if ($dept !== '') {
    $sql .= " AND department = ?";
    $params[] = $dept;
    $types .= "s";
}

$sql .= " ORDER BY last_visit DESC, last_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get distinct departments
$deptStmt = $conn->prepare("SELECT DISTINCT department FROM patients WHERE doctor_id = ? AND department IS NOT NULL AND department <> ''");
$deptStmt->bind_param("i", $doctor_id);
$deptStmt->execute();
$dres = $deptStmt->get_result();
$departments = [];
while ($drow = $dres->fetch_assoc()) {
    $departments[] = $drow['department'];
}
$deptStmt->close();
?>
