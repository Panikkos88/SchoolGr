<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
session_start();

if(!isset($_SESSION['user_id'])){
    die($LANG['access_denied']);
}

require_once '../includes/common.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM trips
WHERE id=?
");
$stmt->execute([$id]);

$trip = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$trip){
die($LANG['trip_not_found']);
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

$parents = $pdo->prepare("
SELECT
p.email,
s.first_name,
s.last_name,
ts.token
FROM trip_signatures ts
INNER JOIN parents p ON p.id = ts.parent_id
INNER JOIN students s ON s.id = ts.student_id
WHERE ts.trip_id=?
AND ts.status='pending'
AND p.email IS NOT NULL
AND p.email<>''
AND ts.token IS NOT NULL
AND ts.token<>''
");

$parents->execute([$id]);

while($parent = $parents->fetch(PDO::FETCH_ASSOC)){


    $approve_link =
    "https://2dmegarwn.eu/schoolmedia/parent/trip_approve.php?token=".$parent['token'];
$reject_link =
"https://2dmegarwn.eu/schoolmedia/parent/trip_reject.php?token=".$parent['token'];
    $mail = new PHPMailer(true);

    try{

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
    $settings['mail_from_name'] ?? 'SchoolMedia'
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

$mail->Subject =
'🔔 Υπενθύμιση Έγκρισης Εκδρομής - '.$trip['title'];

$mail->Body = '

<div style="text-align:center">

'.(!empty($logo)
? '<img src="'.$logo.'" style="max-height:120px;"><br><br>'
: '').'

<h2>'.$school['school_name'].'</h2>

<p>
Ημερομηνία: '.date("d/m/Y").'
</p>

</div>

<hr>

<h3>🚌 '.$trip['title'].'</h3>
<p style="color:#d97706;font-size:18px;font-weight:bold;">
🔔 Υπενθύμιση: Δεν έχουμε λάβει ακόμη την απάντησή σας για τη συμμετοχή του παιδιού σας στην εκδρομή.
</p>
<p>
<b>Μαθητής:</b>
'.$parent['first_name'].' '.$parent['last_name'].'
</p>

<p>
<b>Ημερομηνία Εκδρομής:</b>
'.$trip['trip_date'].'
</p>

<p>
<b>Περιγραφή:</b><br>
'.$trip['description'].'
</p>

<br>

<div style="text-align:center">

<a href="'.$approve_link.'"
style="
background:#28a745;
color:white;
padding:12px 20px;
text-decoration:none;
border-radius:5px;
display:inline-block;
margin-right:10px;
">

✅ Εγκρίνω

</a>

<a href="'.$reject_link.'"
style="
background:#dc3545;
color:white;
padding:12px 20px;
text-decoration:none;
border-radius:5px;
display:inline-block;
">

❌ Δεν Εγκρίνω

</a>

</div>

<br><br>

<p>
Έγκριση:
<br>
'.$approve_link.'
</p>

<p>
Απόρριψη:
<br>
'.$reject_link.'
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

header("Location: trip_view.php?id=".$id."&reminders_sent=1");
exit;
