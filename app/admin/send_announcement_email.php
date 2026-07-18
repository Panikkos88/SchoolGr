<?php
session_start();

if(!isset($_SESSION['user_id'])){
die($LANG['access_denied']);
}

require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM announcements
WHERE id=?
");

$stmt->execute([$id]);

$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$announcement){
die($LANG['announcement_not_found']);
}

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$settings = [];

$s = $pdo->query("
SELECT setting_key, setting_value
FROM settings
");

while($row = $s->fetch(PDO::FETCH_ASSOC)){
    $settings[$row['setting_key']] = $row['setting_value'];
}

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();
    $mail->Host = $settings['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_username'];
    $mail->Password = $settings['smtp_password'];

    if($settings['smtp_encryption']=='ssl'){
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    }else{
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->Port = $settings['smtp_port'];

    $mail->setFrom(
        $settings['mail_from'],
       $settings['mail_from_name'] ?? 'SchoolMedia'
    );

    $parents = $pdo->query("
    SELECT *
    FROM parents
    WHERE email IS NOT NULL
      AND email <> ''
    ");

    while($parent = $parents->fetch(PDO::FETCH_ASSOC)){

        $mail->clearAddresses();

        $mail->addAddress($parent['email']);

        $mail->isHTML(true);

        $mail->Subject = $announcement['title'];

        $mail->Body =
        "<h2>".$announcement['title']."</h2>".
        "<p>".nl2br($announcement['content'])."</p>";

        $mail->send();
    }

echo $LANG['email_sent_success'];

}catch(Exception $e){

  echo $LANG['error'].': '.htmlspecialchars($mail->ErrorInfo);

}