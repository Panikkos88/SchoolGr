<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$student_id = (int)($_GET['student_id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM students
WHERE id=?
");

$stmt->execute([$student_id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
die($LANG['student_not_found']);
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO student_finance
    (
        student_id,
        charge_date,
        description,
        amount,
        type
    )
    VALUES
    (
        ?,?,?,?,?
    )
    ");

    $stmt->execute([
        $student_id,
        $_POST['charge_date'],
        $_POST['description'],
        $_POST['amount'],
        $_POST['type']
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
<title><?= $LANG['finance_transaction']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.form-card{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-3">

<h2>

💰 <?= $LANG['finance']; ?>

<br>

<?= htmlspecialchars($student['first_name']); ?>
<?= htmlspecialchars($student['last_name']); ?>

</h2>

<div class="form-card">

<form method="post">

<div class="mb-3">

<label><?= $LANG['date']; ?></label>

<input
type="date"
name="charge_date"
class="form-control"
value="<?= date('Y-m-d'); ?>"
required>

</div>

<div class="mb-3">

<label><?= $LANG['description']; ?></label>

<input
type="text"
name="description"
class="form-control"
placeholder="<?= $LANG['description']; ?>"
required>

</div>

<div class="mb-3">

<label><?= $LANG['amount']; ?></label>

<input
type="number"
step="0.01"
name="amount"
class="form-control"
required>

</div>

<div class="mb-3">

<label><?= $LANG['type']; ?></label>

<select
name="type"
class="form-control">

<option value="charge">
<?= $LANG['charge']; ?>
</option>

<option value="payment">
<?= $LANG['payment']; ?>
</option>

</select>

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>

</form>

</div>

<br>

<a
href="student_view.php?id=<?= $student_id; ?>"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>