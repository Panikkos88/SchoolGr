<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if($id <= 0){
    header("Location: teachers.php");
    exit;
}

// Διαγραφή συσχετίσεων τάξεων/τμημάτων
$stmt = $pdo->prepare("
DELETE FROM teacher_sections
WHERE teacher_id=?
");
$stmt->execute([$id]);

// Διαγραφή λογαριασμού χρήστη
$stmt = $pdo->prepare("
DELETE FROM users
WHERE teacher_id=?
");
$stmt->execute([$id]);

// Διαγραφή εκπαιδευτικού
$stmt = $pdo->prepare("
DELETE FROM teachers
WHERE id=?
");
$stmt->execute([$id]);

header("Location: teachers.php");
exit;
?>