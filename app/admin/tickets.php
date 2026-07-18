<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$tickets = $pdo->query("
SELECT
t.*,
u.email
FROM tickets t
LEFT JOIN users u
ON u.id=t.user_id
ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['tickets']; ?></title>

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

<h2>🎫 <?= $LANG['support_tickets']; ?></h2>

<?php foreach($tickets as $ticket): ?>

<div class="card">

<h5>
<?= htmlspecialchars($ticket['subject']); ?>
</h5>

<p>
👤 <?= htmlspecialchars($ticket['email']); ?>
</p>

<p>
<?= $LANG['status']; ?>:
<b>
<?=
$LANG[$ticket['status']] ??
htmlspecialchars($ticket['status']);
?>
</b>
</p>

<a
href="ticket_view.php?id=<?= $ticket['id']; ?>"
class="btn btn-primary">

<?= $LANG['open']; ?>

</a>

</div>

<?php endforeach; ?>
<br>

<a
href="dashboard.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</body>
</html>