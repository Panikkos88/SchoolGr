<?php

if(file_exists('../install.lock')){
    exit('Το SchoolMedia έχει ήδη εγκατασταθεί.');
}

$message = '';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $host = trim($_POST['host']);
    $dbname = trim($_POST['dbname']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $content = "<?php

\$host = '".$host."';
\$dbname = '".$dbname."';
\$username = '".$username."';
\$password = '".$password."';

try{

\$pdo = new PDO(
    \"mysql:host=\$host;dbname=\$dbname;charset=utf8mb4\",
    \$username,
    \$password
);

\$pdo->setAttribute(
PDO::ATTR_ERRMODE,
PDO::ERRMODE_EXCEPTION
);

}catch(PDOException \$e){

die(\$e->getMessage());

}
";

    file_put_contents(
    '../config/database.php',
    $content
);

header("Location: school.php");
exit;
}
?>

<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>SchoolMedia Installer</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card-box{
    max-width:700px;
    margin:40px auto;
    background:#fff;
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
🗄 Ρύθμιση Βάσης Δεδομένων
</h2>

<div class="step">
Βήμα 2 από 5
</div>

<div class="progress mb-4">

<div
class="progress-bar bg-success"
style="width:40%">

</div>

</div>

<?php if($message): ?>

<div class="alert alert-success">

<?= $message; ?>

</div>

<?php endif; ?>

<form method="post">

<div class="mb-3">

<label class="form-label">

Host

</label>

<input
type="text"
name="host"
class="form-control"
value="localhost"
required>

</div>

<div class="mb-3">

<label class="form-label">

Database

</label>

<input
type="text"
name="dbname"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Username

</label>

<input
type="text"
name="username"
class="form-control"
required>

</div>

<div class="mb-4">

<label class="form-label">

Password

</label>

<input
type="password"
name="password"
class="form-control">

</div>

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success">

💾 Αποθήκευση

</button>

<a
href="index.php"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</form>

</div>

</div>

</body>

</html>