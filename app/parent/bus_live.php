<?php

require_once '../config/database.php';
session_start();

$message = '';

if(isset($_POST['bus_absence'])){

    $student_id = (int)$_POST['student_id'];

    $check = $pdo->prepare("
    SELECT id
    FROM bus_absences
    WHERE student_id=?
    AND absence_date=CURDATE()
    LIMIT 1
    ");

    $check->execute([$student_id]);

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

        $insert->execute([$student_id]);

        $message = 'Ο οδηγός ενημερώθηκε.';
    }
}
$bus_id = (int)($_GET['bus_id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM buses
WHERE id=?
");

$stmt->execute([$bus_id]);

$bus = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$bus){
    die("Το λεωφορείο δεν βρέθηκε");
}

$online = false;

if(!empty($bus['gps_updated_at'])){

    $diff =
    time() -
    strtotime($bus['gps_updated_at']);

    if($diff < 300){
        $online = true;
    }
}

?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Live Λεωφορείο</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-top:20px;
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
👨 Οδηγός:
<strong>
<?= htmlspecialchars($bus['driver_name']); ?>
</strong>
</p>

<p>
📞 Τηλέφωνο:
<strong>
<?= htmlspecialchars($bus['driver_phone']); ?>
</strong>
</p>

<?php if($online): ?>

<div class="alert alert-success">
🟢 Το λεωφορείο είναι Online
</div>

<?php else: ?>

<div class="alert alert-danger">
🔴 Το λεωφορείο είναι Offline
</div>

<?php endif; ?>

<div class="alert alert-info">

📍 Latitude:
<strong><?= $bus['gps_latitude']; ?></strong>

<br>

📍 Longitude:
<strong><?= $bus['gps_longitude']; ?></strong>

<br>

🕒 Τελευταία ενημέρωση:
<strong><?= $bus['gps_updated_at']; ?></strong>

</div>

<?php if(!empty($bus['gps_latitude'])): ?>

<iframe
id="busMap"
width="100%"
height="500"
frameborder="0"
style="border:0;border-radius:10px;"
src="https://maps.google.com/maps?q=<?= $bus['gps_latitude']; ?>,<?= $bus['gps_longitude']; ?>&z=15&output=embed">
</iframe>

<?php endif; ?>
<?php if(!empty($_SESSION['role']) && $_SESSION['role']=='student'): ?>

<hr>

<?php

$user_stmt = $pdo->prepare("
SELECT student_id
FROM users
WHERE id=?
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$current_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

?>

<?php if(!empty($message)): ?>

<div class="alert alert-success">

✅ <?= $message; ?>

</div>

<?php endif; ?>

<form method="post">

<input
type="hidden"
name="student_id"
value="<?= $current_user['student_id']; ?>">

<button
type="submit"
name="bus_absence"
class="btn btn-danger w-100">

❌ Δεν θα έρθω σήμερα με το λεωφορείο

</button>

</form>

<?php endif; ?>
</div>

</div>
<script>

setInterval(function(){

    fetch(window.location.href)
    .then(response => response.text())
    .then(html => {

        let parser = new DOMParser();
        let doc = parser.parseFromString(html,'text/html');

        let newMap =
        doc.getElementById('busMap');

        if(newMap){

            document.getElementById('busMap').src =
            newMap.src;

        }

    });

},5000);

</script>
</body>
</html>