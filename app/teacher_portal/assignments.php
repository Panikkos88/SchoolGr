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

$assignments = $pdo->prepare("
SELECT *
FROM assignments
WHERE teacher_id=?
ORDER BY id DESC
");

$assignments->execute([
    $teacher_id
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Οι Εργασίες μου</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
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

<h2>📚 Οι Εργασίες μου</h2>

<a
href="add_assignment.php"
class="btn btn-success w-100 mb-3">

➕ Νέα Εργασία

</a>

<?php foreach($assignments as $assignment): ?>

<div class="card">

<h4>

<?= htmlspecialchars($assignment['title']); ?>

</h4>

<p>

📅 Παράδοση:

<?= date(
'd/m/Y',
strtotime($assignment['due_date'])
); ?>

</p>

<a
href="view_submissions.php?id=<?= $assignment['id']; ?>"
class="btn btn-primary btn-sm">

👁 Παραδόσεις

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