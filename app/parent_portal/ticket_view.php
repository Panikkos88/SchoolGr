<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$ticket_stmt = $pdo->prepare("
SELECT *
FROM tickets
WHERE id=?
AND user_id=?
LIMIT 1
");

$ticket_stmt->execute([
    $id,
    $_SESSION['user_id']
]);

$ticket = $ticket_stmt->fetch(PDO::FETCH_ASSOC);

if(!$ticket){
    exit('Δεν βρέθηκε το αίτημα');
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
}

$replies = $pdo->prepare("
SELECT *
FROM ticket_replies
WHERE ticket_id=?
ORDER BY id ASC
");

$replies->execute([$id]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Ticket</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.msg{
background:white;
padding:15px;
border-radius:12px;
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

<div class="msg">

<?= nl2br(
htmlspecialchars($ticket['message'])
); ?>

</div>

<?php foreach($replies as $reply): ?>

<div class="msg">

<?= nl2br(
htmlspecialchars($reply['message'])
); ?>

</div>

<?php endforeach; ?>

<hr>

<form method="post">

<textarea
name="message"
class="form-control"
rows="4"
required></textarea>

<br>

<button
class="btn btn-primary">

📨 Απάντηση

</button>

</form>

<br>

<a
href="tickets.php"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>