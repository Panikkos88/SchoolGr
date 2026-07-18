<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
require_once '../config/database.php';

$bus_id = 1;

$lat = $_POST['lat'] ?? '';
$lng = $_POST['lng'] ?? '';

if($lat=='' || $lng==''){
    exit;
}

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

echo "OK ".$lat." ".$lng;