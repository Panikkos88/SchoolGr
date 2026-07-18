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

if($_SERVER['REQUEST_METHOD']=='POST'){

    $file_name = null;

    if(!empty($_FILES['assignment_file']['name'])){

        $file_name =
        time().'_'.
        basename($_FILES['assignment_file']['name']);

        move_uploaded_file(
            $_FILES['assignment_file']['tmp_name'],
            '../uploads/assignments/'.$file_name
        );
    }

    $class_stmt = $pdo->prepare("
    SELECT class_id
    FROM sections
    WHERE id=?
    LIMIT 1
    ");

    $class_stmt->execute([
        $_POST['section_id']
    ]);

    $class = $class_stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
    INSERT INTO assignments
    (
        teacher_id,
        class_id,
        section_id,
        title,
        description,
        due_date,
        file_name
    )
    VALUES
    (
        ?,?,?,?,?,?,?
    )
    ");

    $stmt->execute([
        $teacher_id,
        $class['class_id'],
        $_POST['section_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['due_date'],
        $file_name
    ]);
$students = $pdo->prepare("
SELECT u.id
FROM students s

INNER JOIN users u
ON u.student_id=s.id

WHERE s.class_id=?
AND s.section_id=?
");

$students->execute([
    $class['class_id'],
    $_POST['section_id']
]);

foreach($students as $student){

    $notif = $pdo->prepare("
    INSERT INTO notifications
    (
        user_id,
        title,
        message
    )
    VALUES
    (
        ?,?,?
    )
    ");

    $notif->execute([
        $student['id'],
        '📚 Νέα Εργασία',
        'Αναρτήθηκε νέα εργασία: '.$_POST['title']
    ]);
}
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Νέα Εργασία</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container py-4">

<h2>📚 Νέα Εργασία</h2>

<?php if(!empty($success)): ?>

<div class="alert alert-success">

Η εργασία αποθηκεύτηκε.

</div>

<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<div class="mb-3">

<label>Τμήμα</label>

<select
name="section_id"
class="form-control"
required>

<?php

$sections = $pdo->prepare("
SELECT
s.id,
s.name,
c.name AS class_name
FROM teacher_sections ts
INNER JOIN sections s
ON s.id=ts.section_id
INNER JOIN classes c
ON c.id=ts.class_id
WHERE ts.teacher_id=?
");

$sections->execute([
    $teacher_id
]);

while($section = $sections->fetch(PDO::FETCH_ASSOC)):
?>

<option value="<?= $section['id']; ?>">

<?= htmlspecialchars($section['class_name']); ?>

-

<?= htmlspecialchars($section['name']); ?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label>Τίτλος Εργασίας</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Περιγραφή</label>

<textarea
name="description"
class="form-control"
rows="5"></textarea>

</div>

<div class="mb-3">

<label>Ημερομηνία Παράδοσης</label>

<input
type="date"
name="due_date"
class="form-control">

</div>

<div class="mb-3">

<label>Αρχείο Εργασίας</label>

<input
type="file"
name="assignment_file"
class="form-control">

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 Αποθήκευση

</button>

</form>

</div>

</body>
</html>