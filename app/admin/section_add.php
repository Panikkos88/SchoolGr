<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$classes = $pdo->query("
SELECT *
FROM classes
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO sections
    (
        class_id,
        name
    )
    VALUES
    (
        ?,?
    )
    ");

    $stmt->execute([
        $_POST['class_id'],
        $_POST['name']
    ]);

    header("Location: sections.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $LANG['new_section']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">
	<h2>🏫 <?= $LANG['new_section']; ?></h2>

<form method="post">
	<label><?= $LANG['class']; ?></label>
<select
name="class_id"
class="form-control mb-3"
required>

<?php foreach($classes as $class): ?>

<option value="<?= $class['id']; ?>">
<?= htmlspecialchars($class['name']); ?>
</option>

<?php endforeach; ?>

</select>
	<label><?= $LANG['section']; ?></label>
<input
type="text"
name="name"
class="form-control mb-3"
placeholder="<?= $LANG['section']; ?>"
required>

<button
class="btn btn-success w-100">
💾 <?= $LANG['save']; ?>
</button>

</form>
<br>

<a
href="sections.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

	</a>
</div>

</body>
</html>