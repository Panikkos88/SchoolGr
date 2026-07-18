<?php

if(file_exists('../install.lock')){
    exit('Το SchoolMedia έχει ήδη εγκατασταθεί.');
}

?>

<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>SchoolMedia Installer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{

max-width:700px;

margin:50px auto;

background:white;

padding:40px;

border-radius:20px;

box-shadow:0 5px 20px rgba(0,0,0,.10);

}

.logo{

font-size:70px;

text-align:center;

}

.step{

font-size:18px;

color:#777;

text-align:center;

margin-bottom:15px;

}

.btn{

border-radius:12px;

padding:15px;

font-size:20px;

}

</style>

</head>

<body>

<div class="container">

<div class="card-box">

<div class="logo">

🏫

</div>

<h1 class="text-center">

SchoolMedia

</h1>

<p class="step">

Βήμα 1 από 5

</p>

<hr>

<h4>

Καλώς ήρθατε στον οδηγό εγκατάστασης.

</h4>

<p>

Ο οδηγός θα σας βοηθήσει να εγκαταστήσετε το SchoolMedia σε λίγα μόνο βήματα.

</p>

<div class="alert alert-info">

✔ Έλεγχος PHP<br>
✔ Έλεγχος PDO<br>
✔ Δημιουργία σύνδεσης βάσης<br>
✔ Ρυθμίσεις σχολείου<br>
✔ SMTP<br>
✔ Δημιουργία Διαχειριστή

</div>

<a

href="database.php"

class="btn btn-success w-100">

➡️ Έναρξη Εγκατάστασης

</a>

</div>

</div>

</body>

</html>