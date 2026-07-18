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

if(
    $_SERVER['REQUEST_METHOD']=='POST'
    && !empty($_POST['assignment_id'])
){

    $stmt = $pdo->prepare("
    INSERT INTO assignment_submissions
    (
        assignment_id,
        student_id
    )
    VALUES
    (
        ?,?
    )
    ");

    $stmt->execute([
        $_POST['assignment_id'],
        $student_id
    ]);

    $submission_id =
    $pdo->lastInsertId();

    if(
        !empty($_FILES['files']['name'][0])
    ){

        foreach(
            $_FILES['files']['name']
            as $key=>$file_name
        ){

            if(empty($file_name)){
                continue;
            }

            $new_name =
            time().'_'.$key.'_'.
            basename($file_name);

            move_uploaded_file(
                $_FILES['files']['tmp_name'][$key],
                '../uploads/submissions/'.$new_name
            );

            $file_stmt = $pdo->prepare("
            INSERT INTO submission_files
            (
                submission_id,
                file_name
            )
            VALUES
            (
                ?,?
            )
            ");

            $file_stmt->execute([
                $submission_id,
                $new_name
            ]);
        }
    }

    $success = true;
}

$assignments = $pdo->prepare("
SELECT *
FROM assignments
WHERE class_id=?
AND section_id=?
ORDER BY id DESC
");

$assignments->execute([
    $student['class_id'],
    $student['section_id']
]);
?>

<!DOCTYPE html>
<html lang='el'>
<head>

<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>

<title>Εργασίες</title>

<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>

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

<h2>📚 Εργασίες</h2>

<?php if(!empty($success)): ?>

<div class="alert alert-success">

Η εργασία υποβλήθηκε.

</div>

<?php endif; ?>

<?php foreach($assignments as $assignment): ?>

<div class="card">

<h4>

<?= htmlspecialchars($assignment['title']); ?>

</h4>

<p>

<?= nl2br(
htmlspecialchars($assignment['description'])
); ?>

</p>

<p>

📅 Παράδοση:

<?= date(
'd/m/Y',
strtotime($assignment['due_date'])
); ?>

</p>

<?php if(!empty($assignment['file_name'])): ?>

<a
href="../uploads/assignments/<?= urlencode($assignment['file_name']); ?>"
class="btn btn-primary btn-sm"
target="_blank">

📥 Κατέβασμα Αρχείου

</a>

<?php endif; ?>

<hr>
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
    $assignment['id'],
    $student_id
]);

$submission =
$submission_stmt->fetch(PDO::FETCH_ASSOC);

if($submission):
?>

<hr>

<p>

✅ Έχει υποβληθεί εργασία

</p>

<?php if(!empty($submission['grade'])): ?>

<div class="alert alert-success">

<strong>
⭐ Βαθμός:
</strong>

<?= htmlspecialchars($submission['grade']); ?>

</div>

<?php endif; ?>

<?php if(!empty($submission['teacher_comment'])): ?>
<form
method="post"
enctype="multipart/form-data">
<div class="alert alert-info">

<strong>
💬 Σχόλιο Καθηγητή:
</strong>

<br>

<?= nl2br(
htmlspecialchars(
$submission['teacher_comment']
)
); ?>

</div>

<?php endif; ?>

<?php endif; ?>


<input
type="hidden"
name="assignment_id"
value="<?= $assignment['id']; ?>">

<label>

Υποβολή Αρχείων

</label>

<input
type="file"
name="files[]"
class="form-control"
multiple
required>

<br>

<button
type="submit"
class="btn btn-success">

⬆️ Υποβολή

</button>

</form>

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