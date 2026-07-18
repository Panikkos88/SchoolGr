<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

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

$student_id = $user['student_id'];

$student_stmt = $pdo->prepare("
SELECT *
FROM students
WHERE id=?
LIMIT 1
");

$student_stmt->execute([
    $student_id
]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

$bus_id = $student['bus_id'];

$bus_stmt = $pdo->prepare("
SELECT *
FROM buses
WHERE id=?
LIMIT 1
");

$bus_stmt->execute([$bus_id]);

$bus = $bus_stmt->fetch(PDO::FETCH_ASSOC);

$status_stmt = $pdo->prepare("
SELECT *
FROM student_bus_status
WHERE student_id=?
LIMIT 1
");

$status_stmt->execute([
    $student_id
]);

$status = $status_stmt->fetch(PDO::FETCH_ASSOC);
$message = '';

if(isset($_POST['bus_absence'])){

    $check = $pdo->prepare("
    SELECT id
    FROM bus_absences
    WHERE student_id=?
    AND absence_date=CURDATE()
    LIMIT 1
    ");

    $check->execute([
        $student_id
    ]);

    if(!$check->fetch()){

        $insert = $pdo->prepare("
        INSERT INTO bus_absences
        (
        student_id,
        absence_date
        )
        VALUES
        (
        ?,
        CURDATE()
        )
        ");

        $insert->execute([
            $student_id
        ]);

        $message = 'Ο οδηγός ενημερώθηκε.';
    }else{

        $message = 'Έχει ήδη σταλεί ενημέρωση σήμερα.';
    }
}

$online = false;

if($bus['trip_active'] == 1){
    $online = true;
}
?>

<!DOCTYPE html>

<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Το Λεωφορείο μου</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}
</style>

</head>
<body>

<div class="container py-4">

<h2>🚌 Το Λεωφορείο μου</h2>

<div class="card-box">

<h4><?= htmlspecialchars($bus['name']); ?></h4>

<?php if($online): ?>

<div class="alert alert-success">
🟢 Το λεωφορείο είναι Online
</div>
<?php else: ?>
<div class="alert alert-danger">
🔴 Το λεωφορείο είναι Offline
</div>
<?php endif; ?>

<?php if(!empty($bus['gps_latitude'])): ?>
<div
id="busMap"
style="
height:400px;
border:3px solid red;
border-radius:10px;
">
TEST ΧΑΡΤΗ
</div>
<?php endif; ?>

</div>
<div class="card-box">

<h4>🚍 Δήλωση Μεταφοράς</h4>

<?php if(!empty($message)): ?>

<div class="alert alert-success">

<?= htmlspecialchars($message); ?>

</div>

<?php endif; ?>

<form method="post">

<button
type="submit"
name="bus_absence"
class="btn btn-danger w-100">

❌ Δεν θα έρθω σήμερα με το λεωφορείο

</button>

</form>

</div>
<div class="card-box">

<h4>📋 Κατάσταση Μεταφοράς</h4>

<?php if(!empty($status['picked_up'])): ?>

<p>✅ Παραλήφθηκα<br><?= $status['pickup_time']; ?></p>
<?php endif; ?>

<?php if(!empty($status['arrived_school'])): ?>

<p>🏫 Έφτασα στο Σχολείο<br><?= $status['arrived_school_time']; ?></p>
<?php endif; ?>

<?php if(!empty($status['delivered_home'])): ?>

<p>🏠 Παραδόθηκα στον Γονέα<br><?= $status['delivered_home_time']; ?></p>
<?php endif; ?>

</div>

<a href="index.php" class="btn btn-secondary">
🏠 Επιστροφή
</a>

</div>
<script>

let map = L.map('busMap').setView(
[
<?= $bus['gps_latitude']; ?>,
<?= $bus['gps_longitude']; ?>
],
16
);

L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
{
maxZoom:19
}
).addTo(map);

let marker = L.marker(
[
<?= $bus['gps_latitude']; ?>,
<?= $bus['gps_longitude']; ?>
]
).addTo(map);

function updateBus(){

    fetch('bus_position.php')

    .then(response => response.json())

    .then(data => {

        marker.setLatLng([
            data.lat,
            data.lng
        ]);

        map.panTo([
            data.lat,
            data.lng
        ]);

    });

}

setInterval(updateBus,5000);

</script>
</body>
</html>
