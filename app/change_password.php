<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $password1 = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    if(empty($password1)){

        $error = "Δώσε νέο κωδικό.";

    }elseif($password1 != $password2){

        $error = "Οι κωδικοί δεν ταιριάζουν.";

    }else{

        $stmt = $pdo->prepare("
        UPDATE users
        SET
        password=?,
        must_change_password=0
        WHERE id=?
        ");

        $stmt->execute([
            $password1,
            $user_id
        ]);

        $stmt = $pdo->prepare("
        SELECT role
        FROM users
        WHERE id=?
        ");

        $stmt->execute([$user_id]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        switch($user['role']){

            case 'teacher':
                header("Location: teacher_portal/index.php");
                break;

            case 'student':
                header("Location: student_portal/index.php");
                break;

            case 'parent':
                header("Location: parent_portal/index.php");
                break;

            case 'driver':
                header("Location: driver_portal/index.php");
                break;

            default:
                header("Location: admin/dashboard.php");
                break;
        }

        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="el">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Αλλαγή Κωδικού</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
}

.box{
background:white;
padding:30px;
border-radius:15px;
width:100%;
max-width:500px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="box">

<h3 class="text-center mb-4">

🔑 Αλλαγή Κωδικού

</h3>

<?php if($error): ?>

<div class="alert alert-danger">

<?= $error; ?>

</div>

<?php endif; ?>

<form method="post">

<div class="mb-3">

<label>Νέος Κωδικός</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Επανάληψη Κωδικού</label>

<input
type="password"
name="password2"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 Αποθήκευση

</button>

</form>

</div>

</body>
</html>