<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$user_id = $_SESSION['user_id'];

$conversations = $pdo->prepare("
SELECT DISTINCT
u.id,
u.first_name,
u.last_name

FROM users u

INNER JOIN messages m
ON (
    u.id = m.sender_id
    OR
    u.id = m.receiver_id
)

WHERE
(
    m.sender_id=?
    OR
    m.receiver_id=?
)
AND u.id<>?

ORDER BY u.first_name,u.last_name
");

$conversations->execute([
    $user_id,
    $user_id,
    $user_id
]);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Μηνύματα</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.chat-card{
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

<h2>💬 Συνομιλίες</h2>
<a
href="new_chat.php"
class="btn btn-success mb-3">

➕ Νέα Συνομιλία

	</a>
<?php while($row = $conversations->fetch(PDO::FETCH_ASSOC)): ?>

<a
href="chat.php?user_id=<?= $row['id']; ?>"
style="text-decoration:none;">

<div class="chat-card">

👤

<?= htmlspecialchars(
$row['first_name'].' '.$row['last_name']
); ?>

</div>

</a>

<?php endwhile; ?>

<a
href="index.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>