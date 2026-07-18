<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM trips
WHERE id=?
");

$stmt->execute([$id]);

$trip = $stmt->fetch(PDO::FETCH_ASSOC);
$isDirector=false;
$isLeader=false;

if($_SESSION['role']=='director'){

    $isDirector=true;

}

if($_SESSION['role']=='teacher'){

    $teacher=$pdo->prepare("
    SELECT teacher_id
    FROM users
    WHERE id=?
    ");

    $teacher->execute([
        $_SESSION['user_id']
    ]);

    $teacher=$teacher->fetch(PDO::FETCH_ASSOC);

    if($teacher){

        if($teacher['teacher_id']==$trip['leader_teacher_id']){

            $isLeader=true;

        }

    }

}
if(isset($_POST['trip_status'])){

    $status=$_POST['trip_status'];

    $upd=$pdo->prepare("
    UPDATE trips
    SET trip_status=?
    WHERE id=?
    ");

    $upd->execute([
        $status,
        $id
    ]);

    $parents=$pdo->prepare("
    SELECT
    p.email,
    p.first_name,
    s.first_name AS student_name
    FROM trip_signatures ts
    INNER JOIN parents p
    ON p.id=ts.parent_id
    INNER JOIN students s
    ON s.id=ts.student_id
    WHERE ts.trip_id=?
    ");

    $parents->execute([$id]);

    $parents=$parents->fetchAll(PDO::FETCH_ASSOC);

    switch($status){

        case 'departed':
            $subject="Αναχώρηση Εκδρομής";
            $message="Το λεωφορείο αναχώρησε από το σχολείο.";
        break;

        case 'arrived':
            $subject="Άφιξη στην Εκδρομή";
            $message="Οι μαθητές έφτασαν με ασφάλεια στον προορισμό.";
        break;

        case 'returning':
            $subject="Αναχώρηση από την Εκδρομή";
            $message="Το λεωφορείο αναχώρησε για την επιστροφή.";
        break;

        case 'finished':
            $subject="Άφιξη στο Σχολείο";
            $message="Οι μαθητές επέστρεψαν με ασφάλεια στο σχολείο.";
        break;

        default:
            $subject="";
            $message="";
    }
echo "Μπήκα εδώ";
exit;
    foreach($parents as $parent){
        if(empty($parent['email'])){
            continue;
        }

        sendEmail(
            $parent['email'],
            $parent['first_name'],
            $subject,
            "
            <h2>".$trip['title']."</h2>
            <p>".$message."</p>
            <p><strong>Μαθητής:</strong> ".$parent['student_name']."</p>
            <p>SchoolMedia</p>
            "
        );
    }

    header("Location: trip_view.php?id=".$id);
    exit;
}
if(!$trip){
    die($LANG['trip_not_found']);
}

$target_name = $LANG['all_school'];

if($trip['target_type']=='class'){

    $q = $pdo->prepare("
    SELECT name
    FROM classes
    WHERE id=?
    ");

    $q->execute([$trip['target_id']]);

    $c = $q->fetch(PDO::FETCH_ASSOC);

    $target_name = $LANG['class_prefix'].' '.$c['name'];

}elseif($trip['target_type']=='section'){

    $q = $pdo->prepare("
    SELECT name
    FROM sections
    WHERE id=?
    ");

    $q->execute([$trip['target_id']]);

    $s = $q->fetch(PDO::FETCH_ASSOC);

    $target_name = $LANG['section_prefix'].' '.$s['name'];

}

$sig_stmt = $pdo->prepare("
SELECT

ts.*,

s.first_name,
s.last_name,

hc.medical_conditions,
hc.allergies,

p.first_name AS parent_first_name,
p.last_name AS parent_last_name

FROM trip_signatures ts

LEFT JOIN students s
ON s.id = ts.student_id

LEFT JOIN student_health_cards hc
ON hc.student_id=s.id

LEFT JOIN parents p
ON p.id=ts.parent_id

WHERE ts.trip_id=?

ORDER BY s.last_name,s.first_name
");

$sig_stmt->execute([$id]);

$participants = $sig_stmt->fetchAll(PDO::FETCH_ASSOC);
$history = [];
$approved=0;
$rejected=0;
$pending=0;
$health_alerts=0;

foreach($participants as $p){

    if($p['status']=='approved'){
        $approved++;
    }elseif($p['status']=='rejected'){
        $rejected++;
    }else{
        $pending++;
    }

    if(
        trim($p['medical_conditions'] ?? '')!='' ||
        trim($p['allergies'] ?? '')!=''
    ){
        $health_alerts++;
    }

}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['trips']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:#fff;
padding:20px;
border-radius:18px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-bottom:20px;
}

.stat-card{
border-radius:15px;
padding:15px;
text-align:center;
font-weight:bold;
font-size:18px;
margin-bottom:15px;
}

</style>

</head>

<body>

<div class="container py-3">

<div class="card-box">

<h2>

🚌 <?= htmlspecialchars($trip['title']); ?>

</h2>
<?php if($isDirector || $isLeader): ?>

<form method="post" class="mb-4">
<div class="row g-2">

<div class="col-md-3">

<button
name="trip_status"
value="departed"
class="btn btn-primary w-100">

🚌 Αναχώρηση

</button>

</div>

<div class="col-md-3">

<button
name="trip_status"
value="arrived"
class="btn btn-success w-100">

📍 Άφιξη

</button>

</div>

<div class="col-md-3">

<button
name="trip_status"
value="returning"
class="btn btn-warning w-100">

↩ Επιστροφή

</button>

</div>

<div class="col-md-3">

<button
name="trip_status"
value="finished"
class="btn btn-dark w-100">

🏫 Άφιξη Σχολείο

</button>

</div>

</div>

	</form>
	<?php endif; ?>
<hr>

<p>

📅 <strong>Ημερομηνία:</strong>

<?= htmlspecialchars($trip['trip_date']); ?>

</p>

<p>

💰 <strong>Κόστος:</strong>

<?= number_format($trip['cost'],2); ?> €

</p>

<p>

🎯 <strong>Αποδέκτες:</strong>

<?= htmlspecialchars($target_name); ?>

</p>

<?php if(!empty($trip['description'])): ?>

<p>

<?= nl2br(htmlspecialchars($trip['description'])); ?>

</p>

<?php endif; ?>

<div class="d-grid gap-2 mt-3">

<a
send_trip_reminders.php?id=<?= $trip['id']; ?>
class="btn btn-primary">

📧 Αποστολή Email

</a>

<a
href="trip_delete.php?id=<?= $trip['id']; ?>"
class="btn btn-danger"
onclick="return confirm('<?= $LANG['delete_trip_confirm']; ?>');">

🗑️ Διαγραφή Εκδρομής

</a>

</div>

</div>

<div class="row">

<div class="col-6">

<div class="stat-card bg-success text-white">

✅<br>

<?= $approved; ?>

<br>

Εγκρίθηκαν

</div>

</div>

<div class="col-6">

<div class="stat-card bg-warning">

⏳<br>

<?= $pending; ?>

<br>

Εκκρεμούν

</div>

</div>

<div class="col-6">

<div class="stat-card bg-danger text-white">

❌<br>

<?= $rejected; ?>

<br>

Απορρίφθηκαν

</div>

</div>

<div class="col-6">

<div class="stat-card bg-info text-white">

❤️<br>

<?= $health_alerts; ?>

<br>

Ιατρικές Ειδοποιήσεις

</div>

</div>

</div>

<div class="alert alert-primary text-center">

👨‍🎓

<strong>

Σύνολο Συμμετεχόντων:

<?= count($participants); ?>

</strong>

</div>

<div class="card-box">

<h4>

👨‍🎓 Συμμετέχοντες

</h4>

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead>

<tr>

<th>Μαθητής</th>

<th>Γονέας</th>

<th>Υπογραφή</th>

<th>❤️</th>

<th>Κάρτα</th>

</tr>

</thead>

<tbody>
	<?php foreach($participants as $p): ?>

<tr>

<td>

👨‍🎓

<?= htmlspecialchars($p['first_name'].' '.$p['last_name']); ?>

</td>

<td>

<?= htmlspecialchars(
($p['parent_first_name'] ?? '').
' '.
($p['parent_last_name'] ?? '')
); ?>

</td>

<td>

<?php

if($p['status']=='approved'){

?>

<span class="badge bg-success">

✅ Εγκρίθηκε

</span>

<?php

}elseif($p['status']=='rejected'){

?>

<span class="badge bg-danger">

❌ Απορρίφθηκε

</span>

<?php

}else{

?>

<span class="badge bg-warning text-dark">

⏳ Εκκρεμεί

</span>

<?php

}

?>

</td>

<td>

<?php

if(

trim($p['medical_conditions'] ?? '')!='' ||

trim($p['allergies'] ?? '')!=''

){

?>

<span class="badge bg-danger">

🔴 Προσοχή

</span>

<?php

}else{

?>

<span class="badge bg-success">

🟢 OK

</span>

<?php

}

?>

</td>

<td>

<a

href="student_health.php?id=<?= $p['student_id']; ?>"

class="btn btn-danger btn-sm">

❤️ Κάρτα

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>
	<div class="d-grid gap-2">

<a
href="trip_companion_print.php?id=<?= $trip['id']; ?>"
class="btn btn-primary">

🖨️ Εκτύπωση Συνοδού

</a>

<a
href="trip_health_print.php?id=<?= $trip['id']; ?>"
class="btn btn-danger">

❤️ Εκτύπωση Καρτών Υγείας

</a>

<a
href="send_trip_reminders.php?id=<?= $trip['id']; ?>"
class="btn btn-warning">

📧 Υπενθύμιση σε Γονείς

</a>

<a
href="trip_payments.php?id=<?= $trip['id']; ?>"
class="btn btn-success">

💳 Πληρωμές Εκδρομής

</a>
<div class="card-box">

<h4>

🕒 Χρονολόγιο Εκδρομής

</h4>

<?php if(count($history)==0): ?>

<div class="alert alert-secondary">

Δεν υπάρχουν ακόμη ενημερώσεις.

</div>

<?php else: ?>

<table class="table table-striped">

<thead>

<tr>

<th>Ώρα</th>

<th>Κατάσταση</th>

<th>Χρήστης</th>

</tr>

</thead>

<tbody>

<?php foreach($history as $h): ?>

<tr>

<td>

<?= date('d/m/Y H:i',strtotime($h['changed_at'])) ?>

</td>

<td>

<?php

switch($h['status']){

case 'departed':

echo "🚌 Αναχώρηση από το σχολείο";

break;

case 'arrived':

echo "📍 Άφιξη στην εκδρομή";

break;

case 'returning':

echo "↩️ Αναχώρηση επιστροφής";

break;

case 'finished':

echo "🏫 Άφιξη στο σχολείο";

break;

default:

echo $h['status'];

}

?>

</td>

<td>

<?= htmlspecialchars($h['full_name'] ?? '-') ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

		</div>
<a
href="trips.php"
class="btn btn-secondary">

⬅️ Επιστροφή στις Εκδρομές

</a>

</div>

</div>

</body>

</html>