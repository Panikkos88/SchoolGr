<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM parents
WHERE id=?
");

$stmt->execute([$id]);

$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Ο γονέας δεν βρέθηκε.");
}

$students = $pdo->prepare("
SELECT
s.*
FROM students s
INNER JOIN parent_students ps
ON s.id = ps.student_id
WHERE ps.parent_id=?
ORDER BY s.last_name,s.first_name
");

$students->execute([$id]);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $LANG['parent_details']; ?></title>
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

<div class="container py-3">

<h2>
👨‍👩‍👧 <?= $LANG['parent_details']; ?>
</h2>

<div class="card-box">
<p>
📞 <?= htmlspecialchars($parent['phone']); ?>
</p>

<p>
📧 <?= htmlspecialchars($parent['email']); ?>
</p>

<p>
<strong><?= $LANG['relationship']; ?>:</strong>
<?= htmlspecialchars($parent['relationship']); ?>
</p>

</div>

<div class="card-box">

<h4>
	👨‍🎓 <?= $LANG['children']; ?>
</h4>

<?php foreach($students as $student): ?>

<div class="mb-2">

<?= htmlspecialchars(
$parent['first_name'].' '.$parent['last_name']
); ?>

</div>

<?php endforeach; ?>

</div>

<a
href="parents.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>