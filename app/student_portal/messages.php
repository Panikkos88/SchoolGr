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

<title>Μηνύματα</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.message-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.date{
color:#888;
font-size:13px;
}

</style>

</head>
<body>

<div class="container py-4">

<h2 class="mb-4">
📨 Μηνύματα Σχολείου
</h2>

<div class="message-card">

<h4>
📢 Ανακοίνωση Διεύθυνσης
</h4>

<div class="date">
20/06/2026
</div>

<hr>

<p>
Αγαπητοί μαθητές, η σχολική γιορτή θα πραγματοποιηθεί την Παρασκευή στις 19:00.
</p>

</div>

<div class="message-card">

<h4>
🚌 Υπενθύμιση Εκδρομής
</h4>

<div class="date">
18/06/2026
</div>

<hr>

<p>
Μην ξεχάσετε να παραδώσετε τις δηλώσεις συμμετοχής.
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