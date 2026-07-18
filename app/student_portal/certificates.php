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

<title>Βεβαιώσεις</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.doc-card{
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
📄 Οι Βεβαιώσεις Μου
</h2>

<div class="doc-card">

<h4>
📄 Βεβαίωση Φοίτησης
</h4>

<p>
Έκδοση: 15/06/2026
</p>

<a href="#"
class="btn btn-primary">

⬇️ Λήψη

</a>

</div>

<div class="doc-card">

<h4>
📄 Βεβαίωση Επίσκεψης
</h4>

<p>
Έκδοση: 10/06/2026
</p>

<a href="#"
class="btn btn-primary">

⬇️ Λήψη

</a>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>
<?php require_once '../includes/mobile_footer.php'; ?>
</body>
</html>