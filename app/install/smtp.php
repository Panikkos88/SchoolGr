<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

if(file_exists('../install.lock')){
    exit('Το SchoolMedia έχει ήδη εγκατασταθεί.');
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $pdo->exec("DELETE FROM smtp_settings");

    $stmt = $pdo->prepare("
    INSERT INTO smtp_settings
    (
        smtp_host,
        smtp_port,
        smtp_username,
        smtp_password,
        sender_email,
        sender_name
    )
    VALUES
    (
        ?,?,?,?,?,?
    )
    ");

    $stmt->execute([
        $_POST['smtp_host'],
        $_POST['smtp_port'],
        $_POST['smtp_username'],
        $_POST['smtp_password'],
        $_POST['sender_email'],
        $_POST['sender_name']
    ]);

    header("Location: admin.php");
    exit;
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

📧 Ρυθμίσεις SMTP

</h2>

<div class="step">

Βήμα 4 από 5

</div>

<div class="progress mb-4">

<div
class="progress-bar bg-success"
style="width:80%">

</div>

</div>

<form method="post">

<div class="mb-3">

<label class="form-label">

SMTP Host

</label>

<input
type="text"
name="smtp_host"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

SMTP Port

</label>

<input
type="text"
name="smtp_port"
class="form-control"
value="587">

</div>

<div class="mb-3">

<label class="form-label">

SMTP Username

</label>

<input
type="text"
name="smtp_username"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

SMTP Password

</label>

<input
type="password"
name="smtp_password"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

Email Αποστολέα

</label>

<input
type="email"
name="sender_email"
class="form-control">

</div>

<div class="mb-4">

<label class="form-label">

Όνομα Αποστολέα

</label>

<input
type="text"
name="sender_name"
class="form-control">

</div>

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success">

➡️ Αποθήκευση & Συνέχεια

</button>

<a
href="school.php"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</form>

</div>

</div>

</body>

</html>