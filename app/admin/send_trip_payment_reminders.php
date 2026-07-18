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

p.first_name AS parent_first_name,
p.last_name AS parent_last_name,
p.email,

s.first_name,
s.last_name,

tp.status

FROM trip_signatures ts

INNER JOIN parents p
ON p.id=ts.parent_id

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

$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);

$sent=0;
$noEmail=0;
$alreadyPaid=0;
foreach($rows as $row){

    if($row['status']=='paid'){

        $alreadyPaid++;
        continue;

    }

    if(empty($row['email'])){

        $noEmail++;
        continue;

    }

    $subject="Υπενθύμιση Πληρωμής Εκδρομής";

    $message="

    <h2>".htmlspecialchars($trip['title'])."</h2>

    <p>Αγαπητέ/ή ".$row['parent_first_name'].",</p>

    <p>Σας υπενθυμίζουμε ότι η πληρωμή της εκδρομής του μαθητή:</p>

    <p><strong>".$row['first_name']." ".$row['last_name']."</strong></p>

    <p>παραμένει σε εκκρεμότητα.</p>

    <p><strong>Ποσό:</strong> ".number_format($trip['cost'],2)." €</p>

    <p>Παρακαλούμε πραγματοποιήστε την πληρωμή το συντομότερο δυνατό.</p>

    <hr>

    <p>Με εκτίμηση,<br>
SchoolMedia</p>

    ";

    if(sendEmail(

        $row['email'],
        $row['parent_first_name'],
        $subject,
        $message

    )){

        $sent++;

    }

}
$school=$pdo->query("
SELECT *
FROM school_settings
LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>

Αποστολή Υπενθυμίσεων

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
max-width:700px;
margin:40px auto;
background:white;
padding:25px;
border-radius:18px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="card-box">

<h2>

📧 Υπενθυμίσεις Πληρωμής

</h2>

<hr>

<div class="alert alert-success">

✅ Στάλθηκαν

<strong>

<?= $sent; ?>

</strong>

email.

</div>

<div class="alert alert-info">

💰 Είχαν ήδη πληρώσει

<strong>

<?= $alreadyPaid; ?>

</strong>

γονείς.

</div>

<div class="alert alert-warning">

⚠️ Χωρίς email

<strong>

<?= $noEmail; ?>

</strong>

γονείς.

</div>

<div class="d-grid gap-2 mt-4">

<a
href="trip_payments.php?id=<?= $trip_id; ?>"
class="btn btn-primary">

⬅️ Επιστροφή στις Πληρωμές

</a>

<a
href="trip_view.php?id=<?= $trip_id; ?>"
class="btn btn-secondary">

🚌 Επιστροφή στην Εκδρομή

</a>

</div>

</div>

</body>

</html>
