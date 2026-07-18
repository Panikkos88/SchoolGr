<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$student_id = (int)($_GET['student_id'] ?? 0);

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO absences
    (
        student_id,
        absence_date,
        hours,
        reason
    )
    VALUES
    (
        ?,?,?,?
    )
    ");

    $stmt->execute([

        $student_id,
        $_POST['absence_date'],
        $_POST['hours'],
        $_POST['reason']

    ]);

    header("Location: student_view.php?id=".$student_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['new_absence']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.form-card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2>
📝 <?= $LANG['new_absence']; ?>
</h2>

<div class="form-card">

<form method="post">

<div class="mb-3">

<label><?= $LANG['date']; ?></label>

<input
type="date"
name="absence_date"
class="form-control"
required>

</div>

<div class="mb-3">

<label><?= $LANG['absence_hours']; ?></label>

<input
type="number"
name="hours"
class="form-control"
required>

</div>

<div class="mb-3">

<label><?= $LANG['reason']; ?></label>

<textarea
name="reason"
class="form-control"></textarea>

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>

</form>
<br>

<a
href="student_view.php?id=<?= $student_id; ?>"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</div>

</body>
</html>