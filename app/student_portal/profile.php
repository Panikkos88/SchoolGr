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

<title>Το Προφίλ Μου</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card{
    border:none;
    border-radius:15px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    background:#0d6efd;
    color:white;
    font-size:50px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="card p-4">

<div class="text-center">

<div class="avatar">
👨‍🎓
</div>

<h2 class="mt-3">
Αναστασία Λεβάντη
</h2>

<p class="text-muted">
Μαθητής
</p>

</div>

<hr>

<div class="row">

<div class="col-md-6">

<p>
<strong>Αριθμός Μητρώου:</strong>
12345
</p>

<p>
<strong>Ημερομηνία Γέννησης:</strong>
01/01/2015
</p>

<p>
<strong>Email:</strong>
student@email.gr
</p>

</div>

<div class="col-md-6">

<p>
<strong>Τάξη:</strong>
Α2
</p>

<p>
<strong>Τμήμα:</strong>
Α1
</p>

<p>
<strong>Κατάσταση:</strong>
Ενεργός
</p>

</div>

</div>

<hr>

<a href="index.php"
class="btn btn-primary">

🏠 Επιστροφή

</a>

</div>

</div>
<?php require_once '../includes/mobile_footer.php'; ?>
</body>
</html>