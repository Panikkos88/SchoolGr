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

<title>Ρυθμίσεις</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.setting-card{
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
⚙️ Ρυθμίσεις
</h2>

<div class="setting-card">

<h5>
📧 Email
</h5>

<p>
student@email.gr
</p>

</div>

<div class="setting-card">

<h5>
📱 Τηλέφωνο
</h5>

<p>
6900000000
</p>

</div>

<div class="setting-card">

<h5>
🔒 Κωδικός Πρόσβασης
</h5>

<button class="btn btn-warning">

Αλλαγή Κωδικού

</button>

</div>

<div class="setting-card">

<h5>
ℹ️ Έκδοση Εφαρμογής
</h5>

<p>
SchoolMedia v1.0
</p>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>
<?php require_once '../includes/mobile_footer.php'; ?>
</body>
</html>