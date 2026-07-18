<?php

require_once '../config/database.php';

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$settings = [];

$stmt = $pdo->query("
SELECT setting_key, setting_value
FROM settings
");

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $settings[$row['setting_key']] = $row['setting_value'];
}

$mail = new PHPMailer(true);

try{

    $mail->isSMTP();

    $mail->Host = $settings['smtp_host'];

    $mail->SMTPAuth = true;

    $mail->Username = $settings['smtp_username'];

    $mail->Password = $settings['smtp_password'];

    $mail->Port = $settings['smtp_port'];

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        $settings['mail_from'],
        'SchoolMedia'
    );

    $mail->addAddress('info@2dmegarwn.eu');

    $mail->Subject = 'TEST EMAIL';

    $mail->Body = 'Το email λειτουργεί!';

    $mail->send();

    echo "EMAIL SENT";

}
catch(Exception $e){

    echo "ERROR:<br>";
    echo $mail->ErrorInfo;

}