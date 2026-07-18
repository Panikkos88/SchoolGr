<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();
require_once '../includes/common.php';
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$files = glob("../backups/*.sql");

rsort($files);

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['backup']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:15px;
border-radius:15px;
margin-bottom:10px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2>💾 <?= $LANG['database_backups']; ?></h2>

<a
href="create_backup.php"
class="btn btn-success mb-3">

➕ <?= $LANG['create_backup']; ?>

</a>

<?php if(count($files)==0): ?>

<div class="alert alert-warning">

<?= $LANG['no_backups']; ?>

</div>

<?php endif; ?>

<?php foreach($files as $file): ?>

<div class="card-box">

<strong>

<?= basename($file); ?>

</strong>

<br><br>

<a
href="<?= $file; ?>"
download
class="btn btn-primary btn-sm">

📥 <?= $LANG['download']; ?>

</a>

<a
href="delete_backup.php?file=<?= urlencode(basename($file)); ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('<?= $LANG['delete_backup_confirm']; ?>');">

🗑️ <?= $LANG['delete']; ?>

</a>

</div>

<?php endforeach; ?>

<a
href="settings.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>