<?php

session_start();

require_once '../config/database.php';

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$student_id = (int)($_GET['student_id'] ?? 0);

$bus_id = 1;

$update = $pdo->prepare("
UPDATE student_bus_status
SET
delivered_home = 1,
delivered_home_time = NOW()
WHERE student_id = ?
AND bus_id = ?
");

$update->execute([
    $student_id,
    $bus_id
]);

$student_stmt = $pdo->prepare("
SELECT *
FROM students
WHERE id=?
");

$student_stmt->execute([$student_id]);
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

$parent_stmt = $pdo->prepare("
SELECT p.*
FROM parents p
INNER JOIN parent_students ps
ON p.id = ps.parent_id
WHERE ps.student_id=?
LIMIT 1
");

$parent_stmt->execute([$student_id]);
$parent = $parent_stmt->fetch(PDO::FETCH_ASSOC);

$settings = [];

$q = $pdo->query("
SELECT setting_key, setting_value
FROM settings
");

while($row = $q->fetch(PDO::FETCH_ASSOC)){
    $settings[$row['setting_key']] = $row['setting_value'];
}

if(!empty($parent['email'])){

    try{

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $settings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['smtp_username'];
        $mail->Password = $settings['smtp_password'];
        $mail->Port = $settings['smtp_port'];

        if($settings['smtp_encryption']=='ssl'){
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            $settings['mail_from'],
            'SchoolMedia'
        );

        $mail->addAddress($parent['email']);

        $mail->isHTML(true);

        $mail->Subject = 'Παράδοση μαθητή';

        $mail->Body = '
        <h2>🏠 Παράδοση μαθητή</h2>

        <p>
        Ο μαθητής
        <strong>'.
        htmlspecialchars($student['first_name'].' '.$student['last_name'])
        .'</strong>
        παραδόθηκε με ασφάλεια στον γονέα.
        </p>

        <p>
        Ώρα Παράδοσης: '.date('d/m/Y H:i').'
        </p>

        <hr>

        <p>SchoolMedia</p>
        ';

        $mail->send();

    }catch(Exception $e){

    }
}

header("Location: index.php");
exit;