<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$stmt = $pdo->prepare("
SELECT *
FROM notifications
WHERE user_id=?
ORDER BY id DESC
");

$stmt->execute([
    $_SESSION['user_id']
]);

$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdo->prepare("
UPDATE notifications
SET is_read=1
WHERE user_id=?
")->execute([
    $_SESSION['user_id']
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Ειδοποιήσεις</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
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

<h2>🔔 Ειδοποιήσεις</h2>

<?php foreach($notifications as $notification): ?>

<div class="card">

<h5>
<?= htmlspecialchars($notification['title']); ?>
</h5>

<p>
<?= nl2br(htmlspecialchars($notification['message'])); ?>
</p>

<small class="text-muted">

<?= date(
'd/m/Y H:i',
strtotime($notification['created_at'])
); ?>

</small>

</div>

<?php endforeach; ?>

<a
href="index.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>