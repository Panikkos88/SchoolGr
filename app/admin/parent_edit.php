<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM parents
WHERE id=?
");

$stmt->execute([$id]);

$parent = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$parent){
    die("Ο γονέας δεν βρέθηκε.");
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    UPDATE parents
    SET
        first_name=?,
        last_name=?,
        phone=?,
        email=?,
        relationship=?
    WHERE id=?
    ");

    $stmt->execute([

        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['relationship'],
        $id

    ]);

    header("Location: parent_view.php?id=".$id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?= $LANG['edit_parent']; ?></title>

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

<h2>
✏️ <?= $LANG['edit_parent']; ?>
</h2>

<div class="form-card">

<form method="post">

<div class="mb-3">
	<label><?= $LANG['first_name']; ?></label>
<input
type="text"
name="first_name"
class="form-control"
value="<?= htmlspecialchars($parent['first_name']); ?>"
required>
</div>

<div class="mb-3">
	<label><?= $LANG['last_name']; ?></label>
<input
type="text"
name="last_name"
class="form-control"
value="<?= htmlspecialchars($parent['last_name']); ?>"
required>
</div>

<div class="mb-3">
	<label><?= $LANG['phone']; ?></label>
<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($parent['phone']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['email']; ?></label>
<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($parent['email']); ?>">
</div>

<div class="mb-3">
	<label><?= $LANG['relationship']; ?></label>
<select
name="relationship"
class="form-select">

<option value="father"
<?= $parent['relationship']=='father' ? 'selected' : ''; ?>>
<?= $LANG['father']; ?>
</option>

<option value="mother"
<?= $parent['relationship']=='mother' ? 'selected' : ''; ?>>
<?= $LANG['mother']; ?>
</option>

<option value="guardian"
<?= $parent['relationship']=='guardian' ? 'selected' : ''; ?>>
<?= $LANG['guardian']; ?>
</option>

</select>

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>
<br><br>

<a
href="parent_view.php?id=<?= $id; ?>"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

	</a>
</form>

</div>

</div>

</body>
	</html>