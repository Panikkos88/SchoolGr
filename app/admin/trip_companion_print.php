<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id=(int)($_GET['id'] ?? 0);

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

$stmt->execute([$id]);

$trip=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$trip){
    die("Η εκδρομή δεν βρέθηκε.");
}

$school=$pdo->query("
SELECT *
FROM school_settings
LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$stmt=$pdo->prepare("
SELECT

s.id,
s.first_name,
s.last_name,

c.name AS class_name,
sec.name AS section_name,

hc.medical_conditions,
hc.allergies

FROM trip_signatures ts

INNER JOIN students s
ON s.id=ts.student_id

LEFT JOIN classes c
ON c.id=s.class_id

LEFT JOIN sections sec
ON sec.id=s.section_id

LEFT JOIN student_health_cards hc
ON hc.student_id=s.id

WHERE ts.trip_id=?

ORDER BY
c.name,
sec.name,
s.last_name,
s.first_name
");

$stmt->execute([$id]);

$students=$stmt->fetchAll(PDO::FETCH_ASSOC);

$total=count($students);

$alerts=0;

foreach($students as $student){

    if(
        trim($student['medical_conditions'] ?? '')!='' ||
        trim($student['allergies'] ?? '')!=''
    ){
        $alerts++;
    }

}

?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Δελτίο Συνοδού Εκδρομής</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:white;
    font-family:Arial,Helvetica,sans-serif;
    font-size:15px;
    color:#000;
}

.page{
    max-width:1000px;
    margin:auto;
    padding:25px;
}

.header{
    text-align:center;
    border-bottom:3px solid #0d6efd;
    padding-bottom:15px;
    margin-bottom:25px;
}

.logo{
    max-height:90px;
    margin-bottom:10px;
}

.info-box{
    border:1px solid #ccc;
    border-radius:10px;
    padding:15px;
    margin-bottom:20px;
}

.info-box p{
    margin:4px 0;
}

table{
    font-size:14px;
}

th{
    background:#0d6efd !important;
    color:white !important;
    text-align:center;
}

td{
    height:40px;
    vertical-align:middle;
}

@media print{

.btn-print,
.btn-back{
    display:none;
}

.page{
    max-width:100%;
    padding:0;
}

}

</style>

</head>

<body>

<div class="page">

<div class="header">

<?php if(!empty($school['school_logo'])): ?>

<img
class="logo"
src="../uploads/logo/<?= htmlspecialchars($school['school_logo']); ?>">

<?php endif; ?>

<h2>

<?= htmlspecialchars($school['school_name'] ?? 'SchoolMedia'); ?>

</h2>

<h4>

🚌 ΔΕΛΤΙΟ ΣΥΝΟΔΟΥ ΕΚΔΡΟΜΗΣ

</h4>

</div>
	<div class="info-box">

<div class="row">

<div class="col-md-6">

<p>

<strong>📚 Εκδρομή:</strong><br>

<?= htmlspecialchars($trip['title']); ?>

</p>

<p>

<strong>📅 Ημερομηνία:</strong><br>

<?= date('d/m/Y',strtotime($trip['trip_date'])); ?>

</p>

<p>

<strong>👨‍🏫 Αρχηγός Εκδρομής:</strong><br>

<?= htmlspecialchars(
($trip['teacher_first_name'] ?? '').
' '.
($trip['teacher_last_name'] ?? '')
); ?>

</p>

</div>

<div class="col-md-6">

<p>

<strong>💰 Κόστος:</strong><br>

<?= number_format($trip['cost'],2); ?> €

</p>

<p>

<strong>👨‍🎓 Συνολικοί Μαθητές:</strong><br>

<?= $total; ?>

</p>

<p>

<strong>❤️ Μαθητές με Ιατρικές Παρατηρήσεις:</strong><br>

<?= $alerts; ?>

</p>

</div>

</div>

</div>

<h5 class="mb-3">

📋 Λίστα Συμμετεχόντων

</h5>

<table class="table table-bordered">

<thead>

<tr>

<th width="40">#</th>

<th>Μαθητής</th>

<th width="90">Τάξη</th>

<th width="90">Τμήμα</th>

<th width="50">❤️</th>

<th width="70">Παρών</th>

<th width="80">Λεωφορείο</th>

<th width="80">Επιστροφή</th>

<th width="120">Υπογραφή</th>

</tr>

</thead>

<tbody>

<?php

$no=1;

foreach($students as $student):

?>
	<tr>

<td class="text-center">

<?= $no++; ?>

</td>

<td>

<strong>

<?= htmlspecialchars($student['last_name']); ?>

<?= htmlspecialchars($student['first_name']); ?>

</strong>

</td>

<td class="text-center">

<?= htmlspecialchars($student['class_name']); ?>

</td>

<td class="text-center">

<?= htmlspecialchars($student['section_name']); ?>

</td>

<td class="text-center">

<?php

if(
trim($student['medical_conditions'] ?? '')!='' ||
trim($student['allergies'] ?? '')!=''
){

    echo "⚠️";

}else{

    echo "✔";

}

?>

</td>

<td></td>

<td></td>

<td></td>

<td></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>
	<?php if($alerts>0): ?>

<div style="page-break-before:always;"></div>

<h3 class="mt-4">

❤️ Μαθητές με Ιατρικές Παρατηρήσεις

</h3>

<p class="text-muted">

Οι παρακάτω μαθητές χρειάζονται ιδιαίτερη προσοχή κατά τη διάρκεια της εκδρομής.

</p>

<table class="table table-bordered">

<thead>

<tr>

<th width="220">Μαθητής</th>

<th>Παθήσεις</th>

<th>Αλλεργίες</th>

</tr>

</thead>

<tbody>

<?php foreach($students as $student): ?>

<?php

if(
trim($student['medical_conditions'] ?? '')=='' &&
trim($student['allergies'] ?? '')==''
){
    continue;
}

?>

<tr>

<td>

<strong>

<?= htmlspecialchars($student['last_name']); ?>

<?= htmlspecialchars($student['first_name']); ?>

</strong>

</td>

<td>

<?= nl2br(htmlspecialchars($student['medical_conditions'] ?: '-')); ?>

</td>

<td>

<?= nl2br(htmlspecialchars($student['allergies'] ?: '-')); ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>
	<hr class="mt-5">

<div class="row mt-5">

<div class="col-6 text-center">

<br><br><br>

....................................................

<br>

<strong>

Υπογραφή Αρχηγού Εκδρομής

</strong>

</div>

<div class="col-6 text-center">

<br><br><br>

....................................................

<br>

<strong>

Υπογραφή Διευθυντή

</strong>

</div>

</div>

<br><br>

<div class="text-center text-muted">

Το παρόν έντυπο δημιουργήθηκε από το SchoolMedia.

</div>

<hr>

<div class="text-center mb-4">

<button
type="button"
class="btn btn-primary btn-print"
onclick="window.print();">

🖨️ Εκτύπωση

</button>

<a
href="trip_view.php?id=<?= $trip['id']; ?>"
class="btn btn-secondary btn-back">

⬅️ Επιστροφή

</a>

</div>

</div>

</body>

</html>