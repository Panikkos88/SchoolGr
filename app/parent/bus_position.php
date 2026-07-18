<?php

require_once '../config/database.php';

$bus_id =
(int)($_GET['bus_id'] ?? 0);

$stmt = $pdo->prepare("
SELECT
gps_latitude,
gps_longitude
FROM buses
WHERE id=?
");

$stmt->execute([$bus_id]);

$bus =
$stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');

echo json_encode([

'lat' =>
$bus['gps_latitude'],

'lng' =>
$bus['gps_longitude']

]);