<?php
session_start();
include '../db.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    echo json_encode(['success'=>false,'message'=>'Unauthorized']);
    exit();
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $id = $_POST['patient_id'];
    $fields = ['first_name','last_name','gender','date_of_birth','phone','email','patient_type','blood_type','academic_yr','faculty','accomodation_type','medical_history','surgical_history','family_history','marital_status'];
    
    $set = [];
    $params = [];
    $types = '';
    foreach($fields as $f){
        $set[] = "$f=?";
        $params[] = $_POST[$f] ?? '';
        $types .= 's';
    }
    
    $stmt = $conn->prepare("UPDATE patient SET ".implode(',', $set)." WHERE patient_id=?");
    $types .= 'i';
    $params[] = $id;
    $stmt->bind_param($types, ...$params);
    
    if($stmt->execute()){
        echo json_encode(['success'=>true,'message'=>'Patient updated successfully']);
    }else{
        echo json_encode(['success'=>false,'message'=>'Update failed']);
    }
}
?>