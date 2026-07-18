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
s.*,
c.name AS class_name,
sec.name AS section_name

FROM parent_students ps

INNER JOIN students s
ON s.id = ps.student_id

LEFT JOIN classes c
ON c.id = s.class_id

LEFT JOIN sections sec
ON sec.id = s.section_id

WHERE ps.parent_id=?

ORDER BY s.last_name
");

$children->execute([
    $parent_id
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Τα Παιδιά Μου</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.child-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.avatar{
font-size:55px;
text-align:center;
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
👨‍👩‍👧 Τα Παιδιά Μου
</h2>

<?php foreach($children as $child): ?>

<?php

$abs = $pdo->prepare("
SELECT COUNT(*) total
FROM absences
WHERE student_id=?
");

$abs->execute([
    $child['id']
]);

$absence_count =
$abs->fetch(PDO::FETCH_ASSOC)['total'];

?>

<div class="child-card">

<div class="avatar">
👦
</div>

<h4 class="text-center">

<?= htmlspecialchars(
$child['first_name'].' '.$child['last_name']
); ?>

</h4>

<hr>

<p>
🏫 Τάξη:
<?= htmlspecialchars($child['class_name']); ?>
</p>

<p>
📚 Τμήμα:
<?= htmlspecialchars($child['section_name']); ?>
</p>

<p>
📝 Απουσίες:
<?= $absence_count; ?>
</p>

<p>
🚌 Λεωφορείο:
<?= $child['bus_id']; ?>
</p>

<p>
💰 Δίδακτρα:
<?= number_format(
$child['tuition_fee'],
2
); ?> €
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