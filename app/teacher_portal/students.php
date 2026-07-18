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

<title>Μαθητές</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.student-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.avatar{
font-size:50px;
text-align:center;
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
👨‍🎓 Οι Μαθητές Μου
</h2>

<div class="student-card">

<div class="avatar">
👧
</div>

<h4 class="text-center">
Αναστασία Λεβάντη
</h4>

<hr>

<p>
🏫 Τάξη: Α2
</p>

<p>
📚 Τμήμα: Α1
</p>

<p>
📝 Απουσίες: 2
</p>

<a href="#"
class="btn btn-primary w-100">

👁️ Προβολή

</a>

</div>

<div class="student-card">

<div class="avatar">
👦
</div>

<h4 class="text-center">
Ιωάννης Παπαδόπουλος
</h4>

<hr>

<p>
🏫 Τάξη: Β1
</p>

<p>
📚 Τμήμα: Β1
</p>

<p>
📝 Απουσίες: 1
</p>

<a href="#"
class="btn btn-primary w-100">

👁️ Προβολή

</a>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>