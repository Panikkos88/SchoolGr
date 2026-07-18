<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../config/database.php';
require_once '../includes/update_check.php';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT
t.*
FROM users u
INNER JOIN teachers t
ON t.id = u.teacher_id
WHERE u.id=?
LIMIT 1
");

$stmt->execute([$user_id]);

$teacher = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Portal Καθηγητή</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.top-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.menu-card{
background:white;
border-radius:15px;
padding:25px;
text-align:center;
box-shadow:0 2px 10px rgba(0,0,0,.08);
transition:.3s;
}

.menu-card:hover{
transform:translateY(-5px);
}

.icon{
font-size:40px;
}

.title{
margin-top:10px;
font-weight:bold;
}

a{
text-decoration:none;
color:black;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="top-card text-center">

<h2>
👨‍🏫 Portal Καθηγητή
</h2>

<p class="mb-1">

<?= htmlspecialchars($teacher['position']); ?>

</p>

<h4>

<?= htmlspecialchars($teacher['first_name']); ?>

<?= htmlspecialchars($teacher['last_name']); ?>

</h4>
<hr>

<p>

📧

<?= htmlspecialchars($teacher['email']); ?>

</p>

<p>

📞

<?= htmlspecialchars($teacher['phone']); ?>

</p>
</div>

<div class="row g-3">

<div class="col-6 col-md-4">

<a href="students.php">

<div class="menu-card">

<div class="icon">👨‍🎓</div>

<div class="title">
Μαθητές
</div>

</div>

</a>

</div>
<div class="col-6 col-md-4">

<a href="trips.php">

<div class="menu-card">

<div class="icon">🚌</div>

<div class="title">
Εκδρομές
</div>

</div>

</a>

</div>
<div class="col-6 col-md-4">

<a href="classes.php">

<div class="menu-card">

<div class="icon">🏫</div>

<div class="title">
Τάξεις
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="absences.php">

<div class="menu-card">

<div class="icon">📝</div>

<div class="title">
Απουσίες
</div>

</div>

</a>

</div>
<div class="col-6 col-md-4">

<a href="messages.php">

<div class="menu-card">

<div class="icon">💬</div>

<div class="title">
Μηνύματα
</div>

</div>

</a>

	</div>
<div class="col-6 col-md-4">

<a href="schedule.php">

<div class="menu-card">

<div class="icon">📚</div>

<div class="title">
Πρόγραμμα
</div>

</div>

</a>

</div>
<div class="col-6 col-md-4">

<a href="assignments.php">

<div class="menu-card">

<div class="icon">📚</div>

<div class="title">
Εργασίες
</div>

</div>

</a>

	</div>
<div class="col-6 col-md-4">

<a href="announcements.php">

<div class="menu-card">

<div class="icon">📢</div>

<div class="title">
Ανακοινώσεις
</div>

</div>

</a>
</div>
	<div class="col-6 col-md-4">

<a href="my_lessons.php">

<div class="menu-card">

<div class="icon">📚</div>

<div class="title">
Μάθημα Ημέρας
</div>

</div>

</a>


</div>
<div class="col-6 col-md-4">

<a href="video/index.php">

<div class="menu-card">

<div class="icon">🎥</div>

<div class="title">
Εξ αποστάσεως μαθήματα
</div>

</div>

</a>

	</div>
<div class="col-6 col-md-4">

<a href="../logout.php">

<div class="menu-card">

<div class="icon">🚪</div>

<div class="title">
Αποσύνδεση
</div>

</div>

</a>

</div>

</div>

</div>

</body>
</html>