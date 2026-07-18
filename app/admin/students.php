<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

require_once '../includes/common.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$search = trim($_GET['search'] ?? '');
$class_filter = $_GET['class_id'] ?? '';
$section_filter = $_GET['section_id'] ?? '';

$classes = $pdo->query("
SELECT *
FROM classes
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

$sections = $pdo->query("
SELECT *
FROM sections
ORDER BY class_id,name
")->fetchAll(PDO::FETCH_ASSOC);
$sql = "
SELECT
students.*,
classes.name AS class_name,
sections.name AS section_name
FROM students
LEFT JOIN classes
ON students.class_id = classes.id
LEFT JOIN sections
ON students.section_id = sections.id
WHERE 1=1
";

$params = [];

if($search != ''){

    $sql .= "
    AND (
        students.first_name LIKE ?
        OR students.last_name LIKE ?
        OR students.phone LIKE ?
    )
    ";

    $like = "%".$search."%";

    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if($class_filter != ''){

    $sql .= "
    AND students.class_id = ?
    ";

    $params[] = $class_filter;
}

if($section_filter != ''){

    $sql .= "
    AND students.section_id = ?
    ";

    $params[] = $section_filter;
}

$sql .= "
ORDER BY students.last_name,
students.first_name
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<title><?= $LANG['students']; ?></title>

<?php require_once '../includes/theme.php'; ?>

<style>

body{
background:#f4f6f9;
}

.top-bar{
background:white;
padding:15px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.student-card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.avatar{
font-size:55px;
text-align:center;
}

</style>

</head>

<body>

<div class="container py-3">

<h2 class="mb-3">
👨‍🎓 <?= $LANG['students']; ?>
	</h2>

<div class="top-bar">

<form method="get">
<input
type="text"
name="search"
class="form-control mb-3"
placeholder="🔍 <?= $LANG['search_student']; ?>"
value="<?= htmlspecialchars($search); ?>">
<select
name="class_id"
id="class_id"
class="form-control mb-3"
onchange="this.form.submit()">
<option value="">
🏫 <?= $LANG['all_classes']; ?>
</option>

<?php foreach($classes as $class): ?>

<option
value="<?= $class['id']; ?>"
<?= $class_filter==$class['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($class['name']); ?>

</option>

<?php endforeach; ?>

</select>
	<select
name="section_id"
class="form-control mb-3">

<option value="">
📚 <?= $LANG['all_sections']; ?>
</option>

<?php

$sections = [];

if($class_filter!=''){

    $stmt = $pdo->prepare("
    SELECT *
    FROM sections
    WHERE class_id=?
    ORDER BY name
    ");

    $stmt->execute([$class_filter]);

    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

foreach($sections as $section):

?>

<option
value="<?= $section['id']; ?>"
<?= $section_filter==$section['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($section['name']); ?>

</option>

<?php endforeach; ?>

	</select>
	<button
type="submit"
class="btn sm-btn btn-view w-100 mb-3">

<i class="bi bi-search"></i>

<?= $LANG['search']; ?>

	</button>
	</form>

<a href="student_add.php"
class="btn sm-btn btn-add w-100">

<i class="bi bi-person-plus-fill"></i>

<?= $LANG['new_student']; ?>

	</a>

</div>

<?php foreach($students as $student): ?>

<div class="student-card">

<div class="avatar">
👨‍🎓
</div>

<h4 class="text-center">

<?= htmlspecialchars(
$student['first_name'].' '.$student['last_name']
); ?>

</h4>

<hr>

<p>
📞 <?= htmlspecialchars($student['phone'] ?? ''); ?>
</p>

<p>
🏫 <?= $LANG['class']; ?>:
<?= htmlspecialchars($student['class_name'] ?? ''); ?>
</p>

<p>
📚 <?= $LANG['section']; ?>:
<?= htmlspecialchars($student['section_name'] ?? ''); ?>
</p>

<p>
🆔 ID: <?= $student['id']; ?>
</p>

<div class="d-grid gap-2">

<a href="student_view.php?id=<?= $student['id']; ?>"
class="btn sm-btn btn-view">

<i class="bi bi-eye-fill"></i>

<?= $LANG['view']; ?>

	</a>

<a href="student_edit.php?id=<?= $student['id']; ?>"
class="btn sm-btn btn-edit">

<i class="bi bi-pencil-fill"></i>

<?= $LANG['edit']; ?>
<a href="student_delete.php?id=<?= $student['id']; ?>"
class="btn sm-btn btn-delete"
onclick="return confirm('<?= $LANG['delete_student_confirm']; ?>')">

<i class="bi bi-trash-fill"></i>

<?= $LANG['delete']; ?>

	</a>

</div>

</div>

<?php endforeach; ?>
<a href="import_students.php"
class="btn btn-primary">
📥 Import CSV
</a>
<a href="dashboard.php"
class="btn btn-secondary w-100">

🏠 Αρχική

</a>

</div>
</body>
</html>
