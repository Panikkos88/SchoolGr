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
FROM teachers
WHERE id=?
");

$stmt->execute([$id]);

$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
$teacher_sections = [];

$q = $pdo->prepare("
SELECT section_id
FROM teacher_sections
WHERE teacher_id=?
");

$q->execute([$id]);

while($row = $q->fetch(PDO::FETCH_ASSOC)){
    $teacher_sections[] = $row['section_id'];
}


if(!$teacher){
    die("Ο καθηγητής δεν βρέθηκε");
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    UPDATE teachers
    SET
    first_name=?,
    last_name=?,
    specialty=?,
    phone=?,
    email=?
    WHERE id=?
    ");

    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['specialty'],
        $_POST['phone'],
        $_POST['email'],
        $id
    ]);
$delete = $pdo->prepare("
DELETE FROM teacher_sections
WHERE teacher_id=?
");

$delete->execute([$id]);

if(!empty($_POST['sections'])){

    foreach($_POST['sections'] as $section_id){

        $class_stmt = $pdo->prepare("
        SELECT class_id
        FROM sections
        WHERE id=?
        ");

        $class_stmt->execute([$section_id]);

        $class = $class_stmt->fetch(PDO::FETCH_ASSOC);

        $insert = $pdo->prepare("
        INSERT INTO teacher_sections
        (
            teacher_id,
            class_id,
            section_id
        )
        VALUES
        (
            ?,?,?
        )
        ");

        $insert->execute([
            $id,
            $class['class_id'],
            $section_id
        ]);
    }
}
    header("Location: teacher_view.php?id=".$id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $LANG['edit_teacher']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.form-card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">
	<h2>✏️ <?= $LANG['edit_teacher']; ?></h2>
<div class="form-card">

<form method="post">

<div class="mb-3">
	<label><?= $LANG['first_name']; ?></label>
<input
type="text"
name="first_name"
class="form-control"
value="<?= htmlspecialchars($teacher['first_name']); ?>"
required>
</div>

<div class="mb-3">
	<label><?= $LANG['last_name']; ?></label>
<input
type="text"
name="last_name"
class="form-control"
value="<?= htmlspecialchars($teacher['last_name']); ?>"
required>
</div>

<div class="mb-3">
	<label><?= $LANG['teacher_specialty']; ?></label>
<input
type="text"
name="specialty"
class="form-control"
value="<?= htmlspecialchars($teacher['specialty']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['phone']; ?></label>
<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($teacher['phone']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['email']; ?></label>
<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($teacher['email']); ?>">
</div>
<hr>
	<h5>📚 <?= $LANG['teacher_sections']; ?></h5>
<?php

$sections = $pdo->query("
SELECT
s.id,
c.name AS class_name,
s.name AS section_name
FROM sections s
INNER JOIN classes c
ON c.id=s.class_id
ORDER BY c.name,s.name
");

while($sec = $sections->fetch(PDO::FETCH_ASSOC)):
?>

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="sections[]"
value="<?= $sec['id']; ?>"

<?= in_array(
    $sec['id'],
    $teacher_sections
) ? 'checked' : ''; ?>

>

<label class="form-check-label">

<?= htmlspecialchars($sec['class_name']); ?>

-

<?= htmlspecialchars($sec['section_name']); ?>

</label>

</div>

<?php endwhile; ?>
<button
type="submit"
class="btn btn-success w-100">
💾 <?= $LANG['save']; ?>
</button>
<br><br>

<a
href="teacher_view.php?id=<?= $id; ?>"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

	</a>
</form>

</div>

</div>

</body>
</html>