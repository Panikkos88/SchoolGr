<?php

session_start();

require_once '../config/database.php';

require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$student_id = (int)($_GET['student_id'] ?? 0);

$bus_id = 1;

/*
|--------------------------------------
| Αποθήκευση παραλαβής
|--------------------------------------
*/

$stmt = $pdo->prepare("
SELECT *
FROM student_bus_status
WHERE student_id=?
AND bus_id=?
");

$stmt->execute([
    $student_id,
    $bus_id
]);

$exists = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$exists){

    $insert = $pdo->prepare("
    INSERT INTO student_bus_status
    (
        student_id,
        bus_id,
        picked_up,
        pickup_time
    )
    VALUES
    (
        ?,?,1,NOW()
    )
    ");

    $insert->execute([
        $student_id,
        $bus_id
    ]);

}else{

    $update = $pdo->prepare("
    UPDATE student_bus_status
    SET
    picked_up=1,
    pickup_time=NOW()
    WHERE student_id=?
    AND bus_id=?
    ");

    $update->execute([
        $student_id,
        $bus_id
    ]);
}

/*
|--------------------------------------
| Στοιχεία μαθητή
|--------------------------------------
*/

$student_stmt = $pdo->prepare("
SELECT *
FROM students
WHERE id=?
");

$student_stmt->execute([$student_id]);

$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------
| Στοιχεία λεωφορείου
|--------------------------------------
*/

$bus_stmt = $pdo->prepare("
SELECT *
FROM buses
WHERE id=?
");

$bus_stmt->execute([$bus_id]);

$bus = $bus_stmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------
| Email γονέα
|--------------------------------------
*/

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

/*
|--------------------------------------
| SMTP Settings
|--------------------------------------
*/

$settings = [];

$q = $pdo->query("
SELECT setting_key, setting_value
FROM settings
");

while($row = $q->fetch(PDO::FETCH_ASSOC)){
    $settings[$row['setting_key']] = $row['setting_value'];
}

/*
|--------------------------------------
| Αποστολή Email
|--------------------------------------
*/

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

        $mail->Subject =
        'Επιβίβαση μαθητή στο σχολικό';

        $mail->Body = '

        <h2>🚌 Επιβίβαση στο σχολικό</h2>

        <p>
        Ο μαθητής:
        <strong>'.
        htmlspecialchars($student['first_name'].' '.$student['last_name'])
        .'</strong>
        επιβιβάστηκε στο σχολικό λεωφορείο.
        </p>

        <p>
        <strong>Λεωφορείο:</strong>
        '.htmlspecialchars($bus['name']).'
        </p>

        <p>
        <strong>Οδηγός:</strong>
        '.htmlspecialchars($bus['driver_name']).'
        </p>

        <p>
        <strong>Ώρα:</strong>
        '.date('d/m/Y H:i').'
        </p>

        <hr>

        <p>
        SchoolMedia
        </p>
        ';

        $mail->send();

    }catch(Exception $e){

    }
}

header("Location: index.php");
exit;