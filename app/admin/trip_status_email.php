<?php
error_reporting(E_ALL);
ini_set('display_errors',0);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id=(int)($_GET['id'] ?? 0);

$status=$_GET['status'] ?? '';

$stmt=$pdo->prepare("
SELECT *
FROM trips
WHERE id=?
");

$stmt->execute([$id]);

$trip=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$trip){
die("Η εκδρομή δεν βρέθηκε.");
}

$upd=$pdo->prepare("
UPDATE trips
SET trip_status=?
WHERE id=?
");

$upd->execute([
$status,
$id
]);
$history=$pdo->prepare("
INSERT INTO trip_status_history
(
trip_id,
status,
changed_by
)
VALUES
(
?,?,?
)
");

$history->execute([

$id,

$status,

$_SESSION['user_id']

]);
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$settings=[];

$s=$pdo->query("
SELECT setting_key,setting_value
FROM settings
");

while($row=$s->fetch(PDO::FETCH_ASSOC())){

    $settings[$row['setting_key']]=$row['setting_value'];

}

switch($status){

case 'departed':

$subject="🚌 Αναχώρηση Εκδρομής";

$message="Το λεωφορείο αναχώρησε από το σχολείο.";

break;

case 'arrived':

$subject="📍 Άφιξη στην Εκδρομή";

$message="Οι μαθητές έφτασαν με ασφάλεια στον προορισμό.";

break;

case 'returning':

$subject="↩️ Αναχώρηση Επιστροφής";

$message="Το λεωφορείο αναχώρησε για την επιστροφή στο σχολείο.";

break;

case 'finished':

$subject="🏫 Άφιξη στο Σχολείο";

$message="Οι μαθητές επέστρεψαν με ασφάλεια στο σχολείο.";

break;

default:

$subject="Ενημέρωση Εκδρομής";

$message="Υπάρχει νέα ενημέρωση.";

}
$parents=$pdo->prepare("
SELECT
p.email,
p.first_name,
s.first_name AS student_first_name,
s.last_name AS student_last_name
FROM trip_signatures ts
INNER JOIN parents p
ON p.id=ts.parent_id
INNER JOIN students s
ON s.id=ts.student_id
WHERE ts.trip_id=?
AND p.email<>''
AND p.email IS NOT NULL
");

$parents->execute([$id]);

$school_stmt=$pdo->query("
SELECT *
FROM school_settings
WHERE id=1
");

$school=$school_stmt->fetch(PDO::FETCH_ASSOC);

$logo='';

if(!empty($school['school_logo'])){

    $logo='https://2dmegarwn.eu/schoolmedia/uploads/logo/'.$school['school_logo'];

}

while($parent=$parents->fetch(PDO::FETCH_ASSOC)){

    $mail=new PHPMailer(true);

    try{

        $mail->isSMTP();

        $mail->Host=$settings['smtp_host'];

        $mail->SMTPAuth=true;

        $mail->Username=$settings['smtp_username'];

        $mail->Password=$settings['smtp_password'];

        $mail->Port=$settings['smtp_port'];

        if($settings['smtp_encryption']=='ssl'){

            $mail->SMTPSecure=PHPMailer::ENCRYPTION_SMTPS;

        }else{

            $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;

        }

        $mail->CharSet='UTF-8';

        $mail->setFrom(

            $settings['mail_from'],

            $settings['mail_from_name'] ?? 'SchoolMedia'

        );

        $mail->addAddress($parent['email']);

        $mail->isHTML(true);

        $mail->Subject=$subject;

        $mail->Body='

<div style="text-align:center">

'.(!empty($logo) ? '<img src="'.$logo.'" style="max-height:120px;"><br><br>' : '').'

<h2>'.$school['school_name'].'</h2>

<hr>

<h3>'.$trip['title'].'</h3>

<p>

'.$message.'

</p>

<p>

<strong>Μαθητής:</strong>

'.$parent['student_first_name'].' '.$parent['student_last_name'].'

</p>

<p>

<strong>Ημερομηνία Εκδρομής:</strong>

'.$trip['trip_date'].'

</p>

<hr>

<p>

Το μήνυμα στάλθηκε αυτόματα από το SchoolMedia.

</p>

';

        $mail->send();

    }catch(Exception $e){

    }

}
?>
<div class="row g-2">

<div class="col-md-3">

<a
href="trip_status_email.php?id=<?= $trip['id']; ?>&status=departed"
class="btn btn-primary w-100"
onclick="return confirm('Να σταλεί ενημέρωση σε όλους τους γονείς;')">

🚌 Αναχώρηση

</a>

</div>

<div class="col-md-3">

<a
href="trip_status_email.php?id=<?= $trip['id']; ?>&status=arrived"
class="btn btn-success w-100"
onclick="return confirm('Να σταλεί ενημέρωση σε όλους τους γονείς;')">

📍 Άφιξη

</a>

</div>

<div class="col-md-3">

<a
href="trip_status_email.php?id=<?= $trip['id']; ?>&status=returning"
class="btn btn-warning w-100"
onclick="return confirm('Να σταλεί ενημέρωση σε όλους τους γονείς;')">

↩️ Επιστροφή

</a>

</div>

<div class="col-md-3">

<a
href="trip_status_email.php?id=<?= $trip['id']; ?>&status=finished"
class="btn btn-dark w-100"
onclick="return confirm('Να σταλεί ενημέρωση σε όλους τους γονείς;')">

🏫 Άφιξη Σχολείο

</a>

</div>

</div>
