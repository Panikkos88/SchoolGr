<?php
session_start();
require_once '../includes/common.php';
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['settings']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
text-align:center;
}

.card-box a{
text-decoration:none;
color:black;
}

.icon{
font-size:40px;
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
⚙️ <?= $LANG['settings']; ?>
</h2>

<div class="card-box">
<a href="school_settings.php">
<div class="icon">🏫</div>
<h5><?= $LANG['school_information']; ?></h5>
</a>
</div>

<div class="card-box">
<a href="email_settings.php">
<div class="icon">📧</div>
<h5><?= $LANG['email_settings']; ?></h5>
</a>
</div>

<div class="card-box">
<a href="backups.php">
<div class="icon">💾</div>
<h5><?= $LANG['backup']; ?></h5>
</a>
	</div>
<div class="card-box">
<a href="tuition_categories.php">
<div class="icon">💰</div>
<h5><?= $LANG['tuition_categories']; ?></h5>
</a>
</div>
<div class="card-box">
<a href="dashboard.php">
<div class="icon">⬅️</div>
<h5><?= $LANG['back']; ?></h5>
</a>
</div>

</div>

</body>
</html>