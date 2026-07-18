<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role']!='student'){
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

$student_id = $user['student_id'];

$student_stmt = $pdo->prepare("
SELECT *
FROM students
WHERE id=?
LIMIT 1
");

$student_stmt->execute([
    $student_id
]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

$lessons = $pdo->prepare("
SELECT
dl.*,
t.first_name,
t.last_name
FROM daily_lessons dl

LEFT JOIN teachers t
ON t.id = dl.teacher_id

WHERE dl.class_id=?
AND dl.section_id=?

ORDER BY dl.lesson_date DESC,
dl.id DESC
");

$lessons->execute([
    $student['class_id'],
    $student['section_id']
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Τα Μαθήματά μου</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.lesson-card{
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

<h2>📚 Τα Μαθήματά μου</h2>

<?php foreach($lessons as $lesson): ?>

<div class="lesson-card">

<h4>

<?= htmlspecialchars($lesson['lesson_title']); ?>

</h4>

<p>

📅

<?= date(
'd/m/Y',
strtotime($lesson['lesson_date'])
); ?>

</p>

<hr>

<p>

<strong>Τι κάναμε σήμερα:</strong>

<br>

<?= nl2br(
htmlspecialchars($lesson['lesson_text'])
); ?>

</p>

<p>

<strong>Εργασία για το σπίτι:</strong>

<br>

<?= nl2br(
htmlspecialchars($lesson['homework'])
); ?>

</p>

<hr>

<p>

👨‍🏫

<?= htmlspecialchars(
$lesson['first_name'].' '.$lesson['last_name']
); ?>

</p>
<?php

$files = $pdo->prepare("
SELECT *
FROM lesson_files
WHERE lesson_id=?
");

$files->execute([
    $lesson['id']
]);

if($files->rowCount() > 0):
?>

<hr>

<h6>📎 Αρχεία Μαθήματος</h6>

<?php while($file = $files->fetch(PDO::FETCH_ASSOC)): ?>

<div class="mb-2">

<strong>
📎 <?= htmlspecialchars($file['file_name']); ?>
</strong>

<br>

<a
href="../uploads/lessons/<?= urlencode($file['file_name']); ?>"
target="_blank"
class="btn btn-primary btn-sm">

👁 Άνοιγμα

</a>

<a
href="../uploads/lessons/<?= urlencode($file['file_name']); ?>"
download
class="btn btn-success btn-sm">

⬇️ Κατέβασμα

</a>

</div>
<?php endwhile; ?>

<?php endif; ?>
</div>

<?php endforeach; ?>

<a
href="index.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>