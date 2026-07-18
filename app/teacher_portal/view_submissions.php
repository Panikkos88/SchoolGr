<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$assignment_id = $_GET['id'] ?? 0;

$assignment_stmt = $pdo->prepare("
SELECT *
FROM assignments
WHERE id=?
LIMIT 1
");

$assignment_stmt->execute([
    $assignment_id
]);

$assignment = $assignment_stmt->fetch(PDO::FETCH_ASSOC);

if(!$assignment){
    exit('Η εργασία δεν βρέθηκε');
}
$students = $pdo->prepare("
SELECT
s.id,
s.first_name,
s.last_name
FROM students s
WHERE s.class_id=?
AND s.section_id=?
ORDER BY s.last_name,s.first_name
");

$students->execute([
    $assignment['class_id'],
    $assignment['section_id']
]);
if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    UPDATE assignment_submissions
    SET
    grade=?,
    teacher_comment=?
    WHERE id=?
    ");

    $stmt->execute([
        $_POST['grade'],
        $_POST['teacher_comment'],
        $_POST['submission_id']
    ]);

    header("Location: view_submissions.php?id=".$assignment_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Παραδόσεις Εργασίας</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
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

<h2>

📚

<?= htmlspecialchars($assignment['title']); ?>

</h2>

<hr>

<?php foreach($students as $student): ?>
<?php

$submission_stmt = $pdo->prepare("
SELECT *
FROM assignment_submissions
WHERE assignment_id=?
AND student_id=?
ORDER BY id DESC
LIMIT 1
");

$submission_stmt->execute([
    $assignment_id,
    $student['id']
]);

$submission =
$submission_stmt->fetch(PDO::FETCH_ASSOC);

?>
<div class="card">

<h5>

👨‍🎓

<?= htmlspecialchars(
$student['first_name'].' '.$student['last_name']
); ?>
<?php if($submission): ?>

<div class="alert alert-success">

✅ Παρέδωσε εργασία

</div>

<?php else: ?>

<div class="alert alert-danger">

❌ Δεν παρέδωσε εργασία

</div>

<?php endif; ?>
</h5>

<p>

📅

<?= date(
'd/m/Y H:i',
strtotime($submission['submitted_at'])
); ?>

</p>

<hr>

<?php

$files = $pdo->prepare("
SELECT *
FROM submission_files
WHERE submission_id=?
");

$files->execute([
    $submission['id']
]);

foreach($files as $file):
?>

<a
href="../uploads/submissions/<?= urlencode($file['file_name']); ?>"
target="_blank"
class="btn btn-primary btn-sm mb-1">

📎

<?= htmlspecialchars($file['file_name']); ?>

</a>

<br>

<?php endforeach; ?>
<hr>

<form method="post">

<input
type="hidden"
name="submission_id"
value="<?= $submission['id']; ?>">

<label>
⭐ Βαθμός
</label>

<input
type="text"
name="grade"
class="form-control"
value="<?= htmlspecialchars($submission['grade']); ?>">

<br>

<label>
💬 Σχόλιο
</label>

<textarea
name="teacher_comment"
class="form-control"
rows="3"><?= htmlspecialchars($submission['teacher_comment']); ?></textarea>

<br>

<button
type="submit"
class="btn btn-success">

💾 Αποθήκευση

</button>

	</form>
</div>

<?php endforeach; ?>

<a
href="assignments.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>