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

<title>Οι Γονείς Μου</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.parent-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.avatar{
font-size:50px;
text-align:center;
margin-bottom:10px;
}

</style>
</head>
<body>

<div class="container py-4">

<h2 class="mb-4">
👨‍👩‍👧 Οι Γονείς Μου
</h2>

<div class="parent-card">

<div class="avatar">
👨
</div>

<h4>
Γιάννης Λεβάντης
</h4>

<p>
📞 6900000000
</p>

<p>
📧 parent@email.gr
</p>

<p>
👨 Πατέρας
</p>

</div>

<div class="parent-card">

<div class="avatar">
👩
</div>

<h4>
Μαρία Λεβάντη
</h4>

<p>
📞 6900000001
</p>

<p>
📧 mother@email.gr
</p>

<p>
👩 Μητέρα
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