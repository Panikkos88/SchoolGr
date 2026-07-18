<?php

require_once '../config/database.php';

$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("
UPDATE trip_signatures
SET status='rejected'
WHERE token=?
");

$stmt->execute([$token]);

?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Απόρριψη Εκδρομής</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body style="background:#f4f6f9;">

<div class="container py-5">

<div class="alert alert-danger text-center">

<h2>❌ Η εκδρομή απορρίφθηκε</h2>

<p>
Η απάντησή σας καταχωρήθηκε επιτυχώς.
</p>

</div>

</div>

</body>
</html>