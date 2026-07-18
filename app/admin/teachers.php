<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$stmt = $pdo->query("
SELECT *
FROM teachers
ORDER BY last_name, first_name
");

$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<title><?= $LANG['teachers']; ?></title>

<?php require_once '../includes/theme.php'; ?>

<style>

body{
background:#f4f6f9;
}

.teacher-card{
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
<h2>👨‍🏫 <?= $LANG['teachers']; ?></h2>
<a
href="teacher_add.php"
class="btn sm-btn btn-add w-100 mb-3">

<i class="bi bi-person-plus-fill"></i>

<?= $LANG['new_teacher']; ?>

	</a>

<?php foreach($teachers as $teacher): ?>

<div class="teacher-card">

<h4 class="text-center">

👨‍🏫

<?= htmlspecialchars($teacher['first_name']); ?>
<?= htmlspecialchars($teacher['last_name']); ?>

	</h4>
<p>
<strong><?= $LANG['teacher_specialty']; ?>:</strong>
<?= htmlspecialchars($teacher['specialty']); ?>
</p>

<p>
📞 <?= htmlspecialchars($teacher['phone']); ?>
</p>

<p>
📧 <?= htmlspecialchars($teacher['email']); ?>
</p>
<div class="mt-3">

<div class="d-grid gap-2 mt-3">

<a
href="teacher_view.php?id=<?= $teacher['id']; ?>"
class="btn sm-btn btn-view">

<i class="bi bi-eye-fill"></i>

<?= $LANG['view']; ?>

</a>

<a
href="teacher_edit.php?id=<?= $teacher['id']; ?>"
class="btn sm-btn btn-edit">

<i class="bi bi-pencil-fill"></i>

<?= $LANG['edit']; ?>

</a>

<a
href="teacher_delete.php?id=<?= $teacher['id']; ?>"
class="btn sm-btn btn-delete"
onclick="return confirm('<?= $LANG['delete_teacher_confirm']; ?>');">

<i class="bi bi-trash-fill"></i>

<?= $LANG['delete']; ?>

</a>

	</div>

</div>
</div>

<?php endforeach; ?>

<a
href="dashboard.php"
class="btn btn-secondary w-100">
🏠 <?= $LANG['dashboard']; ?>
</a>

</div>

</body>
</html>