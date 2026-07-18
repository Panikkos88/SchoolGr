<?php

require_once __DIR__.'/config/database.php';

$message = '';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $license_key = trim($_POST['license_key']);

    $stmt = $pdo->prepare("
    DELETE FROM license_settings
    ");

    $stmt->execute();

    $stmt = $pdo->prepare("
    INSERT INTO license_settings
    (
        license_key
    )
    VALUES
    (
        ?
    )
    ");

    $stmt->execute([
        $license_key
    ]);

    header("Location: login.php");

    exit;

}
?>

<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<title>Ενεργοποίηση SchoolMedia</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-body">

<h3 class="mb-4 text-center">

🔒 Ενεργοποίηση SchoolMedia

</h3>

<form method="post">

<div class="mb-3">

<label>

License Key

</label>

<input
type="text"
name="license_key"
class="form-control"
required>

</div>

<button
class="btn btn-success w-100">

Ενεργοποίηση

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>