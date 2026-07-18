<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role']!='teacher'){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$stmt=$pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$stmt->execute([$_SESSION['user_id']]);

$user=$stmt->fetch(PDO::FETCH_ASSOC);

$teacher_id=$user['teacher_id'];
$stmt=$pdo->prepare("
SELECT *
FROM trips
WHERE leader_teacher_id=?
ORDER BY trip_date DESC
");

$stmt->execute([$teacher_id]);

$trips=$stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Οι Εκδρομές μου</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.trip-card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-3">

<h2 class="mb-4">
🚌 Οι Εκδρομές μου
</h2>

<?php if(count($trips)==0): ?>

<div class="alert alert-warning">

Δεν έχετε οριστεί αρχηγός σε καμία εκδρομή.

</div>

<?php endif; ?>

<?php foreach($trips as $trip): ?>

<div class="trip-card">

<h4>

<?= htmlspecialchars($trip['title']) ?>

</h4>

<p>

📅 <?= htmlspecialchars($trip['trip_date']) ?>

</p>

<p>

💰 <?= number_format($trip['cost'],2) ?> €

</p>

<a
href="../admin/trip_view.php?id=<?= $trip['id'] ?>"
class="btn btn-primary">

👁️ Άνοιγμα Εκδρομής

</a>

</div>

<?php endforeach; ?>

<a
href="index.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>

</html>