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

<title>Τάξεις</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.class-card{
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

<h2 class="mb-4">
🏫 Οι Τάξεις Μου
</h2>

<div class="class-card">

<h4>
🏫 Α1
</h4>

<p>
👨‍🎓 22 μαθητές
</p>

<a href="#"
class="btn btn-primary">

👁️ Προβολή Μαθητών

</a>

</div>

<div class="class-card">

<h4>
🏫 Α2
</h4>

<p>
👨‍🎓 18 μαθητές
</p>

<a href="#"
class="btn btn-primary">

👁️ Προβολή Μαθητών

</a>

</div>

<div class="class-card">

<h4>
🏫 Β1
</h4>

<p>
👨‍🎓 25 μαθητές
</p>

<a href="#"
class="btn btn-primary">

👁️ Προβολή Μαθητών

</a>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>