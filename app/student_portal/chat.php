<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$my_id = $_SESSION['user_id'];
$other_id = (int)($_GET['user_id'] ?? 0);

$user_stmt = $pdo->prepare("
SELECT *
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([$other_id]);

$other_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if(!$other_user){
    die('Ο χρήστης δεν βρέθηκε');
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO messages
    (
        sender_id,
        receiver_id,
        message
    )
    VALUES
    (
        ?,?,?
    )
    ");

    $stmt->execute([
        $my_id,
        $other_id,
        $_POST['message']
    ]);

    header("Location: chat.php?user_id=".$other_id);
    exit;
}

$messages = $pdo->prepare("
SELECT *
FROM messages
WHERE
(
    sender_id=?
    AND receiver_id=?
)
OR
(
    sender_id=?
    AND receiver_id=?
)
ORDER BY created_at ASC
");

$messages->execute([
    $my_id,
    $other_id,
    $other_id,
    $my_id
]);
?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Chat</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.chat-box{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-bottom:20px;
}

.me{
background:#d1ffd6;
padding:10px;
border-radius:10px;
margin-bottom:10px;
}

.other{
background:#f1f1f1;
padding:10px;
border-radius:10px;
margin-bottom:10px;
}

</style>

</head>

<body>

<div class="container py-4">

<h3>

💬

<?= htmlspecialchars(
$other_user['first_name'].' '.$other_user['last_name']
); ?>

</h3>

<div class="chat-box">

<?php while($msg = $messages->fetch(PDO::FETCH_ASSOC)): ?>

<div class="<?= ($msg['sender_id']==$my_id) ? 'me' : 'other'; ?>">

<?= nl2br(htmlspecialchars($msg['message'])); ?>

<br>

<small>

<?= $msg['created_at']; ?>

</small>

</div>

<?php endwhile; ?>

</div>

<form method="post">

<textarea
name="message"
class="form-control"
rows="3"
required></textarea>

<br>

<button
type="submit"
class="btn btn-primary w-100">

📨 Αποστολή

</button>

</form>

<br>

<a
href="messages.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>