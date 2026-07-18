<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';
$pdo->exec("SET NAMES utf8mb4");
$message='';

if(isset($_POST['import'])){

    if(($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE){

       fgetcsv($handle, 1000, ",", '"', "\\");

        while(($data = fgetcsv($handle, 1000, ",", '"', "\\")) !== FALSE){

$first_name = trim($data[0]);
$last_name  = trim($data[1]);
$email      = trim($data[2]);

$class_name = trim($data[3]);
$section_name = trim($data[4]);
$amka = trim($data[5]);

			$class_stmt = $pdo->prepare("
SELECT id
FROM classes
WHERE name=?
LIMIT 1
");

$class_stmt->execute([$class_name]);
$class = $class_stmt->fetch(PDO::FETCH_ASSOC);

if(!$class){
    continue;
}

$class_id = $class['id'];

$section_stmt = $pdo->prepare("
SELECT id
FROM sections
WHERE name=?
AND class_id=?
LIMIT 1
");

$section_stmt->execute([
    $section_name,
    $class_id
]);

$section = $section_stmt->fetch(PDO::FETCH_ASSOC);

if(!$section){
    continue;
}

$section_id = $section['id'];
            $stmt = $pdo->prepare("
INSERT INTO students
(
    first_name,
    last_name,
    email,
    class_id,
    section_id,
    amka
)
VALUES
(
    ?,?,?,?,?,?
)
");
$check = $pdo->prepare("
SELECT id
FROM users
WHERE email=?
LIMIT 1
");

$check->execute([$email]);

if($check->fetch()){
    continue;
}
            $stmt->execute([
    $first_name,
    $last_name,
    $email,
    $class_id,
    $section_id,
    $amka
]);

            $student_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("
            INSERT INTO users
            (
                first_name,
                last_name,
                email,
                password,
                role,
                student_id
            )
            VALUES
            (
                ?, ?, ?, ?, 'student', ?
            )
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $email,
                '123456',
                $student_id
            ]);
        }

        fclose($handle);

      $message = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $LANG['import_students']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-4">

<h2>📥 <?= $LANG['import_students']; ?></h2>

<?php if($message): ?>
<div class="alert alert-success">
<?php if($message == 'success'): ?>
<?= $LANG['import_completed']; ?>
<?php else: ?>
<?= htmlspecialchars($message); ?>
<?php endif; ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

<input
type="file"
name="csv"
class="form-control"
required>

<br>

<button
name="import"
class="btn btn-success">

📥 <?= $LANG['import']; ?>

</button>

</form>
<br>

<a
href="students.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</body>
</html>
