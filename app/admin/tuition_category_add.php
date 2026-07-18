<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO tuition_categories
    (
        title,
        amount,
        billing_cycle
    )
    VALUES
    (
        ?,?,?
    )
    ");

    $stmt->execute([
        $_POST['title'],
        $_POST['amount'],
        $_POST['billing_cycle']
    ]);

    header("Location: tuition_categories.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Νέα Κατηγορία Διδάκτρων</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-3">

<h2>💰 Νέα Κατηγορία</h2>

<form method="post">

<div class="mb-3">

<label>Όνομα Κατηγορίας</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Ποσό</label>

<input
type="number"
step="0.01"
name="amount"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Τρόπος Χρέωσης</label>

<select
name="billing_cycle"
class="form-control">

<option value="monthly">
Μηνιαία
</option>

<option value="semester">
Εξαμηνιαία
</option>

<option value="yearly">
Ετήσια
</option>

</select>

</div>

<button
class="btn btn-success w-100">

💾 Αποθήκευση

</button>

</form>

</div>

</body>
</html>