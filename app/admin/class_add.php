<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO classes(name)
    VALUES(?)
    ");

    $stmt->execute([
        $_POST['name']
    ]);

    header("Location: classes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['create_class']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">

<h2>📚 <?= $LANG['create_class']; ?></h2>

<form method="post">

<div class="mb-3">

<label><?= $LANG['class']; ?></label>

<input
type="text"
name="name"
class="form-control"
placeholder="<?= $LANG['class']; ?>"
required>

</div>

<button
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>

</form>

<br>

<a
href="classes.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>