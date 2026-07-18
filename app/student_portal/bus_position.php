<?php

session_start();

require_once '../config/database.php';

$user_stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$student_stmt = $pdo->prepare("
SELECT *
FROM students
WHERE id=?
LIMIT 1
");

$student_stmt->execute([
    $user['student_id']
]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
SELECT
gps_latitude,
gps_longitude
FROM buses
WHERE id=?
LIMIT 1
");

$stmt->execute([
    $student['bus_id']
]);

$bus = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

echo json_encode([
    'lat' => $bus['gps_latitude'],
    'lng' => $bus['gps_longitude']
]);