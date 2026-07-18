<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$stmt->execute([$user_id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$teacher_id = $user['teacher_id'];

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO daily_lessons
    (
teacher_id,
class_id,
section_id,
lesson_title,
lesson_text,
homework,
lesson_date
)
    VALUES
    (
?,?,?,?,?,?,CURDATE()
)
    ");

    $class_stmt = $pdo->prepare("
SELECT class_id
FROM sections
WHERE id=?
");

$class_stmt->execute([
    $_POST['section_id']
]);

$class = $class_stmt->fetch(PDO::FETCH_ASSOC);

$stmt->execute([
    $teacher_id,
    $class['class_id'],
    $_POST['section_id'],
    $_POST['lesson_title'],
    $_POST['lesson_text'],
    $_POST['homework']
]);
	$lesson_id = $pdo->lastInsertId();

if(!empty($_FILES['lesson_files']['name'][0])){

    foreach($_FILES['lesson_files']['name'] as $key => $file_name){

        if(empty($file_name)){
            continue;
        }

        $new_name =
        time().'_'.$key.'_'.basename($file_name);

        move_uploaded_file(
            $_FILES['lesson_files']['tmp_name'][$key],
            '../uploads/lessons/'.$new_name
        );

        $file_stmt = $pdo->prepare("
        INSERT INTO lesson_files
        (
            lesson_id,
            file_name
        )
        VALUES
        (
            ?,?
        )
        ");

        $file_stmt->execute([
            $lesson_id,
            $new_name
        ]);
    }
}
    header("Location: my_lessons.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Νέο Μάθημα</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-4">

<h2>📚 Νέο Μάθημα Ημέρας</h2>

<?php if(!empty($success)): ?>

<div class="alert alert-success">

<?= $success ?>

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
ON s.id = ts.section_id
INNER JOIN classes c
ON c.id = ts.class_id
WHERE ts.teacher_id=?
ORDER BY c.name,s.name
");

$sections->execute([$teacher_id]);

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

<label>Μάθημα</label>

<input
type="text"
name="lesson_title"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Τι κάναμε σήμερα</label>

<textarea
name="lesson_text"
class="form-control"
rows="5"></textarea>

</div>

<div class="mb-3">

<div class="mb-3">

<label>Εργασία για το σπίτι</label>

<textarea
name="homework"
class="form-control"
rows="4"></textarea>

</div>

<div class="mb-3">

<label>Αρχείο Μαθήματος</label>

<input
type="file"
name="lesson_files[]"
class="form-control"
multiple>

</div>
</div>

<button
type="submit"
class="btn btn-success w-100">

💾 Αποθήκευση

</button>
<br>
<br>
<a
href="my_lessons.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή στα Μαθήματα

</a>
</form>

</div>

</body>
</html>