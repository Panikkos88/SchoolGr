<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$stmt = $pdo->query("
SELECT *
FROM tuition_categories
ORDER BY title
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Κατηγορίες Διδάκτρων</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">

<h2>💰 Κατηγορίες Διδάκτρων</h2>

<a
href="tuition_category_add.php"
class="btn btn-success mb-3">

➕ Νέα Κατηγορία

</a>

<?php foreach($rows as $row): ?>

<div class="card mb-2">

<div class="card-body">

<strong>
<?= htmlspecialchars($row['title']); ?>
</strong>

<br>

Ποσό:
<?= number_format($row['amount'],2); ?> €

<br>

Τύπος:

<?php
if($row['billing_cycle']=='monthly'){
    echo 'Μηνιαία';
}
elseif($row['billing_cycle']=='semester'){
    echo 'Εξαμηνιαία';
}
else{
    echo 'Ετήσια';
}
?>

</div>

</div>

<?php endforeach; ?>

<a
href="settings.php"
class="btn btn-secondary w-100">

⬅️ Επιστροφή

</a>

</div>

</body>
</html>