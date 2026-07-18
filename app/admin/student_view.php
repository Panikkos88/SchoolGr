<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id=(int)($_GET['id'] ?? 0);

$stmt=$pdo->prepare("
SELECT
students.*,
classes.name AS class_name,
sections.name AS section_name
FROM students
LEFT JOIN classes
ON students.class_id=classes.id
LEFT JOIN sections
ON students.section_id=sections.id
WHERE students.id=?
");

$stmt->execute([$id]);

$student=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Δεν βρέθηκε μαθητής");
}

$parents_stmt=$pdo->prepare("
SELECT p.*
FROM parent_students ps
INNER JOIN parents p
ON p.id=ps.parent_id
WHERE ps.student_id=?
");

$parents_stmt->execute([$id]);

$parents=$parents_stmt->fetchAll(PDO::FETCH_ASSOC);

$health_stmt=$pdo->prepare("
SELECT *
FROM student_health_cards
WHERE student_id=?
LIMIT 1
");

$health_stmt->execute([$id]);

$health=$health_stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title><?= $LANG['student_card']; ?></title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.profile-card{
background:#fff;
padding:25px;
border-radius:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-bottom:20px;
}
.tab-content{
    margin-top:0 !important;
}

.info-box{
    margin-top:0 !important;
}
.info-box{
background:transparent;
padding:0;
border-radius:0;
box-shadow:none;
margin:0;
}

.avatar{
font-size:80px;
text-align:center;
}

.nav-tabs .nav-link{
font-weight:bold;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="profile-card">

<div class="avatar">
👨‍🎓
</div>

<h2 class="text-center">

<?= htmlspecialchars($student['first_name'].' '.$student['last_name']); ?>

</h2>

<hr>

<p><strong>Πατρώνυμο:</strong>
<?= htmlspecialchars($student['father_name'] ?? '') ?>
</p>

<p><strong>Τάξη:</strong>
<?= htmlspecialchars($student['class_name'] ?? '') ?>
</p>

<p><strong>Τμήμα:</strong>
<?= htmlspecialchars($student['section_name'] ?? '') ?>
</p>

<p><strong>Τηλέφωνο:</strong>
<?= htmlspecialchars($student['phone'] ?? '') ?>
</p>

<p><strong>Email:</strong>
<?= htmlspecialchars($student['email'] ?? '') ?>
</p>

</div>

<ul class="nav nav-tabs mb-0">

<li class="nav-item">
<button
class="nav-link active"
data-bs-toggle="tab"
data-bs-target="#parents">

👨‍👩‍👧 Γονείς

</button>
</li>

<li class="nav-item">
<button
class="nav-link"
data-bs-toggle="tab"
data-bs-target="#health">

❤️ Κάρτα Υγείας
<?php if($health): ?>
<span class="badge bg-success ms-1">✓</span>
<?php else: ?>
<span class="badge bg-danger ms-1">!</span>
<?php endif; ?>

</button>
</li>

<li class="nav-item">
<button
class="nav-link"
data-bs-toggle="tab"
data-bs-target="#trips">

🚌 <?= $LANG['trips']; ?>

</button>
</li>

<li class="nav-item">
<button
class="nav-link"
data-bs-toggle="tab"
data-bs-target="#absences">

📝 <?= $LANG['attendance']; ?>

</button>
</li>

<li class="nav-item">
<button
class="nav-link"
data-bs-toggle="tab"
data-bs-target="#finance">

💰 Οικονομικά

</button>
</li>

</ul>

<div class="tab-content border border-top-0 p-3 bg-white rounded-bottom">
	<div class="tab-pane fade show active" id="parents">

<div class="info-box">

<h4>👨‍👩‍👧 <?= $LANG['parents']; ?></h4>

<a
href="connect_parent.php?student_id=<?= $student['id']; ?>"
class="btn btn-success mb-3">

➕ <?= $LANG['connect_parent']; ?>

</a>

<?php if(count($parents)>0): ?>

<?php foreach($parents as $parent): ?>

<div class="card mb-3">

<div class="card-body">

<h5>

<?= htmlspecialchars($parent['first_name']); ?>

<?= htmlspecialchars($parent['last_name']); ?>

</h5>

<p>

📞 <?= htmlspecialchars($parent['phone'] ?? ''); ?>

</p>

<p>

📧 <?= htmlspecialchars($parent['email'] ?? ''); ?>

</p>

<p>

👨‍👩‍👧 Σχέση:

<?= htmlspecialchars($parent['relationship'] ?? ''); ?>

</p>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="alert alert-warning">

<?= $LANG['no_parents']; ?>

</div>

<?php endif; ?>

</div>

</div>

<div class="tab-pane fade" id="health">

<div class="info-box">

<h4>❤️ Κάρτα Υγείας</h4>

<div class="d-flex gap-2 mb-3 flex-wrap">

<div class="d-flex gap-2 flex-wrap mb-3">

<a
href="student_health.php?id=<?= $student['id']; ?>"
class="btn btn-danger">

✏️ Επεξεργασία Κάρτας Υγείας

</a>

<a
href="student_health_files.php?id=<?= $student['id']; ?>"
class="btn btn-primary">

📎 Ιατρικά Έγγραφα

</a>

</div>


</div>

<?php if($health): ?>

<table class="table table-striped table-hover table-bordered align-middle">

<tr>

<th width="35%">Ομάδα Αίματος</th>

<td><?= htmlspecialchars($health['blood_group'] ?? '') ?></td>

</tr>

<tr>

<th>Παθήσεις</th>

<td><?= nl2br(htmlspecialchars($health['medical_conditions'] ?? '')) ?></td>

</tr>

<tr>

<th>Αλλεργίες</th>

<td><?= nl2br(htmlspecialchars($health['allergies'] ?? '')) ?></td>

</tr>

<tr>

<th>Φάρμακα</th>

<td><?= nl2br(htmlspecialchars($health['medications'] ?? '')) ?></td>

</tr>

<tr>

<th>Περιορισμοί</th>

<td><?= nl2br(htmlspecialchars($health['doctor_restrictions'] ?? '')) ?></td>

</tr>

<tr>

<th>Θεράπων Ιατρός</th>

<td><?= htmlspecialchars($health['doctor_name'] ?? '') ?></td>

</tr>

<tr>

<th>Ειδικότητα</th>

<td><?= htmlspecialchars($health['doctor_specialty'] ?? '') ?></td>

</tr>

<tr>

<th>Τηλέφωνο Ιατρού</th>

<td><?= htmlspecialchars($health['doctor_phone'] ?? '') ?></td>

</tr>

<tr>

<th>Ασφαλιστική</th>

<td><?= htmlspecialchars($health['insurance_company'] ?? '') ?></td>

</tr>

<tr>

<th>Αρ. Ασφαλιστηρίου</th>

<td><?= htmlspecialchars($health['insurance_number'] ?? '') ?></td>

</tr>

<tr>

<th>Επαφή Έκτακτης Ανάγκης</th>

<td><?= htmlspecialchars($health['emergency_contact_name'] ?? '') ?></td>

</tr>

<tr>

<th>Σχέση</th>

<td><?= htmlspecialchars($health['emergency_contact_relationship'] ?? '') ?></td>

</tr>

<tr>

<th>Τηλέφωνο 1</th>

<td><?= htmlspecialchars($health['emergency_contact_phone1'] ?? '') ?></td>

</tr>

<tr>

<th>Τηλέφωνο 2</th>

<td><?= htmlspecialchars($health['emergency_contact_phone2'] ?? '') ?></td>

</tr>

<tr>

<th>Παρατηρήσεις</th>

<td><?= nl2br(htmlspecialchars($health['notes'] ?? '')) ?></td>

</tr>

</table>

<?php else: ?>

<div class="alert alert-warning">

Δεν υπάρχει ακόμη καταχωρημένη κάρτα υγείας.

<br><br>

<a
href="student_health.php?id=<?= $student['id']; ?>"
class="btn btn-danger">

➕ Δημιουργία Κάρτας Υγείας

</a>

</div>

<?php endif; ?>
</div>
</div>

	<div class="tab-pane fade" id="trips">

<div class="info-box">

<h4>🚌 <?= $LANG['trips']; ?></h4>

<?php

$trip_stmt=$pdo->prepare("
SELECT
t.id AS trip_id,
t.title,
t.trip_date,
ts.status,
ts.signed_at
FROM trip_signatures ts
INNER JOIN trips t
ON t.id=ts.trip_id
WHERE ts.student_id=?
ORDER BY t.trip_date DESC
");

$trip_stmt->execute([$student['id']]);

$trips=$trip_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if(count($trips)>0): ?>

<?php foreach($trips as $trip): ?>

<div class="card mb-2">

<div class="card-body">

<a
href="trip_document.php?student_id=<?= $student['id']; ?>&trip_id=<?= $trip['trip_id']; ?>">

<strong>

<?= htmlspecialchars($trip['title']); ?>

</strong>

</a>

<br>

📅 <?= htmlspecialchars($trip['trip_date']); ?>

<br>

<?= $LANG['status']; ?>:

<strong>

<?= htmlspecialchars($trip['status']); ?>

</strong>

<?php if(!empty($trip['signed_at'])): ?>

<br>

Εγκρίθηκε:

<?= htmlspecialchars($trip['signed_at']); ?>

<?php endif; ?>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="alert alert-warning">

<?= $LANG['no_trips']; ?>

</div>

<?php endif; ?>

</div>
</div>
<div class="tab-pane fade" id="absences">

<div class="info-box">

<h4>📝 <?= $LANG['attendance']; ?></h4>

<a
href="absence_add.php?student_id=<?= $student['id']; ?>"
class="btn btn-success mb-3">

➕ <?= $LANG['new_absence']; ?>

</a>

<?php

$abs_stmt=$pdo->prepare("
SELECT *
FROM absences
WHERE student_id=?
ORDER BY absence_date DESC
");

$abs_stmt->execute([$student['id']]);

$absences=$abs_stmt->fetchAll(PDO::FETCH_ASSOC);

$total_hours=0;

foreach($absences as $a){

$total_hours+=(int)$a['hours'];

}

?>

<div class="alert alert-info">

<?= $LANG['total_absence_hours']; ?>:

<strong>

<?= $total_hours; ?>

</strong>

</div>

<?php if(count($absences)>0): ?>

<?php foreach($absences as $a): ?>

<div class="card mb-2">

<div class="card-body">

<strong>

<?= htmlspecialchars($a['absence_date']); ?>

</strong>

<br>

<?= $LANG['hours']; ?>:

<?= htmlspecialchars($a['hours']); ?>

<br>

<?= htmlspecialchars($a['reason'] ?? ''); ?>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="alert alert-warning">

Δεν υπάρχουν καταχωρημένες απουσίες.

</div>

<?php endif; ?>

</div>

</div>
	<div class="tab-pane fade" id="finance">

<div class="info-box">

<h4>💰 Οικονομικά</h4>

<a
href="finance_add.php?student_id=<?= $student['id']; ?>"
class="btn btn-success mb-3">

➕ Νέα Χρέωση / Πληρωμή

</a>

<?php

$fstmt=$pdo->prepare("
SELECT *
FROM student_finance
WHERE student_id=?
ORDER BY charge_date DESC,id DESC
");

$fstmt->execute([$student['id']]);

$rows=$fstmt->fetchAll(PDO::FETCH_ASSOC);

$total_charges=0;
$total_payments=0;

foreach($rows as $row){

    if($row['type']=='charge'){
        $total_charges+=$row['amount'];
    }

    if($row['type']=='payment'){
        $total_payments+=$row['amount'];
    }

}

$balance=$total_charges-$total_payments;

?>

<div class="alert alert-info">

Χρεώσεις:
<strong><?= number_format($total_charges,2); ?> €</strong>

<br>

Πληρωμές:
<strong><?= number_format($total_payments,2); ?> €</strong>

<br>

Υπόλοιπο:
<strong><?= number_format($balance,2); ?> €</strong>

</div>

<?php if(count($rows)>0): ?>

<?php foreach($rows as $row): ?>

<div class="card mb-2">

<div class="card-body">

<strong>

<?= htmlspecialchars($row['charge_date']); ?>

</strong>

<br>

<?= htmlspecialchars($row['description']); ?>

<br>

<?= number_format($row['amount'],2); ?> €

<br>

<?= htmlspecialchars($row['type']); ?>

</div>

</div>

<?php endforeach; ?>

<?php else: ?>

<div class="alert alert-warning">

Δεν υπάρχουν οικονομικές κινήσεις.

</div>

<?php endif; ?>

</div>

</div>

</div>

<a
href="students.php"
class="btn btn-secondary w-100 mt-3">

⬅️ Επιστροφή

</a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.addEventListener("DOMContentLoaded", function () {

    if(window.location.hash){

        var trigger=document.querySelector(
            '[data-bs-target="'+window.location.hash+'"]'
        );

        if(trigger){

            var tab=new bootstrap.Tab(trigger);
            tab.show();

        }

    }

});

</script>
	<!-- Floating Buttons -->
<button
onclick="window.location.href='../dashboard.php';"
class="btn btn-success rounded-circle shadow"

style="
width:65px;
height:65px;
display:flex;
align-items:center;
justify-content:center;
font-size:26px;
">

🏠

</button>

<button
onclick="history.back();"
class="btn btn-primary rounded-circle shadow"

style="
width:65px;
height:65px;
display:flex;
align-items:center;
justify-content:center;
font-size:30px;
">

←

</button>

<button
onclick="window.scrollTo({
top:0,
behavior:'smooth'
});"

class="btn btn-dark rounded-circle shadow"

style="
width:65px;
height:65px;
display:flex;
align-items:center;
justify-content:center;
font-size:24px;
">

⬆️

</button>

</div>
</body>

</html>