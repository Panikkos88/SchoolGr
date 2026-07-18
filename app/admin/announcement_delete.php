<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
DELETE FROM announcements
WHERE id=?
");

$stmt->execute([$id]);

header("Location: announcements.php");
exit;