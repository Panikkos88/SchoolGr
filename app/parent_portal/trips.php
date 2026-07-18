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

<title>Εκδρομές</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.trip-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
🚌 Εκδρομές & Συγκαταθέσεις
</h2>

<div class="trip-card">

<h4>
👧 Αναστασία Λεβάντη
</h4>

<hr>

<p>
🚌 Επίσκεψη στο Μουσείο
</p>

<p>
📅 25/06/2026
</p>

<p>
💰 Κόστος: 5€
</p>

<span class="badge bg-success">
✅ Εγκρίθηκε
</span>

<br><br>

<a href="#"
class="btn btn-primary">

👁️ Προβολή Συγκατάθεσης

</a>

</div>

<div class="trip-card">

<h4>
👦 Ιωάννης Λεβάντης
</h4>

<hr>

<p>
🚌 Εκδρομή στο Πλανητάριο
</p>

<p>
📅 10/07/2026
</p>

<p>
💰 Κόστος: 8€
</p>

<span class="badge bg-warning text-dark">
⏳ Αναμονή
</span>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>

</body>
</html>