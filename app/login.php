<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/license_check.php';
require_once 'includes/language.php';
$error = '';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE email=?
    LIMIT 1
    ");

    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){

        $validPassword = false;

        if(password_verify($password, $user['password'])){
            $validPassword = true;
        }elseif($password === $user['password']){

            $newHash = password_hash($password, PASSWORD_DEFAULT);

            $upd = $pdo->prepare("
            UPDATE users
            SET password=?
            WHERE id=?
            ");

            $upd->execute([
                $newHash,
                $user['id']
            ]);

            $validPassword = true;
        }

        if($validPassword){

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if(!empty($user['must_change_password']) && $user['must_change_password']==1){
                header("Location: change_password.php");
                exit;
            }

            switch($user['role']){
                case 'student':
                    header("Location: student_portal/index.php");
                    break;
                case 'parent':
                    header("Location: parent_portal/index.php");
                    break;
                case 'teacher':
                    header("Location: teacher_portal/index.php");
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

    $error = $LANG['login_error'];
}

?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#0d6efd">
<meta charset="UTF-8">

<title>SchoolMedia</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js');
}
	</script>
<style>

body{
background:#f4f6f9;
display:flex;
justify-content:center;
align-items:center;
min-height:100vh;
}

.login-box{
background:white;
padding:40px;
border-radius:20px;
width:100%;
max-width:420px;
box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

.logo{
font-size:60px;
text-align:center;
}

.title{
text-align:center;
font-size:28px;
font-weight:bold;
margin-bottom:10px;
}

.subtitle{
text-align:center;
color:#777;
margin-bottom:30px;
}

</style>

</head>

<body>

<div class="login-box">

<div class="logo">
🎓
	</div>

<div class="title">
SchoolMedia
</div>

<div class="subtitle">
<?= $LANG['school_platform'] ?>
</div>

<?php if($error): ?>

<div class="alert alert-danger">
<?= $error; ?>
</div>

<?php endif; ?>

<form method="post">

<div class="mb-3">

	<label><?= $LANG['email']; ?></label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">
<label><?= $LANG['password'] ?></label>
<input
type="password"
name="password"
class="form-control"
required>

</div>
<div class="form-check mb-3">
    <input
    class="form-check-input"
    type="checkbox"
    name="remember"
    id="remember">

    <label class="form-check-label" for="remember">
        <?= $LANG['remember_me'] ?>
    </label>
	</div>
	<button
type="submit"
class="btn btn-primary w-100">

<?= $LANG['login']; ?>

	</button>

</form>

<br>

<div class="text-center text-muted">
	© SchoolMedia
</div>

</div>

</body>
</html>