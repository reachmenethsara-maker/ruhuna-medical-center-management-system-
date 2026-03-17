<?php
if ($_SERVER["REQUSET_METHOD"] == "POST") {

    $unam = $_POST['username'] ?? '';
     $pwd = $_POST['pwd'] ?? '';

 if ($unam === 'staff' && $pwd === '1234') {
    header("Location: staff dash.html");
    exit();
 }
 elseif ($unam === 'doctor' && $pwd === '1234') {
    header("Location: Doctor dash.html");
    exit();
}
 elseif ($unam === 'admin' && $pwd === '1234') {
    header("Location: admindash.html");
    exit();
}
elseif ($unam === 'patient' && $pwd === '1234') {
    header("Location: Patient dash.html");
    exit();
}
  elseif ($unam === 'inventory' && $pwd === '1234') {
    header("Location: Inventory dash.html");
    exit();
} 
else {
   echo "invalid username or password";
}
}
?>