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

<title>Οικονομικά</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.stat-card{
background:white;
border-radius:15px;
padding:20px;
text-align:center;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.fee-card{
background:white;
border-radius:15px;
padding:20px;
margin-top:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
💰 Οικονομικά
</h2>

<div class="row g-3">

<div class="col-4">

<div class="stat-card">

<h4>120€</h4>

<div>Χρεώσεις</div>

</div>

</div>

<div class="col-4">

<div class="stat-card">

<h4>80€</h4>

<div>Πληρωμένα</div>

</div>

</div>

<div class="col-4">

<div class="stat-card">

<h4>40€</h4>

<div>Υπόλοιπο</div>

</div>

</div>

</div>

<div class="fee-card">

<h5>
👧 Αναστασία Λεβάντη
</h5>

<hr>

<p>
📄 Δίδακτρα Ιουνίου
</p>

<p>
💰 20€
</p>

<span class="badge bg-danger">
Απλήρωτο
</span>

</div>

<div class="fee-card">

<h5>
👦 Ιωάννης Λεβάντης
</h5>

<hr>

<p>
📄 Εκδρομή Πλανητάριο
</p>

<p>
💰 20€
</p>

<span class="badge bg-success">
Πληρωμένο
</span>

</div>

<br>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>