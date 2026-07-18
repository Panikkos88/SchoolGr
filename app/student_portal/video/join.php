<?php
session_start();

require_once '../../config/database.php';

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] != 'student'
) {
    header("Location: ../../login.php");
    exit;
}

$room_id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT
meet_link
FROM virtual_classrooms
WHERE id=?
LIMIT 1
");

$stmt->execute([$room_id]);

$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room || empty($room['meet_link'])) {
    die("Δεν υπάρχει διαθέσιμος σύνδεσμος Google Meet.");
}

header("Location: " . $room['meet_link']);
exit;