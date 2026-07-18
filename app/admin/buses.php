<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';
if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO buses
    (
        name,
        driver_name,
        driver_phone,
        gps_device_id
    )
    VALUES
    (
        ?,?,?,?
    )
    ");

    $stmt->execute([

        $_POST['name'],
        $_POST['driver_name'],
        $_POST['driver_phone'],
        $_POST['gps_device_id']

    ]);

    header("Location: buses.php");
    exit;
}

$buses = $pdo->query("
SELECT *
FROM buses
ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?= $LANG['buses']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:20px;
border-radius:15px;
margin-bottom:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">
<h2>
🚌 <?= $LANG['buses']; ?>
	</h2>
<div class="card-box">

<form method="post">

<div class="mb-3">
	<label><?= $LANG['bus_name']; ?></label>
<input
type="text"
name="name"
class="form-control"
placeholder="<?= $LANG['bus_name']; ?>"
required>

</div>

<div class="mb-3">
	<label><?= $LANG['driver']; ?></label>
<input
type="text"
name="driver_name"
class="form-control">

</div>

<div class="mb-3">
	<label><?= $LANG['driver_phone']; ?></label>
<input
type="text"
name="driver_phone"
class="form-control">

</div>

<div class="mb-3">
	<label><?= $LANG['gps_device_id']; ?></label>
<input
type="text"
name="gps_device_id"
class="form-control">

</div>

<button
type="submit"
class="btn btn-success w-100">
💾 <?= $LANG['save']; ?>
</button>

</form>

</div>

<?php foreach($buses as $bus): ?>

<div class="card-box">

<h5>

<a href="bus_view.php?id=<?= $bus['id']; ?>">

🚌 <?= htmlspecialchars($bus['name']); ?>

</a>

	</h5>

<p>
👨 <?= $LANG['driver']; ?>:
<?= htmlspecialchars($bus['driver_name']); ?>
</p>
<p>
📞 <?= $LANG['driver_phone']; ?>:
<?= htmlspecialchars($bus['driver_phone']); ?>
	</p>
<p>
📡 <?= $LANG['gps_device_id']; ?>:
<?= htmlspecialchars($bus['gps_device_id']); ?>
	</p>

</div>

<?php endforeach; ?>

<a
href="dashboard.php"
class="btn btn-secondary w-100">
⬅️ <?= $LANG['back']; ?>
</a>

</div>

</body>
</html>