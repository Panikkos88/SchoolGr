<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$stmt = $pdo->query("
SELECT *
FROM announcements
ORDER BY created_at DESC
");

$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Ανακοινώσεις</title>

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

<h2 class="mb-4">
📢 Ανακοινώσεις
</h2>

<?php foreach($announcements as $row): ?>

<div class="card-box">

<h4>
<?= htmlspecialchars($row['title']); ?>
</h4>

<hr>

<p>
<?= nl2br(htmlspecialchars($row['content'])); ?>
</p>

<small class="text-muted">
<?= $row['created_at']; ?>
</small>

</div>

<?php endforeach; ?>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>