<?php

session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$pdo->prepare("
DELETE FROM sections
WHERE class_id=?
")->execute([$id]);

$pdo->prepare("
DELETE FROM classes
WHERE id=?
")->execute([$id]);

header("Location: classes.php");
exit;