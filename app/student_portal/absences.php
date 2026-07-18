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

<title>Απουσίες</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.stat-card{
    border:none;
    border-radius:15px;
    padding:20px;
    text-align:center;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.history-card{
    background:white;
    border-radius:15px;
    padding:15px;
    margin-bottom:15px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
📝 Οι Απουσίες Μου
</h2>

<div class="row g-3 mb-4">

<div class="col-4">

<div class="stat-card bg-primary text-white">

<h4>2</h4>

<div>Σύνολο</div>

</div>

</div>

<div class="col-4">

<div class="stat-card bg-success text-white">

<h4>1</h4>

<div>Δικαιολογημένες</div>

</div>

</div>

<div class="col-4">

<div class="stat-card bg-danger text-white">

<h4>1</h4>

<div>Αδικαιολόγητες</div>

</div>

</div>

</div>

<h4 class="mb-3">
📋 Ιστορικό
</h4>

<div class="history-card">

<strong>20/06/2026</strong>

<br>

1 ώρα

<br>

<span class="badge bg-success">
Δικαιολογημένη
</span>

</div>

<div class="history-card">

<strong>15/06/2026</strong>

<br>

1 ώρα

<br>

<span class="badge bg-danger">
Αδικαιολόγητη
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