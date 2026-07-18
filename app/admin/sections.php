<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$stmt = $pdo->query("
SELECT
sections.*,
classes.name AS class_name
FROM sections
LEFT JOIN classes
ON classes.id = sections.class_id
ORDER BY classes.name, sections.name
");

$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $LANG['sections']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">
	<h2>🏫 <?= $LANG['sections']; ?></h2>
<a
href="section_add.php"
class="btn btn-success mb-3">
➕ <?= $LANG['new_section']; ?>
</a>

<?php foreach($sections as $section): ?>

<div class="card mb-2">
<div class="card-body">

<strong>
<?= htmlspecialchars($section['name']); ?>
</strong>

<br>
<?= $LANG['class']; ?>:
<?= htmlspecialchars($section['class_name']); ?>

</div>
</div>

<?php endforeach; ?>

<a
href="dashboard.php"
class="btn btn-secondary w-100">
⬅️ <?= $LANG['back']; ?>
</a>

</div>

</body>
</html>