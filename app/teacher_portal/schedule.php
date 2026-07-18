<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Πρόγραμμα Καθηγητή</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.day-card{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.lesson{
padding:10px;
border-bottom:1px solid #eee;
}

.lesson:last-child{
border-bottom:none;
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
📚 Το Πρόγραμμά Μου
</h2>

<div class="day-card">

<h4>
Δευτέρα
</h4>

<div class="lesson">
08:00 - Α1 - Μαθηματικά
</div>

<div class="lesson">
09:00 - Α2 - Μαθηματικά
</div>

<div class="lesson">
10:00 - Β1 - Μαθηματικά
</div>

</div>

<div class="day-card">

<h4>
Τρίτη
</h4>

<div class="lesson">
08:00 - Β2 - Μαθηματικά
</div>

<div class="lesson">
09:00 - Γ1 - Μαθηματικά
</div>

</div>

<div class="day-card">

<h4>
Τετάρτη
</h4>

<div class="lesson">
08:00 - Α1 - Μαθηματικά
</div>

<div class="lesson">
09:00 - Α2 - Μαθηματικά
</div>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>