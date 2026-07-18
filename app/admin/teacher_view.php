<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$id = (int)($_GET['id'] ?? 0);
$sections_stmt = $pdo->prepare("
SELECT
c.name AS class_name,
s.name AS section_name
FROM teacher_sections ts
INNER JOIN classes c
ON c.id = ts.class_id
INNER JOIN sections s
ON s.id = ts.section_id
WHERE ts.teacher_id=?
ORDER BY c.name,s.name
");
$stmt = $pdo->prepare("
SELECT *
FROM teachers
WHERE id=?
");

$stmt->execute([$id]);

$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
$sections_stmt->execute([$id]);

$teacher_sections =
$sections_stmt->fetchAll(PDO::FETCH_ASSOC);
if(!$teacher){
    die("Ο εκπαιδευτικός δεν βρέθηκε");
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $LANG['teacher_card']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.profile-card{
background:white;
padding:25px;
border-radius:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.avatar{
font-size:90px;
text-align:center;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="profile-card">

<div class="avatar">
👨‍🏫
</div>
<h2 class="text-center">

<?= htmlspecialchars($teacher['first_name']); ?>
<?= htmlspecialchars($teacher['last_name']); ?>

</h2>
<hr>

<p>
	<strong><?= $LANG['phone']; ?>:</strong>
<?= htmlspecialchars($teacher['phone']); ?>
</p>

<hr>
<h4>📚 <?= $LANG['teacher_sections']; ?></h4>
<?php if(count($teacher_sections)>0): ?>

<ul>

<?php foreach($teacher_sections as $sec): ?>

<li>

<?= htmlspecialchars($sec['class_name']); ?>

-

<?= htmlspecialchars($sec['section_name']); ?>

</li>

<?php endforeach; ?>

</ul>

<?php else: ?>

<div class="alert alert-warning">
<?= $LANG['no_teacher_sections']; ?>
</div>

<?php endif; ?>
<p>
	<strong><?= $LANG['teacher_position']; ?>:</strong>
<?= htmlspecialchars($teacher['position']); ?>
</p>
<p>
	<strong><?= $LANG['created_at']; ?>:</strong>
<?= htmlspecialchars($teacher['created_at']); ?>
</p>

<br>

<a
href="teacher_edit.php?id=<?= $teacher['id']; ?>"
class="btn btn-warning w-100 mb-2">
	✏️ <?= $LANG['edit']; ?>
</a>
<a
href="teacher_delete.php?id=<?= $teacher['id']; ?>"
class="btn btn-danger w-100 mb-2"
onclick="return confirm('<?= $LANG['delete_teacher_confirm']; ?>');">

🗑️ <?= $LANG['delete']; ?>

	</a>
<a
href="teachers.php"
class="btn btn-secondary w-100">
⬅️ <?= $LANG['back']; ?>
</a>

</div>

</div>

</body>
</html>