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

$stmt=$pdo->prepare("
SELECT *
FROM trips
WHERE id=?
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

tp.id AS payment_id,
tp.amount,
tp.status,
tp.payment_method,
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
if(isset($_GET['filter']) && $_GET['filter']=='pending'){

    $students=array_filter($students,function($student){

        return $student['status']!='paid';

    });

}
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

$remaining=$pending*$trip['cost'];
?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Πληρωμές Εκδρομής

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:#fff;
padding:20px;
border-radius:18px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-top:20px;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="card-box">

<h2>

💳 Πληρωμές Εκδρομής

</h2>

<hr>
<div class="row mb-4">

<div class="col-md-3">

<div class="alert alert-success text-center">

<h6>💰 Εισπράξεις</h6>

<h3>

<?= number_format($totalAmount,2); ?> €

</h3>

</div>

</div>

<div class="col-md-3">

<div class="alert alert-primary text-center">

<h6>✅ Πλήρωσαν</h6>

<h3>

<?= $paid; ?>

</h3>

</div>

</div>

<div class="col-md-3">

<div class="alert alert-warning text-center">

<h6>⏳ Εκκρεμούν</h6>

<h3>

<?= $pending; ?>

</h3>

</div>

</div>

<div class="col-md-3">

<div class="alert alert-danger text-center">

<h6>💸 Υπόλοιπο</h6>

<h3>

<?= number_format($remaining,2); ?> €

</h3>

</div>

</div>

	</div>
<h5>

🚌 <?= htmlspecialchars($trip['title']); ?>

</h5>
	<div class="mb-3">

<a
href="send_trip_payment_reminders.php?id=<?= $trip_id; ?>"
class="btn btn-danger">

📧 Υπενθύμιση Πληρωμής

</a>

	</div>
<div class="mb-3">

<a
href="trip_payments.php?id=<?= $trip_id; ?>"
class="btn btn-primary">

👨‍🎓 Όλοι οι μαθητές

</a>

<a
href="trip_payments.php?id=<?= $trip_id; ?>&filter=pending"
class="btn btn-warning">

⏳ Μόνο όσοι δεν πλήρωσαν

</a>

	</div>
<p>

📅 <?= date('d/m/Y',strtotime($trip['trip_date'])); ?>

</p>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>Μαθητής</th>

<th>Ποσό</th>

<th>Κατάσταση</th>

<th>Τρόπος</th>

<th>Ημερομηνία</th>

<th>Ενέργεια</th>

</tr>

</thead>

<tbody>

<?php foreach($students as $student): ?>

<tr>

<td>

<?= htmlspecialchars($student['last_name'].' '.$student['first_name']); ?>

</td>

<td>

<?= number_format($trip['cost'],2); ?> €

</td>
	<td>

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

<td>

<?php if($student['status']=='paid'): ?>

<span class="text-success">

✔ Ολοκληρώθηκε

</span>

<?php else: ?>

<a
href="trip_payment_add.php?trip_id=<?= $trip_id; ?>&student_id=<?= $student['id']; ?>"
class="btn btn-success btn-sm">

💰 Καταχώριση

</a>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>
	<?php

$paid=0;
$pending=0;
$totalAmount=0;

foreach($students as $student){

    if($student['status']=='paid'){

        $paid++;
        $totalAmount += $trip['cost'];

    }else{

        $pending++;

    }

}

$remaining=$pending * $trip['cost'];

?>

<hr>

<div class="row text-center">

<div class="col-md-4">

<div class="alert alert-success">

<h5>

💰 Εισπράξεις

</h5>

<h3>

<?= number_format($totalAmount,2); ?> €

</h3>

</div>

</div>

<div class="col-md-4">

<div class="alert alert-warning">

<h5>

👨‍🎓 Πλήρωσαν

</h5>

<h3>

<?= $paid; ?>

/

<?= count($students); ?>

</h3>

</div>

</div>

<div class="col-md-4">

<div class="alert alert-danger">

<h5>

❌ Υπόλοιπο

</h5>

<h3>

<?= number_format($remaining,2); ?> €

</h3>

</div>

</div>

</div>

<div class="d-grid gap-2 mt-4">

<a
href="trip_view.php?id=<?= $trip_id; ?>"
class="btn btn-secondary">

⬅️ Επιστροφή στην Εκδρομή

</a>

</div>

</div>

</div>

</body>

</html>
