<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once '../includes/common.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$file_id=(int)($_GET['id'] ?? 0);
$student_id=(int)($_GET['student_id'] ?? 0);

$stmt=$pdo->prepare("
SELECT *
FROM student_health_files
WHERE id=?
LIMIT 1
");

$stmt->execute([$file_id]);

$file=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$file){
    die("Το αρχείο δεν βρέθηκε.");
}

$path="../uploads/health/".$file['file_name'];

if(file_exists($path)){
    unlink($path);
}

$stmt=$pdo->prepare("
DELETE
FROM student_health_files
WHERE id=?
");

$stmt->execute([$file_id]);

header("Location: student_health_files.php?id=".$student_id);
exit;