<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$stmt = $pdo->query("
SELECT *
FROM school_settings
WHERE id=1
");

$school = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=='POST'){

    $logo = $school['school_logo'] ?? '';

    if(
        isset($_FILES['school_logo']) &&
        !empty($_FILES['school_logo']['name'])
    ){

        if(!is_dir('../uploads/logo')){
            mkdir('../uploads/logo',0755,true);
        }

        $filename =
        time().'_'.
        basename($_FILES['school_logo']['name']);

        move_uploaded_file(
            $_FILES['school_logo']['tmp_name'],
            '../uploads/logo/'.$filename
        );

        $logo = $filename;
    }

    $stmt = $pdo->prepare("
    UPDATE school_settings
    SET
    school_name=?,
    school_address=?,
    school_phone=?,
    school_email=?,
    school_director=?,
    school_logo=?
    WHERE id=1
    ");

    $stmt->execute([

        $_POST['school_name'],
        $_POST['school_address'],
        $_POST['school_phone'],
        $_POST['school_email'],
        $_POST['school_director'],
        $logo

    ]);

    header("Location: school_settings.php?success=1");
    exit;
}

$stmt = $pdo->query("
SELECT *
FROM school_settings
WHERE id=1
");

$school = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title><?= $LANG['school_information']; ?></title>

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

<h2 class="mb-4">
🏫 <?= $LANG['school_information']; ?>
</h2>

<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success">

<?= $LANG['changes_saved']; ?>

</div>

<?php endif; ?>

<div class="card-box">

<form
method="post"
enctype="multipart/form-data">

<div class="mb-3">

<label><?= $LANG['school_name']; ?></label>

<input
type="text"
name="school_name"
class="form-control"
value="<?= htmlspecialchars($school['school_name'] ?? ''); ?>">

</div>

<div class="mb-3">

<label><?= $LANG['address']; ?></label>

<input
type="text"
name="school_address"
class="form-control"
value="<?= htmlspecialchars($school['school_address'] ?? ''); ?>">

</div>

<div class="mb-3">

<label><?= $LANG['phone']; ?></label>

<input
type="text"
name="school_phone"
class="form-control"
value="<?= htmlspecialchars($school['school_phone'] ?? ''); ?>">

</div>

<div class="mb-3">

<label><?= $LANG['email']; ?></label>

<input
type="email"
name="school_email"
class="form-control"
value="<?= htmlspecialchars($school['school_email'] ?? ''); ?>">

</div>

<div class="mb-3">

<label><?= $LANG['director']; ?></label>

<input
type="text"
name="school_director"
class="form-control"
value="<?= htmlspecialchars($school['school_director'] ?? ''); ?>">

</div>

<div class="mb-3">

<label><?= $LANG['director']; ?></label>

<input
type="file"
name="school_logo"
class="form-control">

</div>

<?php if(!empty($school['school_logo'])): ?>

<div class="mb-3">

<img
src="../uploads/logo/<?= htmlspecialchars($school['school_logo']); ?>"
style="max-width:150px;">

</div>

<?php endif; ?>

<button
type="submit"
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>

</form>

</div>

<br>

<a
href="settings.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>