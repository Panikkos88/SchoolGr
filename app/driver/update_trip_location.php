<?php

session_start();

require_once '../includes/common.php';

if(!isset($_SESSION['user_id'])){
    exit;
}

$trip_id=(int)$_POST['trip_id'];

$lat=$_POST['lat'];

$lng=$_POST['lng'];

$stmt=$pdo->prepare("
UPDATE trips
SET
bus_lat=?,
bus_lng=?,
last_location_update=NOW()
WHERE id=?
");

$stmt->execute([

$lat,

$lng,

$trip_id

]);

echo "OK";