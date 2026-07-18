<?php

session_start();

require_once '../config/database.php';

$student_id = (int)($_GET['student_id'] ?? 0);
$trip_id = (int)($_GET['trip_id'] ?? 0);

$stmt = $pdo->prepare("
SELECT
t.*,
ts.status,
ts.signed_at,
ts.signature_image,
s.first_name,
s.last_name
FROM trip_signatures ts
INNER JOIN trips t ON t.id=ts.trip_id
INNER JOIN students s ON s.id=ts.student_id
WHERE ts.student_id=?
AND ts.trip_id=?
LIMIT 1
");

$stmt->execute([
    $student_id,
    $trip_id
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    die('Δεν βρέθηκε.');
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<title>Έγγραφο Εκδρομής</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container py-4">

<h2>🚌 <?= htmlspecialchars($row['title']); ?></h2>

<p>
<b>Μαθητής:</b>
<?= htmlspecialchars($row['first_name']); ?>
<?= htmlspecialchars($row['last_name']); ?>
</p>

<p>
<b>Ημερομηνία:</b>
<?= htmlspecialchars($row['trip_date']); ?>
</p>

<p>
<?= nl2br(htmlspecialchars($row['description'])); ?>
</p>

<hr>

<p>
<b>Κατάσταση:</b>
<?= htmlspecialchars($row['status']); ?>
</p>

<?php if(!empty($row['signed_at'])): ?>

<p>
<b>Εγκρίθηκε:</b>
<?= htmlspecialchars($row['signed_at']); ?>
</p>

<?php endif; ?>

<?php if(!empty($row['signature_image'])): ?>

<h4>Υπογραφή Γονέα</h4>

<img
src="<?= $row['signature_image']; ?>"
style="max-width:500px;width:100%;border:1px solid #ccc;">

<?php endif; ?>
<br>

<a
href="javascript:history.back();"
class="btn btn-secondary">

⬅️ Επιστροφή

	</a>
</div>

</body>
</html>