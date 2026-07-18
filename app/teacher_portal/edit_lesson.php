<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM daily_lessons
WHERE id=?
LIMIT 1
");

$stmt->execute([$id]);

$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$lesson){
    die('Το μάθημα δεν βρέθηκε');
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $update = $pdo->prepare("
    UPDATE daily_lessons
    SET
    lesson_title=?,
    lesson_text=?,
    homework=?
    WHERE id=?
    ");

    $update->execute([
        $_POST['lesson_title'],
        $_POST['lesson_text'],
        $_POST['homework'],
        $id
    ]);

    header("Location: my_lessons.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Επεξεργασία Μαθήματος</title>

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

<h2>✏️ Επεξεργασία Μαθήματος</h2>
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
            $id,
            $new_name
        ]);
    }
}
<form method="post" enctype="multipart/form-data">

<div class="mb-3">

<label>Μάθημα</label>

<input
type="text"
name="lesson_title"
class="form-control"
value="<?= htmlspecialchars($lesson['lesson_title']); ?>"
required>

</div>

<div class="mb-3">

<label>Τι κάναμε σήμερα</label>

<textarea
name="lesson_text"
class="form-control"
rows="6"><?= htmlspecialchars($lesson['lesson_text']); ?></textarea>

</div>

<div class="mb-3">

<label>Εργασία για το σπίτι</label>

<textarea
name="homework"
class="form-control"
rows="4"><?= htmlspecialchars($lesson['homework']); ?></textarea>

</div>
<hr>

<h5>📎 Αρχεία Μαθήματος</h5>

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
target="_blank">

📎 <?= htmlspecialchars($file['file_name']); ?>

</a>

<a
href="delete_lesson_file.php?id=<?= $file['id']; ?>&lesson_id=<?= $id; ?>"
class="btn btn-danger btn-sm ms-2"
onclick="return confirm('Να διαγραφεί το αρχείο;');">

🗑

</a>

</div>

<?php endwhile; ?>

<hr>

<h5>➕ Νέα Αρχεία</h5>

<input
type="file"
name="lesson_files[]"
class="form-control"
multiple>
<button
type="submit"
class="btn btn-success w-100">

💾 Αποθήκευση

</button>

</form>

<br>

<a
href="my_lessons.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</div>

</body>
</html>