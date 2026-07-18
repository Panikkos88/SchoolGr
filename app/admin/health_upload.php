<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);

session_start();

require_once '../includes/common.php';

if(!isset($_SESSION['user_id'])){
    exit("Δεν έχετε πρόσβαση.");
}

$student_id=(int)($_POST['student_id'] ?? 0);

if($student_id==0){
    die("Λάθος μαθητής.");
}

if(!isset($_FILES['health_file'])){
    die("Δεν επιλέχθηκε αρχείο.");
}

$allowed=[
'pdf',
'jpg',
'jpeg',
'png',
'webp',
'doc',
'docx'
];

$ext=strtolower(pathinfo($_FILES['health_file']['name'],PATHINFO_EXTENSION));

if(!in_array($ext,$allowed)){
    die("Μη επιτρεπτός τύπος αρχείου.");
}

$newName=time().'_'.uniqid().'.'.$ext;

$folder="../uploads/health/";

if(!is_dir($folder)){
    mkdir($folder,0755,true);
}

if(!move_uploaded_file(
    $_FILES['health_file']['tmp_name'],
    $folder.$newName
)){
    die("Απέτυχε η μεταφορά του αρχείου.");
}

$stmt=$pdo->prepare("
INSERT INTO student_health_files
(
student_id,
title,
description,
file_name,
expire_date,
uploaded_by
)
VALUES
(
?,?,?,?,?,?
)
");

$stmt->execute([

$student_id,
$_POST['title'],
$_POST['description'],
$newName,
$_POST['expire_date'] ?: null,
$_SESSION['user_id']

]);

header("Location: student_health_files.php?id=".$student_id);
exit;