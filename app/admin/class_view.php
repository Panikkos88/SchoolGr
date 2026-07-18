<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM classes
WHERE id=?
");

$stmt->execute([$id]);

$class = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$class){
die($LANG['class_not_found']);
}

$sections_stmt = $pdo->prepare("
SELECT *
FROM sections
WHERE class_id=?
ORDER BY name
");

$sections_stmt->execute([$id]);

$sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);

$students_stmt = $pdo->prepare("
SELECT COUNT(*)
FROM students
WHERE class_id=?
");

$students_stmt->execute([$id]);

$total_students = $students_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['class']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">

<h2>
📚 <?= htmlspecialchars($class['name']); ?>
</h2>

<div class="alert alert-info">

👨‍🎓 <?= $LANG['students_count']; ?>:
<strong><?= $total_students; ?></strong>

</div>

<a
href="section_add.php?class_id=<?= $class['id']; ?>"
class="btn btn-success mb-3">

➕ <?= $LANG['new_section']; ?>

</a>

<?php foreach($sections as $section): ?>

<?php

$count_stmt = $pdo->prepare("
SELECT COUNT(*)
FROM students
WHERE section_id=?
");

$count_stmt->execute([$section['id']]);

$section_students = $count_stmt->fetchColumn();

?>

<div class="card mb-2">

<div class="card-body">

<strong>
🏫 <?= htmlspecialchars($section['name']); ?>
</strong>

<br>

👨‍🎓 <?= $LANG['students_count']; ?>:
<?= $section_students; ?>

</div>

</div>

<?php endforeach; ?>

<a
href="classes.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>
