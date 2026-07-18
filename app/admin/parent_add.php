<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
$error='';

if($_SERVER['REQUEST_METHOD']=='POST'){

$email=trim($_POST['email']);

$check=$pdo->prepare("
SELECT id
FROM users
WHERE email=?
LIMIT 1
");

$check->execute([$email]);

if($check->fetch()){

    $error="Το email χρησιμοποιείται ήδη.";

}else{

    $stmt = $pdo->prepare("
    INSERT INTO parents
    (
        first_name,
        last_name,
        phone,
        email,
        address,
        relationship
    )
    VALUES
    (
        ?,?,?,?,?,?
    )
    ");

    $stmt->execute([

        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['address'],
        $_POST['relationship']

    ]);
	$parent_id = $pdo->lastInsertId();

$default_password = '123456';

$stmt = $pdo->prepare("
INSERT INTO users
(
    first_name,
    last_name,
    email,
    password,
    role,
    parent_id
)
VALUES
(
    ?, ?, ?, ?, 'parent', ?
)
");

$stmt->execute([

    $_POST['first_name'],
    $_POST['last_name'],
    $email,
    password_hash($default_password, PASSWORD_DEFAULT),
    $parent_id
]);
    header("Location: parents.php");
exit;

}

}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?= $LANG['new_parent']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.form-card{
background:white;
padding:25px;
border-radius:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
👨‍👩‍👧 <?= $LANG['new_parent']; ?>
</h2>

<div class="form-card">
<?php if(!empty($error)): ?>

<div class="alert alert-danger">

❌ <?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>
<form method="post">

<div class="mb-3">
	<label><?= $LANG['first_name']; ?></label>
<input type="text" name="first_name" class="form-control" required>
</div>

<div class="mb-3">
	<label><?= $LANG['last_name']; ?></label>
<input type="text" name="last_name" class="form-control" required>
</div>

<div class="mb-3">
	<label><?= $LANG['phone']; ?></label>
<input type="text" name="phone" class="form-control">
</div>

<div class="mb-3">
	<label><?= $LANG['email']; ?></label>
<input type="email" name="email" class="form-control">
</div>

<div class="mb-3">
	<label><?= $LANG['address']; ?></label>
<textarea name="address" class="form-control"></textarea>
</div>

<div class="mb-3">
	<label><?= $LANG['relationship']; ?></label>

<select name="relationship" class="form-control">

<option value="father">
<?= $LANG['father']; ?>
	</option>

<option value="mother">
<?= $LANG['mother']; ?>
	</option>

<option value="guardian">
<?= $LANG['guardian']; ?>
	</option>

</select>

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>

</form>

</div>

<br>

<a href="parents.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>