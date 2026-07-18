<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$message='';

if(isset($_POST['import'])){

    if(($handle = fopen($_FILES['csv']['tmp_name'], "r")) !== FALSE){

        fgetcsv($handle,1000,",","\"","\\");

        while(($data = fgetcsv($handle,1000,",","\"","\\")) !== FALSE){

            $first_name  = trim($data[0]);
            $last_name   = trim($data[1]);
            $email       = trim($data[2]);
            $phone       = trim($data[3]);
            $address     = trim($data[4]);
            $relationship= trim($data[5]);
           $amka_child = trim($data[6]);

$student_stmt = $pdo->prepare("
SELECT id
FROM students
WHERE amka=?
LIMIT 1
");

$student_stmt->execute([$amka_child]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    continue;
}

$student_id = $student['id'];

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

            $stmt = $pdo->prepare("
            INSERT INTO parents
            (
                first_name,
                last_name,
                phone,
                email,
                address,
                relationship
            )
            VALUES
            (
                ?,?,?,?,?,?
            )
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $phone,
                $email,
                $address,
                $relationship
            ]);

            $parent_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare("
            INSERT INTO users
            (
                first_name,
                last_name,
                email,
                password,
                role,
                parent_id
            )
            VALUES
            (
                ?, ?, ?, ?, 'parent', ?
            )
            ");

            $stmt->execute([
    $first_name,
    $last_name,
    $email,
    password_hash('123456', PASSWORD_DEFAULT),
    $parent_id
]);

            $stmt = $pdo->prepare("
            INSERT INTO parent_students
            (
                parent_id,
                student_id
            )
            VALUES
            (
                ?,?
            )
            ");

            $stmt->execute([
                $parent_id,
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
<title><?= $LANG['import_parents']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-4">

<h2>👨‍👩‍👧 <?= $LANG['import_parents']; ?></h2>

<?php if($message): ?>
<div class="alert alert-success">
<?php if($message == 'success'): ?>
<?= $LANG['parents_import_completed']; ?>
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

📥 <?= $LANG['import_parents']; ?>

</button>

</form>
<br>

<a
href="parents.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</body>
</html>