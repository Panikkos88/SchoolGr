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
    header("Location: parents.php");
    exit;
}

/* Σύνδεση γονέα με μαθητές */
$stmt = $pdo->prepare("
DELETE FROM parent_students
WHERE parent_id=?
");
$stmt->execute([$id]);
/* Διαγραφή λογαριασμού χρήστη */
$stmt = $pdo->prepare("
DELETE FROM users
WHERE parent_id=?
");
$stmt->execute([$id]);
/* Διαγραφή γονέα */
$stmt = $pdo->prepare("
DELETE FROM parents
WHERE id=?
");
$stmt->execute([$id]);

header("Location: parents.php");
exit;