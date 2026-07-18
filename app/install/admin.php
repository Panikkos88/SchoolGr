<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

require_once '../config/database.php';

$installed = false;

if($_SERVER['REQUEST_METHOD']=='POST'){

    $check = $pdo->query("
    SELECT id
    FROM users
    WHERE role='admin'
    LIMIT 1
    ");

    if(!$check->fetch()){

        $stmt = $pdo->prepare("
        INSERT INTO users
        (
            first_name,
            last_name,
            email,
            password,
            role
        )
        VALUES
        (
            ?,?,?,?,?
        )
        ");

        $stmt->execute([

            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            password_hash(
                $_POST['password'],
                PASSWORD_DEFAULT
            ),
            'admin'

        ]);

    }

    file_put_contents(
        '../config/installed.php',
        "<?php define('INSTALLED',true);"
    );

    file_put_contents(
        '../install.lock',
        date('Y-m-d H:i:s')
    );

    $installed = true;

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
margin:40px auto;
background:white;
padding:35px;
border-radius:20px;
box-shadow:0 5px 20px rgba(0,0,0,.10);
}

.progress{
height:12px;
border-radius:20px;
}

.btn{
padding:14px;
font-size:18px;
border-radius:12px;
}

.step{
text-align:center;
color:#666;
margin-bottom:15px;
font-size:18px;
}

</style>

</head>

<body>

<div class="container">

<div class="card-box">

<h2 class="text-center">

👤 Δημιουργία Διαχειριστή

</h2>

<div class="step">

Βήμα 5 από 5

</div>

<div class="progress mb-4">

<div
class="progress-bar bg-success"
style="width:100%">

</div>

</div>

<?php if($installed){ ?>

<div class="alert alert-success text-center">

<h4>🎉 Η εγκατάσταση ολοκληρώθηκε!</h4>

<p>

Το SchoolMedia εγκαταστάθηκε με επιτυχία.

</p>

</div>

<div class="d-grid gap-2">

<a
href="../login.php"
class="btn btn-primary">

🔑 Μετάβαση στη Σύνδεση

</a>

</div>

<?php }else{ ?>

<form method="post">

<div class="mb-3">

<label class="form-label">

Όνομα

</label>

<input
type="text"
name="first_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Επώνυμο

</label>

<input
type="text"
name="last_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-4">

<label class="form-label">

Κωδικός

</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success">

🚀 Ολοκλήρωση Εγκατάστασης

</button>

<a
href="smtp.php"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</form>

<?php } ?>

</div>

</div>

</body>

</html>