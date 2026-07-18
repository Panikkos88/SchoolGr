<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO tickets
    (
        user_id,
        subject,
        message
    )
    VALUES
    (
        ?,?,?
    )
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['subject'],
        $_POST['message']
    ]);

    $success = true;
}

$tickets = $pdo->prepare("
SELECT *
FROM tickets
WHERE user_id=?
ORDER BY id DESC
");

$tickets->execute([
    $_SESSION['user_id']
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Tickets</title>

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

<h2>🎫 Αιτήματα</h2>

<?php if(!empty($success)): ?>

<div class="alert alert-success">

Το αίτημα καταχωρήθηκε.

</div>

<?php endif; ?>

<div class="card">

<form method="post">

<label>Θέμα</label>

<input
type="text"
name="subject"
class="form-control"
required>

<br>

<label>Μήνυμα</label>

<textarea
name="message"
class="form-control"
rows="5"
required></textarea>

<br>

<button
class="btn btn-primary">

➕ Υποβολή

</button>

</form>

</div>

<?php foreach($tickets as $ticket): ?>

<div class="card">

<h5>

<?= htmlspecialchars($ticket['subject']); ?>

</h5>

<p>

<?= nl2br(
htmlspecialchars($ticket['message'])
); ?>

</p>

<p>

Κατάσταση:

<b>

<?= $ticket['status']; ?>

</b>

</p>

<a
href="ticket_view.php?id=<?= $ticket['id']; ?>"
class="btn btn-success btn-sm">

💬 Προβολή

</a>

</div>

<?php endforeach; ?>

</div>

</body>
</html>