<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

if($_SERVER['REQUEST_METHOD']=='POST'){

    foreach($_POST as $key => $value){

        $stmt = $pdo->prepare("
        INSERT INTO settings
        (
            setting_key,
            setting_value
        )
        VALUES
        (
            ?,?
        )
        ON DUPLICATE KEY UPDATE
        setting_value=VALUES(setting_value)
        ");

        $stmt->execute([
            $key,
            $value
        ]);
    }

    $saved = true;
}

$stmt = $pdo->query("
SELECT *
FROM settings
");

$settings = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $settings[$row['setting_key']] = $row['setting_value'];

}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['email_settings']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-4">

<h2>
📧 <?= $LANG['email_settings']; ?>
</h2>

<?php if(!empty($saved)): ?>

<div class="alert alert-success">

<?= $LANG['settings_saved']; ?>

</div>

<?php endif; ?>

<form method="post">

<div class="mb-3">

<label>SMTP Host</label>

<input
type="text"
name="smtp_host"
class="form-control"
value="<?= htmlspecialchars($settings['smtp_host'] ?? ''); ?>">

</div>

<div class="mb-3">

<label>SMTP Port</label>

<input
type="text"
name="smtp_port"
class="form-control"
value="<?= htmlspecialchars($settings['smtp_port'] ?? ''); ?>">

</div>

<div class="mb-3">

<label>SMTP Username</label>

<input
type="text"
name="smtp_username"
class="form-control"
value="<?= htmlspecialchars($settings['smtp_username'] ?? ''); ?>">

</div>

<div class="mb-3">

<label>SMTP Password</label>

<input
type="password"
name="smtp_password"
class="form-control"
value="<?= htmlspecialchars($settings['smtp_password'] ?? ''); ?>">

</div>

<div class="mb-3">

	<label><?= $LANG['encryption']; ?></label>
	<select
name="smtp_encryption"
class="form-control">

<option
value="tls"
<?= (($settings['smtp_encryption'] ?? '')=='tls') ? 'selected' : ''; ?>>

TLS

</option>

<option
value="ssl"
<?= (($settings['smtp_encryption'] ?? '')=='ssl') ? 'selected' : ''; ?>>

SSL

</option>

	</select>

</div>

<div class="mb-3">

<label><?= $LANG['from_email']; ?></label>

<input
type="email"
name="mail_from"
class="form-control"
value="<?= htmlspecialchars($settings['mail_from'] ?? ''); ?>">

</div>

<button
type="submit"
class="btn btn-success w-100">

💾 <?= $LANG['save']; ?>

</button>

</form>

<br>

<a href="settings.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</body>
</html>