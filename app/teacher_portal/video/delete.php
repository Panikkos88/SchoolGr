<?php
session_start();

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']!='teacher'
){
    header("Location: ../../login.php");
    exit;
}

require_once '../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
DELETE FROM virtual_classrooms
WHERE id=?
LIMIT 1
");

$stmt->execute([$id]);

header("Location: index.php");
exit;