<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$user_stmt = $pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$teacher_id = $user['teacher_id'];

$lessons = $pdo->prepare("
SELECT *
FROM daily_lessons
WHERE teacher_id=?
ORDER BY lesson_date DESC,id DESC
");

$lessons->execute([
    $teacher_id
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
<div class="create-card">

<a
href="add_lesson.php"
class="btn btn-success w-100 mb-3">

➕ Δημιουργία Νέου Μαθήματος

</a>

</div>
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

<a
href="edit_lesson.php?id=<?= $lesson['id']; ?>"
class="btn btn-warning btn-sm">

✏️ Επεξεργασία

</a>
<a
href="view_lesson.php?id=<?= $lesson['id']; ?>"
class="btn btn-primary btn-sm">

👁️ Προβολή

</a>
<a
href="delete_lesson.php?id=<?= $lesson['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Να διαγραφεί το μάθημα;');">

🗑 Διαγραφή

</a>

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