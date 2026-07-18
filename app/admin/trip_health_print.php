<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$trip_id=(int)($_GET['id'] ?? 0);

$school=$pdo->query("
SELECT *
FROM school_settings
LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$stmt=$pdo->prepare("
SELECT

t.*,

te.first_name AS teacher_first_name,
te.last_name AS teacher_last_name

FROM trips t

LEFT JOIN teachers te
ON te.id=t.leader_teacher_id

WHERE t.id=?

LIMIT 1
");

$stmt->execute([$trip_id]);

$trip=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$trip){
    die("Η εκδρομή δεν βρέθηκε.");
}

$stmt=$pdo->prepare("
SELECT

s.id,
s.first_name,
s.last_name,

hc.blood_group,
hc.rh_factor,
hc.medical_conditions,
hc.allergies,
hc.medications,

p.first_name AS parent_first_name,
p.last_name AS parent_last_name,
p.phone,
p.email

FROM trip_signatures ts

INNER JOIN students s
ON s.id=ts.student_id

LEFT JOIN student_health_cards hc
ON hc.student_id=s.id

LEFT JOIN parents p
ON p.id=ts.parent_id

WHERE ts.trip_id=?

ORDER BY
s.last_name,
s.first_name
");

$stmt->execute([$trip_id]);

$students=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<title>

Εκτύπωση Καρτών Υγείας

</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:white;
    padding:20px;
    font-family:Arial;
}

.card-health{

    border:2px solid #000;

    border-radius:12px;

    padding:15px;

    margin-bottom:20px;

    page-break-inside:avoid;

}

.logo{

    height:70px;

}

@media print{

button{

display:none;

}

}

</style>

</head>

<body>

<div class="container">

<div class="text-center mb-4">

<?php if(!empty($school['logo'])): ?>

<img
src="../uploads/logo/<?= htmlspecialchars($school['logo']); ?>"
class="logo">

<br><br>

<?php endif; ?>

<h2>

<?= htmlspecialchars($school['school_name']); ?>

</h2>

<h3>

❤️ ΚΑΡΤΕΣ ΥΓΕΙΑΣ ΕΚΔΡΟΜΗΣ

</h3>

<p>

🚌 <?= htmlspecialchars($trip['title']); ?>

<br>

📅 <?= date('d/m/Y',strtotime($trip['trip_date'])); ?>

</p>

</div>
	<?php foreach($students as $student): ?>

<div class="card-health">

<div class="row">

<div class="col-8">

<h4>

👨‍🎓

<?= htmlspecialchars($student['last_name'].' '.$student['first_name']); ?>

</h4>

<p>

<strong>🩸 Ομάδα Αίματος:</strong>

<?= htmlspecialchars($student['blood_group'] ?: '-'); ?>

&nbsp;&nbsp;

<strong>Rh:</strong>

<?= htmlspecialchars($student['rh_factor'] ?: '-'); ?>

</p>

<p>

<strong>⚠️ Παθήσεις</strong>

<br>

<?= nl2br(htmlspecialchars($student['medical_conditions'] ?: 'Καμία')); ?>

</p>

<p>

<strong>🌼 Αλλεργίες</strong>

<br>

<?= nl2br(htmlspecialchars($student['allergies'] ?: 'Καμία')); ?>

</p>

<p>

<strong>💊 Φάρμακα</strong>

<br>

<?= nl2br(htmlspecialchars($student['medications'] ?: 'Δεν υπάρχουν')); ?>

</p>

</div>

<div class="col-4">

<p>

<strong>👨‍👩‍👦 Γονέας</strong>

<br>

<?= htmlspecialchars($student['parent_last_name'].' '.$student['parent_first_name']); ?>

</p>

<p>

<strong>📞 Τηλέφωνο</strong>

<br>

<?= htmlspecialchars($student['phone'] ?: '-'); ?>

</p>

<p>

<strong>📧 Email</strong>

<br>

<?= htmlspecialchars($student['email'] ?: '-'); ?>

</p>

</div>

</div>

</div>

<?php endforeach; ?>
	<hr>

<div class="row">

<div class="col-6 text-center">

<br><br>

_________________________

<br>

<b>Υπογραφή Αρχηγού Εκδρομής</b>

</div>

<div class="col-6 text-center">

<br><br>

_________________________

<br>

<b>Υπογραφή Διευθυντή</b>

</div>

</div>

<br><br>

<div class="text-center">

<button
class="btn btn-primary btn-lg"
onclick="window.print();">

🖨️ Εκτύπωση

</button>

<a
href="trip_view.php?id=<?= $trip['id']; ?>"
class="btn btn-secondary btn-lg">

⬅️ Επιστροφή

</a>

</div>

</div>

</body>

</html>