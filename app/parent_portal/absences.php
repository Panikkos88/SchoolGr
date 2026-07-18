<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$user_stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$parent_id = $user['parent_id'];

$student_stmt = $pdo->prepare("
SELECT s.*
FROM parent_students ps
INNER JOIN students s
ON s.id = ps.student_id
WHERE ps.parent_id=?
LIMIT 1
");

$student_stmt->execute([
    $parent_id
]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

$abs_stmt = $pdo->prepare("
SELECT *
FROM absences
WHERE student_id=?
ORDER BY absence_date DESC
");

$abs_stmt->execute([
    $student['id']
]);

$absences = $abs_stmt->fetchAll(PDO::FETCH_ASSOC);

$total_hours = 0;

foreach($absences as $a){
    $total_hours += $a['hours'];
}

?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Απουσίες</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2>📝 Απουσίες</h2>

<div class="alert alert-info">

Σύνολο Ωρών Απουσίας:
<strong><?= $total_hours; ?></strong>

</div>

<?php foreach($absences as $row): ?>

<div class="card-box">

<p>
📅 <?= $row['absence_date']; ?>
</p>

<p>
⏰ Ώρες:
<?= $row['hours']; ?>
</p>

<p>
📌 Λόγος:
<?= htmlspecialchars($row['reason']); ?>
</p>

</div>

<?php endforeach; ?>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>