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
FROM students
WHERE id=?
");

$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);
$classes = $pdo->query("
SELECT *
FROM classes
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

$sections = $pdo->query("
SELECT *
FROM sections
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

$buses = $pdo->query("
SELECT *
FROM buses
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);
if(!$student){
    die("Δεν βρέθηκε μαθητής");
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    UPDATE students
SET
first_name=?,
last_name=?,
father_name=?,
birth_date=?,
email=?,
phone=?,
class_id=?,
section_id=?,
bus_id=?,
amka=?
WHERE id=?
    ");

    $stmt->execute([
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['father_name'],
    $_POST['birth_date'],
    $_POST['email'],
    $_POST['phone'],
    $_POST['class_id'],
    $_POST['section_id'],
    $_POST['bus_id'],
    $_POST['amka'],
    $id
]);

    header("Location: student_view.php?id=".$id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?= $LANG['edit_student']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-4">

<h2>
✏️ <?= $LANG['edit_student']; ?>
	</h2>
<form method="post">

<div class="mb-3">
	<label><?= $LANG['first_name']; ?></label>
<input type="text"
name="first_name"
class="form-control"
value="<?= htmlspecialchars($student['first_name']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['last_name']; ?></label>
<input type="text"
name="last_name"
class="form-control"
value="<?= htmlspecialchars($student['last_name']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['father_name']; ?></label>
<input type="text"
name="father_name"
class="form-control"
value="<?= htmlspecialchars($student['father_name']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['birth_date']; ?></label>
<input type="date"
name="birth_date"
class="form-control"
value="<?= htmlspecialchars($student['birth_date']); ?>">
</div>
<div class="mb-3">
	<label><?= $LANG['amka']; ?></label>
<input
type="text"
name="amka"
value="<?= htmlspecialchars($student['amka']); ?>"
class="form-control">
</div>
<div class="mb-3">
	<label><?= $LANG['email']; ?></label>
<input type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($student['email']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['phone']; ?></label>
<input type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($student['phone']); ?>">
</div>
<div class="mb-3">

<label><?= $LANG['class']; ?></label>

<select
name="class_id"
id="class_id"
class="form-select">

<?php foreach($classes as $class): ?>

<option
value="<?= $class['id']; ?>"
<?= $student['class_id']==$class['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($class['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-3">

<label><?= $LANG['section']; ?></label>

<select
name="section_id"
id="section_id"
class="form-select">

<?php foreach($sections as $section): ?>

<option
value="<?= $section['id']; ?>"
data-class="<?= $section['class_id']; ?>"
<?= $student['section_id']==$section['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($section['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-3">

<label><?= $LANG['bus']; ?></label>

<select
name="bus_id"
class="form-select">

<option value="">
<?= $LANG['without_bus']; ?>
</option>

<?php foreach($buses as $bus): ?>

<option
value="<?= $bus['id']; ?>"
<?= $student['bus_id']==$bus['id'] ? 'selected' : ''; ?>>

<?= htmlspecialchars($bus['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>
<button type="submit"
class="btn btn-success">

💾 Αποθήκευση

</button>

<a href="student_view.php?id=<?= $id; ?>"
class="btn btn-secondary">

Ακύρωση

</a>

</form>

</div>

</body>
	<script>

function filterSections(){

    const classId = document.getElementById('class_id').value;

    const options = document.querySelectorAll('#section_id option');

    let firstVisible = null;

    options.forEach(function(option){

        if(option.dataset.class == classId){

            option.hidden = false;

            if(firstVisible === null){
                firstVisible = option;
            }

        }else{

            option.hidden = true;

        }

    });

    if(firstVisible){
        firstVisible.selected = true;
    }

}

document.addEventListener('DOMContentLoaded', function(){

    filterSections();

    document
    .getElementById('class_id')
    .addEventListener('change', filterSections);

});

	</script>
</html>