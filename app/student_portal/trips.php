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
<meta name="viewport" content="width=device-width, initial-scale=1">

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
🚌 Οι Εκδρομές Μου
</h2>

<div class="trip-card">

<h4>Επίσκεψη στο Μουσείο</h4>

<p>
📅 25/06/2026
</p>

<p>
💰 5 €
</p>

<span class="badge bg-success">
✅ Εγκρίθηκε
</span>

<br><br>

<a href="#"
class="btn btn-primary btn-sm">

👁️ Προβολή Συγκατάθεσης

</a>

</div>

<div class="trip-card">

<h4>Εκδρομή στο Πλανητάριο</h4>

<p>
📅 10/07/2026
</p>

<p>
💰 8 €
</p>

<span class="badge bg-warning text-dark">
⏳ Εκκρεμεί
</span>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>
<?php require_once '../includes/mobile_footer.php'; ?>
</body>
</html>