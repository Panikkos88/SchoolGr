<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
if(file_exists('../install.lock')){
    exit('Το SchoolMedia έχει ήδη εγκατασταθεί.');
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $logo='';

    if(!empty($_FILES['logo']['name'])){

        if(!is_dir('../uploads')){
            mkdir('../uploads',0777,true);
        }

        $logo='uploads/'.time().'_'.basename($_FILES['logo']['name']);

        move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            '../'.$logo
        );
    }

    $pdo->exec("DELETE FROM school_settings");

    $stmt=$pdo->prepare("
    INSERT INTO school_settings
    (
        school_name,
        school_email,
        school_phone,
        school_address,
        school_logo
    )
    VALUES
    (
        ?,?,?,?,?
    )
    ");

    $stmt->execute([
        $_POST['school_name'],
        $_POST['school_email'],
        $_POST['school_phone'],
        $_POST['school_address'],
        $logo
    ]);

    header("Location: smtp.php");
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

🏫 Στοιχεία Σχολείου

</h2>

<div class="step">

Βήμα 3 από 5

</div>

<div class="progress mb-4">

<div
class="progress-bar bg-success"
style="width:60%">

</div>

</div>

<form
method="post"
enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">

Όνομα Σχολείου

</label>

<input
type="text"
name="school_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
name="school_email"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

Τηλέφωνο

</label>

<input
type="text"
name="school_phone"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

Διεύθυνση

</label>

<textarea
name="school_address"
class="form-control"
rows="3"></textarea>

</div>

<div class="mb-4">

<label class="form-label">

Λογότυπο

</label>

<input
type="file"
name="logo"
class="form-control">

</div>

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success">

➡️ Αποθήκευση & Συνέχεια

</button>

<a
href="database.php"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</form>

</div>

</div>

</body>

</html>