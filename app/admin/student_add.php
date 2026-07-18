<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/common.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

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

$error='';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

    $email = trim($_POST['email'] ?? '');

    $check = $pdo->prepare("
    SELECT id
    FROM users
    WHERE email=?
    LIMIT 1
    ");

    $check->execute([$email]);

    if($check->fetch()){

        throw new Exception("Το email χρησιμοποιείται ήδη.");

	}
$amka = trim($_POST['amka'] ?? '');

if($amka!=''){

    $check = $pdo->prepare("
    SELECT id
    FROM students
    WHERE amka=?
    LIMIT 1
    ");

    $check->execute([$amka]);

    if($check->fetch()){

        throw new Exception("Το ΑΜΚΑ υπάρχει ήδη.");

    }

}

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $amka = trim($_POST['amka'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $class_id = (int)($_POST['class_id'] ?? 0);
    $section_id = (int)($_POST['section_id'] ?? 0);

    $bus_id = !empty($_POST['bus_id'])
        ? (int)$_POST['bus_id']
        : null;

    $stmt = $pdo->prepare("
    INSERT INTO students
    (
        first_name,
        last_name,
        father_name,
        birth_date,
        amka,
        address,
        email,
        phone,
        class_id,
        section_id,
        bus_id
    )
    VALUES
    (
        ?,?,?,?,?,?,?,?,?,?,?
    )
    ");

    $stmt->execute([
        $first_name,
        $last_name,
        $father_name,
        $birth_date,
        $amka,
        $address,
        $email,
        $phone,
        $class_id,
        $section_id,
        $bus_id
    ]);

    $student_id = (int)$pdo->lastInsertId();

    $user = $pdo->prepare("
    INSERT INTO users
(
    first_name,
    last_name,
    email,
    password,
    role,
    student_id,
    must_change_password
)
    VALUES
  (
    ?, ?, ?, ?, 'student', ?, 1
)
    ");

   $tempPassword = '123456';

$user->execute([
    $first_name,
    $last_name,
    $email,
    password_hash($tempPassword, PASSWORD_DEFAULT),
    $student_id
]);

       header('Location: students.php');
exit;

    } catch (Exception $e) {

    $error = $e->getMessage();

}

}

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<title><?= $LANG['new_student']; ?></title>

<?php require_once '../includes/theme.php'; ?>

<style>

body{
    background:#f4f6f9;
}

.form-card{
    background:#fff;
    border-radius:15px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">

<i class="bi bi-person-plus-fill text-success"></i>

<?= $LANG['new_student']; ?>

	</h2>

<div class="form-card">
<?php if(!empty($error)): ?>

<div class="alert alert-danger">

❌ <?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>
<?php if(count($classes)==0 || count($sections)==0): ?>

<div class="alert alert-warning">

⚠ <?= $LANG['create_classes_first']; ?>

</div>

<?php else: ?>

<form method="post">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['first_name']; ?>

</label>

<input
type="text"
name="first_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['last_name']; ?>

</label>

<input
type="text"
name="last_name"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['father_name']; ?>

</label>

<input
type="text"
name="father_name"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['birth_date']; ?>

</label>

<input
type="date"
name="birth_date"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['amka']; ?>

</label>

<input
type="text"
name="amka"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['phone']; ?>

</label>

<input
type="text"
name="phone"
class="form-control">

</div>

<div class="col-12 mb-3">

<label class="form-label">

<?= $LANG['address']; ?>

</label>

<input
type="text"
name="address"
class="form-control">

</div>

<div class="col-12 mb-3">

<label class="form-label">

<?= $LANG['email']; ?>

</label>
	<input
type="email"
name="email"
class="form-control">

	</div>
	<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['class']; ?>

</label>

<select
name="class_id"
id="class_id"
class="form-select"
required>

<?php foreach($classes as $class): ?>

<option value="<?= $class['id']; ?>">

<?= htmlspecialchars($class['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['section']; ?>

</label>

<select
name="section_id"
id="section_id"
class="form-select"
required>

<?php foreach($sections as $section): ?>

<option
value="<?= $section['id']; ?>"
data-class="<?= $section['class_id']; ?>">

<?= htmlspecialchars($section['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<?= $LANG['bus']; ?>

</label>

<select
name="bus_id"
class="form-select">

<option value="">

<?= $LANG['without_bus']; ?>

</option>

<?php foreach($buses as $bus): ?>

<option value="<?= $bus['id']; ?>">

<?= htmlspecialchars($bus['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-12 mt-4">

<button
type="submit"
class="btn sm-btn btn-add w-100">

<i class="bi bi-floppy-fill"></i>

<?= $LANG['save']; ?>

	</button>

</div>

</div>

</form>

<?php endif; ?>

</div>

<br>

<a
href="students.php"
class="btn sm-btn btn-back w-100">

<i class="bi bi-arrow-left-circle-fill"></i>

<?= $LANG['back']; ?>

	</a>

</div>
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

</body>
</html>
