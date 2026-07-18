<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

if($_SESSION['role'] != 'student'){
    exit('Δεν έχετε πρόσβαση');
}

require_once '../config/database.php';

$user_stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    exit('Ο χρήστης δεν βρέθηκε');
}

$student_stmt = $pdo->prepare("
SELECT
s.*,
c.name AS class_name,
sec.name AS section_name
FROM students s
LEFT JOIN classes c ON c.id=s.class_id
LEFT JOIN sections sec ON sec.id=s.section_id
WHERE s.id=?
LIMIT 1
");

$student_stmt->execute([
    $user['student_id']
]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    exit('Δεν βρέθηκε ο μαθητής');
}

$abs_stmt = $pdo->prepare("
SELECT COUNT(*) total
FROM absences
WHERE student_id=?
");

$abs_stmt->execute([
    $student['id']
]);

$absence_count = $abs_stmt->fetch(PDO::FETCH_ASSOC)['total'];

$notif_stmt = $pdo->prepare("
SELECT COUNT(*) total
FROM notifications
WHERE user_id=?
AND is_read=0
");

$notif_stmt->execute([
    $_SESSION['user_id']
]);

$unread_notifications = $notif_stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Πίνακας Μαθητή</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
background:#f4f6f9;
}

.card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.menu-card{
background:white;
border:none;
border-radius:15px;
padding:20px;
text-align:center;
box-shadow:0 2px 10px rgba(0,0,0,.08);
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

<h2>👨‍🎓 Πίνακας Μαθητή</h2>

<div class="card">

<h4>
<?= htmlspecialchars($student['first_name'].' '.$student['last_name']); ?>
</h4>

<hr>

<p>🏫 Τάξη: <?= htmlspecialchars($student['class_name']); ?></p>

<p>📚 Τμήμα: <?= htmlspecialchars($student['section_name']); ?></p>

<p>📝 Απουσίες: <?= $absence_count; ?></p>

<p>🚌 Λεωφορείο: <?= $student['bus_id']; ?></p>

</div>

<div class="row g-3">

<div class="col-6">
<a href="bus_live.php">
<div class="menu-card">
<div class="icon">🚌</div>
<div class="title">Το Λεωφορείο μου</div>
</div>
</a>
</div>

<div class="col-6">
<a href="announcements.php">
<div class="menu-card">
<div class="icon">📢</div>
<div class="title">Ανακοινώσεις</div>
</div>
</a>
</div>

<div class="col-6">
<a href="chat_list.php">
<div class="menu-card">
<div class="icon">💬</div>
<div class="title">Chat</div>
</div>
</a>
</div>

<div class="col-6">
<a href="notifications.php">
<div class="menu-card">
<div class="icon">🔔</div>
<div class="title">
Ειδοποιήσεις

<?php if($unread_notifications > 0): ?>
<span class="badge bg-danger">
<?= $unread_notifications ?>
</span>
<?php endif; ?>

</div>
</div>
</a>
</div>

<div class="col-6">
<a href="lessons.php">
<div class="menu-card">
<div class="icon">📚</div>
<div class="title">Τα Μαθήματά μου</div>
</div>
</a>
</div>

<div class="col-6">
<a href="trips.php">
<div class="menu-card">
<div class="icon">🚌</div>
<div class="title">Εκδρομές</div>
</div>
</a>
</div>
<div class="col-6">

<a href="video/index.php">

<div class="menu-card">

<div class="icon">🎥</div>

<div class="title">

Εξ αποστάσεως μαθήματα

</div>

</div>

</a>

	</div>
<div class="col-6">
<a href="../logout.php">
<div class="menu-card">
<div class="icon">🚪</div>
<div class="title">Έξοδος</div>
</div>
</a>
</div>

</div>

</div>

</body>
</html>