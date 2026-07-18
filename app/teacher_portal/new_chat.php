<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$user_stmt = $pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$teacher_id = $user['teacher_id'];

$students = $pdo->prepare("
SELECT DISTINCT

u.id AS user_id,
s.first_name,
s.last_name,
c.name AS class_name,
sec.name AS section_name

FROM teacher_sections ts

INNER JOIN students s
ON s.class_id=ts.class_id
AND s.section_id=ts.section_id

INNER JOIN users u
ON u.student_id=s.id

LEFT JOIN classes c
ON c.id=s.class_id

LEFT JOIN sections sec
ON sec.id=s.section_id

WHERE ts.teacher_id=?

ORDER BY
s.first_name,
s.last_name
");

$students->execute([
    $teacher_id
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Νέα Συνομιλία</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.user-card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:10px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2>➕ Νέα Συνομιλία</h2>

<?php while($student = $students->fetch(PDO::FETCH_ASSOC)): ?>

<a
href="chat.php?user_id=<?= $student['user_id']; ?>"
style="text-decoration:none;color:black;">

<div class="user-card">

👨‍🎓

<?= htmlspecialchars(
$student['first_name'].' '.$student['last_name']
); ?>

<br>

<small>

<?= htmlspecialchars($student['class_name']); ?>

-

<?= htmlspecialchars($student['section_name']); ?>

</small>

</div>

</a>

<?php endwhile; ?>

<a
href="messages.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>