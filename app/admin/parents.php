<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$stmt = $pdo->query("
SELECT *
FROM parents
ORDER BY last_name, first_name
");

$parents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<title><?= $LANG['parents']; ?></title>

<?php require_once '../includes/theme.php'; ?>

<style>
body{
background:#f4f6f9;
}
.parent-card{
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

<h2 class="mb-3">
👨‍👩‍👧 <?= $LANG['parents']; ?>
</h2>

<a href="parent_add.php"
class="btn sm-btn btn-add w-100 mb-3">

<i class="bi bi-person-plus-fill"></i>

<?= $LANG['new_parent']; ?>

	</a>

<?php foreach($parents as $parent): ?>

<div class="parent-card">

<h4>
<?= htmlspecialchars(
$parent['first_name'].' '.$parent['last_name']
); ?>
</h4>

<p>
📞 <?= htmlspecialchars($parent['phone'] ?? ''); ?>
</p>

<p>
📧 <?= htmlspecialchars($parent['email'] ?? ''); ?>
</p>

<p>
👨‍👩‍👧 <?= htmlspecialchars($parent['relationship'] ?? ''); ?>
</p>
<a
href="parent_view.php?id=<?= $parent['id']; ?>"
class="btn sm-btn btn-view w-100 mb-2">

<i class="bi bi-eye-fill"></i>
<?= $LANG['view']; ?>

</a>

<a
href="parent_edit.php?id=<?= $parent['id']; ?>"
class="btn sm-btn btn-edit w-100 mb-2">

<i class="bi bi-pencil-fill"></i>
<?= $LANG['edit']; ?>

</a>

<a
href="parent_delete.php?id=<?= $parent['id']; ?>"
class="btn sm-btn btn-delete w-100"
onclick="return confirm('<?= $LANG['delete_parent_confirm']; ?>')">

<i class="bi bi-trash-fill"></i>
<?= $LANG['delete']; ?>

</a>

</div>

<?php endforeach; ?>
<a
href="import_parents.php"
class="btn sm-btn btn-view w-100 mb-2">

<i class="bi bi-upload"></i>

<?= $LANG['import_parents']; ?>

</a>
<a href="dashboard.php"
class="btn btn-secondary w-100">

🏠 Αρχική

</a>

</div>

</body>
</html>
