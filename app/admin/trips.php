<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

/* ===========================
   ΣΤΑΤΙΣΤΙΚΑ
=========================== */

$total_trips = $pdo->query("
SELECT COUNT(*)
FROM trips
")->fetchColumn();

$pending_signatures = $pdo->query("
SELECT COUNT(*)
FROM trip_signatures
WHERE status='pending'
")->fetchColumn();

$pending_payments = $pdo->query("
SELECT COUNT(*)
FROM trip_payments
WHERE payment_status='pending'
")->fetchColumn();

$total_income = $pdo->query("
SELECT IFNULL(SUM(amount),0)
FROM trip_payments
WHERE payment_status='paid'
")->fetchColumn();

/* ===========================
   ΛΙΣΤΑ ΕΚΔΡΟΜΩΝ
=========================== */

$stmt = $pdo->query("
SELECT *
FROM trips
ORDER BY trip_date DESC
");

$trips = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>

<html lang="<?= $lang; ?>">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title><?= $LANG['trips']; ?></title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.trip-card{

background:#fff;

border-radius:18px;

padding:20px;

margin-bottom:20px;

box-shadow:0 2px 12px rgba(0,0,0,.08);

transition:.25s;

}

.trip-card:hover{

transform:translateY(-3px);

box-shadow:0 10px 25px rgba(0,0,0,.15);

}

.progress{

height:22px;

}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">

🚌 <?= $LANG['trips']; ?>

</h2>
	<div class="row mb-4">

<div class="col-md-6 mb-2">

<a
href="trip_add.php"
class="btn btn-success w-100">

➕ <?= $LANG['new_trip']; ?>

</a>

</div>

<div class="col-md-6 mb-2">

<a
href="trip_payments.php"
class="btn btn-primary w-100">

💳 Πληρωμές Εκδρομών

</a>

</div>

</div>

<div class="row mb-4">

<div class="col-6 col-lg-3">

<div class="card bg-primary text-white text-center">

<div class="card-body">

<h2><?= $total_trips; ?></h2>

<div>🚌 Εκδρομές</div>

</div>

</div>

</div>

<div class="col-6 col-lg-3">

<div class="card bg-warning text-dark text-center">

<div class="card-body">

<h2><?= $pending_signatures; ?></h2>

<div>✍️ Εκκρεμείς Υπογραφές</div>

</div>

</div>

</div>

<div class="col-6 col-lg-3">

<div class="card bg-danger text-white text-center">

<div class="card-body">

<h2><?= $pending_payments; ?></h2>

<div>💳 Εκκρεμείς Πληρωμές</div>

</div>

</div>

</div>

<div class="col-6 col-lg-3">

<div class="card bg-success text-white text-center">

<div class="card-body">

<h2><?= number_format($total_income,2); ?>€</h2>

<div>💰 Εισπράξεις</div>

</div>

</div>

</div>

</div>

<?php foreach($trips as $trip): ?>

<?php

$trip_id = $trip['id'];

$total_students_stmt = $pdo->prepare("
SELECT COUNT(*)
FROM trip_signatures
WHERE trip_id=?
");
$total_students_stmt->execute([$trip_id]);
$total_students = $total_students_stmt->fetchColumn();

$total_signed_stmt = $pdo->prepare("
SELECT COUNT(*)
FROM trip_signatures
WHERE trip_id=?
AND status='approved'
");
$total_signed_stmt->execute([$trip_id]);
$total_signed = $total_signed_stmt->fetchColumn();

$total_paid_stmt = $pdo->prepare("
SELECT COUNT(*)
FROM trip_payments
WHERE trip_id=?
AND payment_status='paid'
");
$total_paid_stmt->execute([$trip_id]);
$total_paid = $total_paid_stmt->fetchColumn();

$progress = 0;

if($total_students>0){

    $signed_percent = ($total_signed/$total_students)*50;
    $paid_percent = ($total_paid/$total_students)*50;

    $progress = round($signed_percent+$paid_percent);

}

?>

<div class="trip-card">

<h3>

<a
href="trip_view.php?id=<?= $trip['id']; ?>"
class="text-decoration-none">

<?= htmlspecialchars($trip['title']); ?>

</a>

</h3>

<p class="mb-2">

📅 <strong><?= htmlspecialchars($trip['trip_date']); ?></strong>

</p>

<p class="mb-3">

💰 <strong><?= number_format($trip['cost'],2); ?> €</strong>

	</p>
	<div class="row text-center mb-3">

<div class="col-4">

<h5><?= $total_students; ?></h5>

<div>👨‍🎓 Μαθητές</div>

</div>

<div class="col-4">

<h5><?= $total_signed; ?></h5>

<div>✍️ Υπέγραψαν</div>

</div>

<div class="col-4">

<h5><?= $total_paid; ?></h5>

<div>💳 Πλήρωσαν</div>

</div>

</div>

<div class="progress mb-3">

<div
class="progress-bar progress-bar-striped progress-bar-animated bg-success"
style="width:<?= $progress; ?>%;">

<?= $progress; ?>%

</div>

</div>

<p class="mb-3">

<?= nl2br(htmlspecialchars($trip['description'])); ?>

</p>

<div class="row g-2">

<div class="col-md-3">

<a
href="trip_view.php?id=<?= $trip['id']; ?>"
class="btn btn-primary w-100">

👁️ Προβολή

</a>

</div>

<div class="col-md-3">

<a
href="trip_payments.php?trip_id=<?= $trip['id']; ?>"
class="btn btn-success w-100">

💳 Πληρωμές

</a>

</div>

<div class="col-md-3">

<a
href="send_trip_email.php?id=<?= $trip['id']; ?>"
class="btn btn-warning w-100">

📧 Email

</a>

</div>

<div class="col-md-3">

<a
href="trip_delete.php?id=<?= $trip['id']; ?>"
class="btn btn-danger w-100"
onclick="return confirm('<?= $LANG['delete_trip_confirm']; ?>');">

🗑️ Διαγραφή

</a>

</div>

</div>

</div>
	<?php endforeach; ?>

<div class="mt-4">

<a
href="dashboard.php"
class="btn btn-secondary w-100">

🏠 <?= $LANG['dashboard']; ?>

</a>

</div>

</div>

<script>

document.querySelectorAll('.progress-bar').forEach(function(bar){

    let value=parseInt(bar.innerText);

    if(value>=100){

        bar.classList.remove('bg-success');
        bar.classList.add('bg-primary');

    }else if(value>=70){

        bar.classList.remove('bg-success');
        bar.classList.add('bg-info');

    }else if(value>=40){

        bar.classList.remove('bg-success');
        bar.classList.add('bg-warning');
        bar.classList.remove('text-white');
        bar.classList.add('text-dark');

    }else{

        bar.classList.remove('bg-success');
        bar.classList.add('bg-danger');

    }

});

</script>

</body>

</html>
