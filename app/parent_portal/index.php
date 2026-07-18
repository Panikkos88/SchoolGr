<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
// require_once '../includes/update_check.php';
?>
<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Portal Γονέα</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.top-card{
background:white;
border-radius:15px;
padding:20px;
margin-bottom:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.menu-card{
border:none;
border-radius:15px;
padding:25px;
text-align:center;
cursor:pointer;
transition:.3s;
box-shadow:0 2px 10px rgba(0,0,0,.08);
background:white;
}

.menu-card:hover{
transform:translateY(-5px);
}

.icon{
font-size:40px;
}

.title{
margin-top:10px;
font-weight:bold;
}

a{
text-decoration:none;
color:black;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="top-card text-center">

<h2>
👨‍👩‍👧 Portal Γονέα
</h2>

<p class="mb-0">
Καλώς ήρθατε
</p>

</div>
	<div class="top-card">

<h4>🚌 Μεταφορά Μαθητή</h4>

<p>
Παρακολουθήστε την κατάσταση μεταφοράς του παιδιού σας.
</p>

<a href="transport_status.php"
class="btn btn-primary">

Προβολή Κατάστασης

</a>

</div>
<div class="row g-3">

<div class="col-6 col-md-4">

<a href="children.php">

<div class="menu-card">

<div class="icon">👦</div>

<div class="title">
Τα Παιδιά Μου
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="trips.php">

<div class="menu-card">

<div class="icon">🚌</div>

<div class="title">
Εκδρομές
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="finances.php">

<div class="menu-card">

<div class="icon">💰</div>

<div class="title">
Οικονομικά
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="announcements.php">

<div class="menu-card">

<div class="icon">📢</div>

<div class="title">
Ανακοινώσεις
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="absences.php">

<div class="menu-card">

<div class="icon">📝</div>

<div class="title">
Απουσίες
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="messages.php">

<div class="menu-card">

<div class="icon">📨</div>

<div class="title">
Μηνύματα
</div>

</div>

</a>

</div>

<div class="col-6 col-md-4">

<a href="../logout.php">

<div class="menu-card">

<div class="icon">🚪</div>

<div class="title">
Αποσύνδεση
</div>

</div>

</a>

</div>

</div>

</div>

</body>
</html>
