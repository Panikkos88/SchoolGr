<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']!='student'
){
    header("Location: ../../login.php");
    exit;
}

$user_id=$_SESSION['user_id'];

$stmt=$pdo->prepare("
SELECT
s.*
FROM users u
INNER JOIN students s
ON s.id=u.student_id
WHERE u.id=?
LIMIT 1
");

$stmt->execute([
    $user_id
]);

$student=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){

    die("Δεν βρέθηκε μαθητής.");

}

$class_id=$student['class_id'];

$section_id=$student['section_id'];

$stmt=$pdo->prepare("
SELECT
vc.*,
t.first_name,
t.last_name,
c.name class_name,
s.name section_name
FROM virtual_classrooms vc

INNER JOIN teachers t
ON t.id=vc.teacher_id

INNER JOIN classes c
ON c.id=vc.class_id

INNER JOIN sections s
ON s.id=vc.section_id

WHERE
vc.class_id=?
AND
vc.section_id=?

LIMIT 1
");

$stmt->execute([
$class_id,
$section_id
]);

$room=$stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>

<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Οι Βιντεοκλήσεις μου</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
border-radius:18px;
box-shadow:0 2px 12px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2>

🎥 Τα εξ αποστάσεως μαθήματα μου

</h2>

<?php if(!$room){ ?>

<div class="alert alert-warning">

Δεν υπάρχει διαθέσιμη ψηφιακή αίθουσα.

</div>

<a
href="../index.php"
class="btn btn-secondary">

Επιστροφή

</a>

<?php }else{ ?>

<div class="card p-4">

<h4>

🏫

<?= htmlspecialchars($room['class_name']) ?>

-

<?= htmlspecialchars($room['section_name']) ?>

</h4>

<hr>

<p>

👨‍🏫 Καθηγητής:

<strong>

<?= htmlspecialchars($room['first_name']) ?>

<?= htmlspecialchars($room['last_name']) ?>

</strong>

</p>

<p>

🎥 Αίθουσα:

<strong>

<?= htmlspecialchars($room['room_name']) ?>

</strong>

	</p>
	<?php if($room['is_live']==1 && !empty($room['meet_link'])){ ?>

<div class="alert alert-success">

🟢 Ο καθηγητής ξεκίνησε το μάθημα.

</div>
<a
href="join.php?id=<?= $room['id'] ?>"
class="btn btn-success btn-lg w-100">

🎥 Συμμετοχή στο Μάθημα

	</a>
<?php }else{ ?>

<div class="alert alert-warning">

<?php if(!$room['is_live']){ ?>

<div class="alert alert-warning">

🔴 Ο καθηγητής δεν έχει ξεκινήσει ακόμη το μάθημα.

</div>

<?php }else{ ?>

<div class="alert alert-warning">

⏳ Ο καθηγητής δεν έχει δημοσιεύσει ακόμη σύνδεσμο.

</div>

<?php } ?>

</div>

<?php } ?>

<br><br>

<a
href="../index.php"
class="btn btn-secondary">

⬅ Επιστροφή

</a>

</div>

<?php } ?>

</div>

</body>

	</html>