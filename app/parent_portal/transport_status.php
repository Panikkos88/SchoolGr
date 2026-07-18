<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

/*
|-----------------------------------
| Συνδεδεμένος γονέας
|-----------------------------------
*/

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

$parent_id = $user['parent_id'];

/*
|-----------------------------------
| Παιδιά γονέα
|-----------------------------------
*/

$children = $pdo->prepare("
SELECT
s.id,
s.first_name,
s.last_name

FROM parent_students ps

INNER JOIN students s
ON s.id = ps.student_id

WHERE ps.parent_id=?
");

$children->execute([$parent_id]);

?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Κατάσταση Μεταφοράς</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
🚌 Κατάσταση Μεταφοράς
</h2>

<?php foreach($children as $child): ?>

<?php

$status = $pdo->prepare("
SELECT *
FROM student_bus_status
WHERE student_id=?
LIMIT 1
");

$status->execute([
    $child['id']
]);

$row = $status->fetch(PDO::FETCH_ASSOC);

?>

<div class="card-box">

<h4>

<?= htmlspecialchars(
$child['first_name'].' '.$child['last_name']
); ?>

</h4>

<hr>

<p>

<?php if(!empty($row['picked_up'])): ?>

✅ Παραλήφθηκε

<br>

<?= $row['pickup_time']; ?>

<?php else: ?>

⌛ Αναμονή Παραλαβής

<?php endif; ?>

</p>

<p>

<?php if(!empty($row['arrived_school'])): ?>

🏫 Έφτασε στο Σχολείο

<br>

<?= $row['arrived_school_time']; ?>

<?php endif; ?>

</p>

<p>

<?php if(!empty($row['delivered_home'])): ?>

🏠 Παραδόθηκε στον Γονέα

<br>

<?= $row['delivered_home_time']; ?>

<?php endif; ?>

</p>

</div>

<?php endforeach; ?>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>