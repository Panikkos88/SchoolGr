<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$ticket_stmt = $pdo->prepare("
SELECT
t.*,
u.email
FROM tickets t
LEFT JOIN users u
ON u.id=t.user_id
WHERE t.id=?
LIMIT 1
");

$ticket_stmt->execute([$id]);

$ticket = $ticket_stmt->fetch(PDO::FETCH_ASSOC);

if(!$ticket){
exit($LANG['ticket_not_found']);
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO ticket_replies
    (
        ticket_id,
        user_id,
        message
    )
    VALUES
    (
        ?,?,?
    )
    ");

    $stmt->execute([
        $id,
        $_SESSION['user_id'],
        $_POST['message']
    ]);

    $stmt = $pdo->prepare("
    UPDATE tickets
    SET status='answered'
    WHERE id=?
    ");
$notif = $pdo->prepare("
INSERT INTO notifications
(
    user_id,
    title,
    message
)
VALUES
(
    ?,?,?
)
");

$notif->execute([
    $ticket['user_id'],
$LANG['ticket_reply'],
$LANG['ticket_reply_message'].' '.$ticket['subject']
]);
    $stmt->execute([$id]);

    header("Location: ticket_view.php?id=".$id);
    exit;
}

$replies = $pdo->prepare("
SELECT
r.*,
u.email
FROM ticket_replies r
LEFT JOIN users u
ON u.id=r.user_id
WHERE r.ticket_id=?
ORDER BY r.id ASC
");

$replies->execute([$id]);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['ticket']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card{
background:white;
padding:15px;
border-radius:15px;
margin-bottom:10px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h3>
🎫 <?= htmlspecialchars($ticket['subject']); ?>
</h3>

<div class="card">

<b><?= $LANG['user']; ?>:</b>

<?= htmlspecialchars($ticket['email']); ?>

<hr>

<?= nl2br(htmlspecialchars($ticket['message'])); ?>

</div>

<?php foreach($replies as $reply): ?>

<div class="card">

<b>

<?= htmlspecialchars($reply['email']); ?>

</b>

<hr>

<?= nl2br(htmlspecialchars($reply['message'])); ?>

</div>

<?php endforeach; ?>

<form method="post">

<textarea
name="message"
class="form-control"
rows="5"
required></textarea>

<br>

<button
class="btn btn-success">

📨 <?= $LANG['reply']; ?>

</button>

</form>

<br>

<a
href="tickets.php"
class="btn btn-secondary">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>
