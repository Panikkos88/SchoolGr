<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']!='teacher'
){
    header("Location: ../../login.php");
    exit;
}

$user_id=$_SESSION['user_id'];

$stmt=$pdo->prepare("
SELECT
t.id
FROM users u
INNER JOIN teachers t
ON t.id=u.teacher_id
WHERE u.id=?
LIMIT 1
");

$stmt->execute([$user_id]);

$teacher=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$teacher){
die("Δεν βρέθηκε καθηγητής.");
}

$teacher_id=$teacher['id'];
$message='';

if($_SERVER['REQUEST_METHOD']=='POST'){

    list($class_id,$section_id)=explode(
    '|',
    $_POST['class_section']
);

$class_id=(int)$class_id;
$section_id=(int)$section_id;

    $check=$pdo->prepare("
    SELECT id
    FROM virtual_classrooms
    WHERE class_id=?
    AND section_id=?
    LIMIT 1
    ");

    $check->execute([
        $class_id,
        $section_id
    ]);

    if($check->fetch()){

        $message="❌ Υπάρχει ήδη αίθουσα για αυτό το τμήμα.";

    }else{

        $room_code='SM-'
        .$class_id.'-'
        .$section_id;

        $room_name='Ψηφιακή Τάξη';

        $stmt=$pdo->prepare("
        INSERT INTO virtual_classrooms
        (
            class_id,
            section_id,
            teacher_id,
            room_name,
            room_code,
            is_live
        )
        VALUES
        (
            ?,?,?,?,?,
            0
        )
        ");

        $stmt->execute([
            $class_id,
            $section_id,
            $teacher_id,
            $room_name,
            $room_code
        ]);

        $message="✅ Η αίθουσα δημιουργήθηκε.";

    }

}
$stmt = $pdo->prepare("
SELECT
    ts.class_id,
    ts.section_id,
    c.name AS class_name,
    s.name AS section_name
FROM teacher_sections ts
INNER JOIN classes c
ON c.id = ts.class_id
INNER JOIN sections s
ON s.id = ts.section_id
WHERE ts.teacher_id = ?
ORDER BY c.name,s.name
");

$stmt->execute([
    $teacher_id
]);

$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Νέα Ψηφιακή Αίθουσα</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<div class="container py-4">

<h2>🎥 Δημιουργία Ψηφιακής Αίθουσας</h2>

<?php if($message){ ?>

<div class="alert alert-info">

<?= $message ?>

</div>

<?php } ?>
	<form method="post">

<div class="card shadow">

<div class="card-body">

<div class="mb-3">

<label class="form-label">

🏫 Επιλογή Τάξης / Τμήματος

</label>

<select
name="class_section"
class="form-select"
required>

<option value="">
-- Επιλέξτε --
</option>

<?php foreach($classrooms as $room){ ?>

<option
value="<?= $room['class_id'] ?>|<?= $room['section_id'] ?>">

<?= htmlspecialchars($room['class_name']) ?>

-

<?= htmlspecialchars($room['section_name']) ?>

</option>

<?php } ?>

</select>

</div>

<button
type="submit"
class="btn btn-success">

🎥 Δημιουργία Ψηφιακής Αίθουσας

</button>

<a
href="index.php"
class="btn btn-secondary">

Επιστροφή

</a>

</div>

</div>

	</form>
	</div>

</body>

</html>