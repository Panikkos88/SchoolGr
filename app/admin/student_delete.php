<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if($id <= 0){
    header("Location: students.php");
    exit;
}

/* Εκδρομές */
$stmt = $pdo->prepare("
DELETE FROM trip_signatures
WHERE student_id=?
");
$stmt->execute([$id]);

/* Σύνδεση με γονείς */
$stmt = $pdo->prepare("
DELETE FROM parent_students
WHERE student_id=?
");
$stmt->execute([$id]);

/* Απουσίες */
$stmt = $pdo->prepare("
DELETE FROM absences
WHERE student_id=?
");
$stmt->execute([$id]);
/* Λογαριασμός χρήστη */
$stmt = $pdo->prepare("
DELETE FROM users
WHERE student_id=?
");
$stmt->execute([$id]);
/* Μαθητής */
$stmt = $pdo->prepare("
DELETE FROM students
WHERE id=?
");
$stmt->execute([$id]);

header("Location: students.php");
exit;