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

s.first_name,
s.last_name,

tp.status,
tp.payment_method,
tp.amount,
tp.paid_at

FROM trip_signatures ts

INNER JOIN students s
ON s.id=ts.student_id

LEFT JOIN trip_payments tp
ON tp.trip_id=ts.trip_id
AND tp.student_id=ts.student_id

WHERE ts.trip_id=?

ORDER BY
s.last_name,
s.first_name
");

$stmt->execute([$trip_id]);

$students=$stmt->fetchAll(PDO::FETCH_ASSOC);
$totalStudents=count($students);

$paid=0;
$pending=0;
$totalAmount=0;

foreach($students as $student){

    if($student['status']=='paid'){

        $paid++;
        $totalAmount+=$trip['cost'];

    }else{

        $pending++;

    }

}

?>
<!DOCTYPE html>

<html lang="el">

<head>

<meta charset="UTF-8">

<title>

Κατάσταση Πληρωμών Εκδρομής

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{

background:white;
padding:20px;

}

table{

font-size:14px;

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
style="height:90px;"><br><br>

<?php endif; ?>

<h2>

<?= htmlspecialchars($school['school_name']); ?>

</h2>

<h3>

💳 ΚΑΤΑΣΤΑΣΗ ΠΛΗΡΩΜΩΝ ΕΚΔΡΟΜΗΣ

</h3>

</div>

<hr>

<div class="row">

<div class="col-6">

<strong>

🚌 Εκδρομή:

</strong>

<br>

<?= htmlspecialchars($trip['title']); ?>

</div>

<div class="col-6">

<strong>

📅 Ημερομηνία:

</strong>

<br>

<?= date('d/m/Y',strtotime($trip['trip_date'])); ?>

</div>

</div>

<br>

<div class="row">

<div class="col-6">

<strong>

👨‍🏫 Αρχηγός:

</strong>

<br>

<?= htmlspecialchars($trip['teacher_first_name'].' '.$trip['teacher_last_name']); ?>

</div>

<div class="col-6">

<strong>

💰 Κόστος ανά μαθητή:

</strong>

<br>

<?= number_format($trip['cost'],2); ?> €

</div>

</div>

<hr>
	<table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>#</th>

<th>Μαθητής</th>

<th>Ποσό</th>

<th>Κατάσταση</th>

<th>Τρόπος Πληρωμής</th>

<th>Ημερομηνία</th>

</tr>

</thead>

<tbody>

<?php

$no=1;

foreach($students as $student):

?>

<tr>

<td>

<?= $no++; ?>

</td>

<td>

<?= htmlspecialchars($student['last_name'].' '.$student['first_name']); ?>

</td>

<td>

<?= number_format($trip['cost'],2); ?> €

</td>

<td align="center">

<?php if($student['status']=='paid'): ?>

<span class="badge bg-success">

✅ Πληρώθηκε

</span>

<?php else: ?>

<span class="badge bg-danger">

❌ Εκκρεμεί

</span>

<?php endif; ?>

</td>

<td>

<?php

switch($student['payment_method']){

case 'cash':

echo "💵 Μετρητά";

break;

case 'card':

echo "💳 Κάρτα";

break;

case 'bank':

echo "🏦 Κατάθεση";

break;

default:

echo "-";

}

?>

</td>

<td>

<?php

if(!empty($student['paid_at'])){

echo date('d/m/Y H:i',strtotime($student['paid_at']));

}else{

echo "-";

}

?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<hr>

<div class="row text-center">

<div class="col-3">

<div class="alert alert-primary">

<strong>

👨‍🎓 Μαθητές

</strong>

<br><br>

<h3>

<?= $totalStudents; ?>

</h3>

</div>

</div>

<div class="col-3">

<div class="alert alert-success">

<strong>

✅ Πλήρωσαν

</strong>

<br><br>

<h3>

<?= $paid; ?>

</h3>

</div>

</div>

<div class="col-3">

<div class="alert alert-warning">

<strong>

⏳ Εκκρεμούν

</strong>

<br><br>

<h3>

<?= $pending; ?>

</h3>

</div>

</div>

<div class="col-3">

<div class="alert alert-info">

<strong>

💰 Εισπράξεις

</strong>

<br><br>

<h3>

<?= number_format($totalAmount,2); ?> €

</h3>

</div>

</div>

</div>

<hr style="margin-top:60px;">

<div class="row mt-5">

<div class="col-6 text-center">

<br><br><br>

_____________________________

<br>

<b>Υπογραφή Ταμία</b>

</div>

<div class="col-6 text-center">

<br><br><br>

_____________________________

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
href="trip_payments.php?id=<?= $trip_id; ?>"
class="btn btn-secondary btn-lg">

⬅️ Επιστροφή

</a>

</div>

</div>

</body>

</html>