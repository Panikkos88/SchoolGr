<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$classes = $pdo->query("
SELECT *
FROM classes
ORDER BY id
")->fetchAll(PDO::FETCH_ASSOC);

$sections = $pdo->query("
SELECT *
FROM sections
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=='POST'){

    $target_type = $_POST['target_type'];
    $target_id = !empty($_POST['target_id']) ? $_POST['target_id'] : null;

    $stmt = $pdo->prepare("
    INSERT INTO announcements
    (
        title,
        content,
        target_type,
        target_id,
        created_by
    )
    VALUES
    (
        ?,?,?,?,?
    )
    ");

    $stmt->execute([

    $_POST['title'],
    $_POST['content'],
    $target_type,
    $target_id,
    $_SESSION['user_id']

]);

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

$mail_settings = [];

$q = $pdo->query("
SELECT setting_key, setting_value
FROM settings
");

while($row = $q->fetch(PDO::FETCH_ASSOC)){
    $mail_settings[$row['setting_key']] = $row['setting_value'];
}

if($target_type == 'school'){

    $parents = $pdo->query("
    SELECT DISTINCT email
    FROM parents
    WHERE email IS NOT NULL
    AND email <> ''
    ");

}
elseif($target_type == 'class'){

    $parents = $pdo->prepare("
    SELECT DISTINCT p.email
    FROM parents p
    INNER JOIN parent_students ps
    ON p.id = ps.parent_id
    INNER JOIN students s
    ON s.id = ps.student_id
    WHERE s.class_id = ?
    AND p.email <> ''
    ");

    $parents->execute([$target_id]);

}
else{

    $parents = $pdo->prepare("
    SELECT DISTINCT p.email
    FROM parents p
    INNER JOIN parent_students ps
    ON p.id = ps.parent_id
    INNER JOIN students s
    ON s.id = ps.student_id
    WHERE s.section_id = ?
    AND p.email <> ''
    ");

    $parents->execute([$target_id]);

}

foreach($parents as $parent){

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try{

        $mail->isSMTP();
        $mail->Host = $mail_settings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mail_settings['smtp_username'];
        $mail->Password = $mail_settings['smtp_password'];
        $mail->Port = $mail_settings['smtp_port'];
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            $mail_settings['mail_from'],
            'SchoolMedia'
        );

        $mail->addAddress($parent['email']);

        $mail->isHTML(true);

        $school_stmt = $pdo->query("
SELECT *
FROM school_settings
WHERE id=1
");

$school = $school_stmt->fetch(PDO::FETCH_ASSOC);

$logo = '';

if(!empty($school['school_logo'])){

    $logo =
    'https://2dmegarwn.eu/schoolmedia/uploads/logo/'.
    $school['school_logo'];

}

$mail->Subject = $_POST['title'];

$mail->Body = '

<div style="text-align:center">

'.(!empty($logo) ? '<img src="'.$logo.'" style="max-height:100px;"><br><br>' : '').'

<h2>'.$school['school_name'].'</h2>

<p>
Ημερομηνία: '.date("d/m/Y").'
</p>

</div>

<hr>

<h3>'.htmlspecialchars($_POST['title']).'</h3>

<p>
'.nl2br(htmlspecialchars($_POST['content'])).'
</p>

<hr>

<div style="text-align:center">

<strong>
'.$school['school_name'].'
</strong>

</div>

';

        $mail->send();

    }catch(Exception $e){

    }
}

header("Location: announcements.php");
exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['new_announcement']; ?></title>

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

<h2>📢 <?= $LANG['new_announcement']; ?></h2>

<div class="form-card">

<form method="post">

<div class="mb-3">
<label><?= $LANG['title']; ?></label>
<input
type="text"
name="title"
class="form-control"
required>
</div>

<div class="mb-3">
<label><?= $LANG['announcement_text']; ?></label>
<textarea
name="content"
class="form-control"
rows="6"
required></textarea>
</div>

<div class="mb-3">
<label><?= $LANG['recipients']; ?></label>

<select
name="target_type"
class="form-control"
required>

<option value="school">
<?= $LANG['whole_school']; ?>
</option>

<option value="class">
<?= $LANG['specific_class']; ?>
</option>

<option value="section">
<?= $LANG['specific_section']; ?>
</option>

</select>

</div>

<div class="mb-3">

<label><?= $LANG['class_or_section']; ?></label>

<select
name="target_id"
class="form-control">

<option value="">
<?= $LANG['not_required']; ?>
</option>

<optgroup label="<?= $LANG['classes_group']; ?>">

<?php foreach($classes as $class): ?>

<option value="<?= $class['id']; ?>">
<?= $LANG['class']; ?> <?= htmlspecialchars($class['name']); ?>
</option>

<?php endforeach; ?>

</optgroup>

<optgroup label="<?= $LANG['sections_group']; ?>">

<?php foreach($sections as $section): ?>

<option value="<?= $section['id']; ?>">
<?= htmlspecialchars($section['name']); ?>
</option>

<?php endforeach; ?>

</optgroup>

</select>

</div>

<button
type="submit"
class="btn btn-success w-100">
💾 <?= $LANG['save']; ?>
</button>

</form>
<br>

<a
href="announcements.php"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</div>

</body>
</html>