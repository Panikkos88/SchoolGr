<?php
session_start();

require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id'])
    ||
    $_SESSION['role']!='teacher'
){
    header("Location: ../../login.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("
SELECT
vc.*,
c.name class_name,
s.name section_name
FROM virtual_classrooms vc
LEFT JOIN classes c ON c.id=vc.class_id
LEFT JOIN sections s ON s.id=vc.section_id
WHERE vc.id=?
LIMIT 1
");

$stmt->execute([$id]);

$room = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$room){
    die("Η αίθουσα δεν βρέθηκε.");
}

if(isset($_POST['start'])){

    $pdo->prepare("
    UPDATE virtual_classrooms
    SET
        is_live=1,
        started_at=NOW()
    WHERE id=?
    ")->execute([$id]);

    header("Location: meeting.php?id=".$id);
    exit;
}

if(isset($_POST['stop'])){

    $pdo->prepare("
UPDATE virtual_classrooms
SET
    is_live=0,
    ended_at=NOW(),
    meet_link=''
    WHERE id=?
    ")->execute([$id]);

    header("Location: meeting.php?id=".$id);
    exit;
}
$stmt = $pdo->prepare("
SELECT
t.first_name,
t.last_name
FROM users u
INNER JOIN teachers t
ON t.id=u.teacher_id
WHERE u.id=?
LIMIT 1
");

$stmt->execute([
    $_SESSION['user_id']
]);

$teacher=$stmt->fetch(PDO::FETCH_ASSOC);

$teacherName=$teacher['first_name'].' '.$teacher['last_name'];

$roomName=$room['room_code'];
$roomName = $room['room_code'];
?>

<!doctype html>

<html lang="el">

<head>

<meta charset="utf-8">

<title>Ψηφιακή Αίθουσα</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://meet.jit.si/external_api.js"></script>
</head>

<body>

<div class="container py-4">

<h2>

🎥

<?= htmlspecialchars($room['class_name']) ?>

-

<?= htmlspecialchars($room['section_name']) ?>

</h2>

<?php if(!$room['is_live']){ ?>

<form method="post">

<button
name="start"
class="btn btn-success btn-lg">

🟢 Έναρξη Μαθήματος

</button>

</form>

<?php }else{ ?>

<form method="post">

<button
name="stop"
class="btn btn-danger btn-lg">

🔴 Λήξη Μαθήματος

</button>

</form>

<br>

<?php

if(isset($_POST['save_meet'])){

    $stmt=$pdo->prepare("
    UPDATE virtual_classrooms
    SET meet_link=?
    WHERE id=?
    ");

    $stmt->execute([
        trim($_POST['meet_link']),
        $id
    ]);

    header("Location: meeting.php?id=".$id);
    exit;

}

?>

<div class="card p-4">

<h4>🎥 Google Meet</h4>

<a
href="https://meet.google.com/new"
target="_blank"
class="btn btn-primary">

🌐 Δημιουργία Google Meet

</a>

<br><br>

<form method="post">

<label>

Επικόλλησε εδώ το Link του Google Meet

</label>

<input
type="text"
name="meet_link"
class="form-control"
value="<?= htmlspecialchars($room['meet_link']) ?>">

<br>

<button
name="save_meet"
class="btn btn-success">

💾 Αποθήκευση

</button>

</form>

<?php if(!empty($room['meet_link'])){ ?>

<hr>

<a
href="<?= htmlspecialchars($room['meet_link']) ?>"
target="_blank"
class="btn btn-danger btn-lg">

🎥 Είσοδος στο Google Meet

</a>

<?php } ?>

<br><br>

<a
href="index.php"
class="btn btn-secondary">

⬅️ Επιστροφή στις Ψηφιακές Αίθουσες

</a>

</div>

<?php } ?>

</div>

</body>

</html>