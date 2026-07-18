<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$message = '';

if(isset($_POST['restore'])){

   require_once '../includes/common.php';

    $file = "../backups/".basename($_POST['backup_file']);

    if(file_exists($file)){

        $sql = file_get_contents($file);

        try{

            $pdo->exec($sql);

         $message = "restore_success";

        }catch(Exception $e){

   $message = $LANG['error'].": ".$e->getMessage();

        }

    }

}

$files = glob("../backups/*.sql");

rsort($files);

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $LANG['restore_backup']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
♻️ <?= $LANG['restore_backup']; ?>
</h2>

<?php if($message!=''): ?>

<div class="alert alert-info">

<?php
if($message == 'restore_success'){
    echo $LANG['restore_success'];
}else{
    echo htmlspecialchars($message);
}
?>

</div>

<?php endif; ?>

<div class="card-box">

<form method="post">

<label class="mb-2">

<?= $LANG['select_backup']; ?>

</label>

<select
name="backup_file"
class="form-control mb-3"
required>

<?php foreach($files as $file): ?>

<option value="<?= basename($file); ?>">

<?= basename($file); ?>

</option>

<?php endforeach; ?>

</select>

<button
type="submit"
name="restore"
class="btn btn-warning w-100"
onclick="return confirm('<?= $LANG['restore_backup_confirm']; ?>');">

♻️ <?= $LANG['restore_backup']; ?>

</button>

</form>

</div>

<br>

<a
href="backups.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>