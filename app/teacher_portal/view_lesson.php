<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT
dl.*,
t.first_name,
t.last_name
FROM daily_lessons dl
LEFT JOIN teachers t
ON t.id = dl.teacher_id
WHERE dl.id=?
LIMIT 1
");

$stmt->execute([$id]);

$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$lesson){
    die('Το μάθημα δεν βρέθηκε');
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Προβολή Μαθήματος</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}
</style>

</head>
<body>

<div class="container py-4">

<div class="card-box">

<h2>
📚 <?= htmlspecialchars($lesson['lesson_title']); ?>
</h2>

<p>
📅 <?= date('d/m/Y', strtotime($lesson['lesson_date'])); ?>
</p>

<hr>

<h5>📝 Τι κάναμε σήμερα</h5>

<p>
<?= nl2br(htmlspecialchars($lesson['lesson_text'])); ?>
</p>

<hr>

<h5>🏠 Εργασία για το σπίτι</h5>

<p>
<?= nl2br(htmlspecialchars($lesson['homework'])); ?>
</p>

<hr>

<h5>📎 Αρχεία</h5>

<?php

$files = $pdo->prepare("
SELECT *
FROM lesson_files
WHERE lesson_id=?
");

$files->execute([$id]);

while($file = $files->fetch(PDO::FETCH_ASSOC)):
?>

<div class="mb-2">

<a
href="../uploads/lessons/<?= urlencode($file['file_name']); ?>"
target="_blank"
class="btn btn-primary btn-sm">

👁️ Άνοιγμα

</a>

<a
href="../uploads/lessons/<?= urlencode($file['file_name']); ?>"
download
class="btn btn-success btn-sm">

⬇️ Κατέβασμα

</a>

<?= htmlspecialchars($file['file_name']); ?>

</div>

<?php endwhile; ?>

<hr>

<p>

👨‍🏫

<?= htmlspecialchars(
$lesson['first_name'].' '.$lesson['last_name']
); ?>

</p>

<a
href="my_lessons.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</div>

</body>
</html>