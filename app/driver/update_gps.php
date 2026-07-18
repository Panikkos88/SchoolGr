<?php

require_once '../config/database.php';

$bus_id = (int)$_POST['bus_id'];

$lat = $_POST['lat'];
$lng = $_POST['lng'];

$stmt = $pdo->prepare("
UPDATE buses
SET
gps_latitude=?,
gps_longitude=?,
gps_updated_at=NOW()
WHERE id=?
");

$stmt->execute([
$lat,
$lng,
$bus_id
]);

echo 'OK';