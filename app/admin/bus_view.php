<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM buses
WHERE id=?
");

$stmt->execute([$id]);

$bus = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$bus){
    die("Το λεωφορείο δεν βρέθηκε");
}

$students_stmt = $pdo->prepare("
SELECT
first_name,
last_name
FROM students
WHERE bus_id=?
ORDER BY last_name, first_name
");

$students_stmt->execute([$id]);

$students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?= $LANG['buses']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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

<div class="card-box">

<h2>
🚌 <?= htmlspecialchars($bus['name']); ?>
</h2>

<p>
👨 <?= $LANG['driver']; ?>:
<strong>
<?= htmlspecialchars($bus['driver_name']); ?>
</strong>
</p>

<p>
📞 <?= $LANG['phone']; ?>:
<strong>
<?= htmlspecialchars($bus['driver_phone']); ?>
</strong>
</p>

<p>
📡 <?= $LANG['gps']; ?>:
<strong>
<?= htmlspecialchars($bus['gps_device_id']); ?>
</strong>
</p>
<?php if(!empty($bus['gps_latitude'])): ?>

<hr>
	<h5>📍 <?= $LANG['live_location']; ?></h5>
<p>

Latitude:
<?= $bus['gps_latitude']; ?>

<br>

Longitude:
<?= $bus['gps_longitude']; ?>

</p>

<iframe
width="100%"
height="350"
frameborder="0"
style="border:0;border-radius:10px;"
src="https://maps.google.com/maps?q=<?= $bus['gps_latitude']; ?>,<?= $bus['gps_longitude']; ?>&z=16&output=embed">
</iframe>

<br><br>

<small>

🕒 <?= $LANG['last_update']; ?>:
<?= $bus['gps_updated_at']; ?>

</small>
<?php

$online = false;

if(!empty($bus['gps_updated_at'])){

    $diff = time() - strtotime($bus['gps_updated_at']);

    if($diff < 300){
        $online = true;
    }
}

?>

<?php if($online): ?>

<p class="text-success">
	🟢 <?= $LANG['bus_online']; ?>
</p>

<?php else: ?>

<p class="text-danger">
🔴 <?= $LANG['bus_offline']; ?>
</p>

<?php endif; ?>
<?php endif; ?>
</div>

<div class="card-box">
<h4>
👨‍🎓 <?= $LANG['bus_students']; ?>
	</h4>
<?php if(count($students)==0): ?>

<div class="alert alert-warning">
<?= $LANG['no_students']; ?>

</div>

<?php else: ?>

<?php foreach($students as $student): ?>

<div class="border rounded p-2 mb-2">

<?= htmlspecialchars($student['last_name']); ?>
<?= htmlspecialchars($student['first_name']); ?>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

<a
href="buses.php"
class="btn btn-secondary w-100">
⬅️ <?= $LANG['back']; ?>
</a>

</div>

</body>
</html>