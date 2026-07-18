<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$pdo->prepare("
DELETE FROM trip_signatures
WHERE trip_id=?
")->execute([$id]);

$pdo->prepare("
DELETE FROM trips
WHERE id=?
")->execute([$id]);

header("Location: trips.php");
exit;