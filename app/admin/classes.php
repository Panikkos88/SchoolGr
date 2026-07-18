<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
if(isset($_POST['add_class'])){

    $check = $pdo->prepare("
    SELECT id
    FROM classes
    WHERE name=?
    ");

    $check->execute([
        trim($_POST['class_name'])
    ]);

    if($check->rowCount()==0){

        $stmt = $pdo->prepare("
        INSERT INTO classes(name)
        VALUES(?)
        ");

        $stmt->execute([
            trim($_POST['class_name'])
        ]);
    }

    header("Location: classes.php");
    exit;
}

if(isset($_POST['add_section'])){

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
        trim($_POST['section_name'])
    ]);

    header("Location: classes.php");
    exit;
}

$classes = $pdo->query("
SELECT *
FROM classes
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $LANG['classes']; ?></title>
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
	<h2>📚 <?= $LANG['classes']; ?></h2>
<div class="card-box">
	<h5>➕ <?= $LANG['create_class']; ?></h5>
<form method="post">

<input
type="text"
name="class_name"
class="form-control mb-2"
placeholder="<?= $LANG['class']; ?>"
required>

<button
type="submit"
name="add_class"
class="btn btn-success">
💾 <?= $LANG['create_class']; ?>
</button>

</form>

</div>

<div class="card-box">
	<h5>➕ <?= $LANG['create_section']; ?></h5>
<form method="post">

<select
name="class_id"
class="form-control mb-2"
required>

<option value="">
<?= $LANG['select_class']; ?>
</option>

<?php foreach($classes as $c): ?>

<option value="<?= $c['id']; ?>">

<?= htmlspecialchars($c['name']); ?>

</option>

<?php endforeach; ?>

</select>

<input
type="text"
name="section_name"
class="form-control mb-2"
placeholder="<?= $LANG['section']; ?>"
required>

<button
type="submit"
name="add_section"
class="btn btn-success">
💾 <?= $LANG['save_section']; ?>
</button>

</form>

</div>

<?php foreach($classes as $class): ?>

<?php

$sections_stmt = $pdo->prepare("
SELECT *
FROM sections
WHERE class_id=?
ORDER BY name
");

$sections_stmt->execute([$class['id']]);

$sections = $sections_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="card-box">

<h4>
📚 <?= htmlspecialchars($class['name']); ?>
</h4>
<a
href="class_delete.php?id=<?= $class['id']; ?>"
class="btn btn-danger btn-sm mb-2"
onclick="return confirm('<?= $LANG['delete_class_confirm']; ?>');">
🗑️ <?= $LANG['delete_class']; ?>
</a>
<?php foreach($sections as $section): ?>

<?php

$count_stmt = $pdo->prepare("
SELECT COUNT(*)
FROM students
WHERE section_id=?
");

$count_stmt->execute([$section['id']]);

$total_students = $count_stmt->fetchColumn();

?>

<div class="border rounded p-2 mb-2">

<strong>
🏫 <?= htmlspecialchars($section['name']); ?>
</strong>

<br>

👨‍🎓 <?= $LANG['total_students']; ?>:
<?= $total_students; ?>
<br><br>

<a
href="section_delete.php?id=<?= $section['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('<?= $LANG['delete_section_confirm']; ?>');">
🗑️ <?= $LANG['delete_section']; ?>
	</a>
</div>

<?php endforeach; ?>

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