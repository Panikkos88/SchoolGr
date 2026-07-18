<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$charges = $pdo->query("
SELECT COALESCE(SUM(amount),0)
FROM student_finance
WHERE type='charge'
")->fetchColumn();

$payments = $pdo->query("
SELECT COALESCE(SUM(amount),0)
FROM student_finance
WHERE type='payment'
")->fetchColumn();

$balance = $charges - $payments;
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $LANG['finance']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">
<h2>💰 <?= $LANG['school_finance']; ?></h2>
<div class="alert alert-danger">
<?= $LANG['total_charges']; ?>:
<strong><?= number_format($charges,2); ?> €</strong>
</div>

<div class="alert alert-success">
<?= $LANG['total_payments']; ?>:
<strong><?= number_format($payments,2); ?> €</strong>
</div>

<div class="alert alert-info">
<?= $LANG['balance']; ?>:
<strong><?= number_format($balance,2); ?> €</strong>
</div>

<a
href="dashboard.php"
class="btn btn-secondary w-100">
⬅️ <?= $LANG['back']; ?> </a>

</div>

</body>
</html>