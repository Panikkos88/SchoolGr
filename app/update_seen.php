<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

require_once 'config/database.php';

$version = $_GET['version'] ?? '';

$stmt = $pdo->prepare("
UPDATE users
SET last_seen_version=?
WHERE id=?
");

$stmt->execute([
    $version,
    $_SESSION['user_id']
]);

header("Location: ".$_SERVER['HTTP_REFERER']);
exit;