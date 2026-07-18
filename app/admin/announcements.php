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
FROM announcements
ORDER BY created_at DESC
");

$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['announcements']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-3">
📢 <?= $LANG['announcements']; ?>
</h2>

<a href="announcement_add.php"
class="btn btn-success w-100 mb-3">
➕ <?= $LANG['new_announcement']; ?></a>

<?php foreach($announcements as $row): ?>

<div class="card-box">

<h4>
<?= htmlspecialchars($row['title']); ?>
</h4>

<hr>

<p>
<?= nl2br(htmlspecialchars($row['content'])); ?>
</p>

<p class="text-muted">
📅 <?= $row['created_at']; ?>
</p>

<?php if($row['target_type']=='school'): ?>

<span class="badge bg-primary">
<?= $LANG['whole_school']; ?>
</span>

<?php elseif($row['target_type']=='class'): ?>

<span class="badge bg-success">
<?= $LANG['class']; ?>
</span>

<?php elseif($row['target_type']=='section'): ?>

<span class="badge bg-warning text-dark">
<?= $LANG['section']; ?>
</span>

<?php endif; ?>

<br><br>

<a
href="announcement_delete.php?id=<?= $row['id']; ?>"
class="btn btn-danger"
onclick="return confirm('<?= $LANG['delete_announcement_confirm']; ?>')">
🗑️ <?= $LANG['delete_announcement']; ?></a>

</div>

<?php endforeach; ?>

<a href="dashboard.php"
class="btn btn-secondary w-100">

🏠 <?= $LANG['dashboard']; ?>

</a>

</div>

</body>
</html>