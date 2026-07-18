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

<title>Πρόγραμμα</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.day-card{
    background:white;
    border-radius:15px;
    padding:20px;
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
📚 Πρόγραμμα Μαθημάτων
</h2>

<div class="day-card">

<h4>Δευτέρα</h4>

<div class="lesson">
08:00 - Μαθηματικά
</div>

<div class="lesson">
09:00 - Γλώσσα
</div>

<div class="lesson">
10:00 - Ιστορία
</div>

</div>

<div class="day-card">

<h4>Τρίτη</h4>

<div class="lesson">
08:00 - Φυσική
</div>

<div class="lesson">
09:00 - Χημεία
</div>

<div class="lesson">
10:00 - Αγγλικά
</div>

</div>

<div class="day-card">

<h4>Τετάρτη</h4>

<div class="lesson">
08:00 - Πληροφορική
</div>

<div class="lesson">
09:00 - Γυμναστική
</div>

</div>

<a href="index.php"
class="btn btn-secondary">

🏠 Επιστροφή

</a>

</div>
<?php require_once '../includes/mobile_footer.php'; ?>
</body>
</html>