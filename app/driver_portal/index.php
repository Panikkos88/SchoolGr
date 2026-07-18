<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';
require_once '../includes/update_check.php';
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$bus_id = 1;
$success_message = '';

/*
|--------------------------------------------------------------------------
| ΕΝΑΡΞΗ ΔΙΑΔΡΟΜΗΣ
|--------------------------------------------------------------------------
*/

if(isset($_POST['start_trip'])){

    $check = $pdo->prepare("
    SELECT last_trip_date
    FROM buses
    WHERE id=?
    ");

    $check->execute([$bus_id]);

    $trip = $check->fetch(PDO::FETCH_ASSOC);

    if(
        empty($trip['last_trip_date']) ||
        $trip['last_trip_date'] != date('Y-m-d')
    ){

        $reset = $pdo->prepare("
        UPDATE student_bus_status
        SET
        picked_up = 0,
        pickup_time = NULL,
        arrived_school = 0,
        arrived_school_time = NULL,
        delivered_home = 0,
        delivered_home_time = NULL
        WHERE bus_id=?
        ");

        $reset->execute([$bus_id]);

        $save_date = $pdo->prepare("
        UPDATE buses
        SET last_trip_date = CURDATE()
        WHERE id=?
        ");

        $save_date->execute([$bus_id]);
    }

    $start = $pdo->prepare("
    UPDATE buses
    SET trip_active=1
    WHERE id=?
    ");

    $start->execute([$bus_id]);

    header("Location: ./index.php");
exit;
}

/*
|--------------------------------------------------------------------------
| ΛΗΞΗ ΔΙΑΔΡΟΜΗΣ
|--------------------------------------------------------------------------
*/

if(isset($_POST['stop_trip'])){

    $stop = $pdo->prepare("
    UPDATE buses
    SET trip_active=0
    WHERE id=?
    ");

    $stop->execute([$bus_id]);

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| ΕΦΤΑΣΑΝ ΣΤΟ ΣΧΟΛΕΙΟ
|--------------------------------------------------------------------------
*/

if(isset($_POST['arrived_all'])){

    $arrived = $pdo->prepare("
    UPDATE student_bus_status
    SET
    arrived_school=1,
    arrived_school_time=NOW()
    WHERE bus_id=?
    AND picked_up=1
    ");

    $arrived->execute([$bus_id]);

    $settings = [];

    $q = $pdo->query("
    SELECT setting_key, setting_value
    FROM settings
    ");

    while($row = $q->fetch(PDO::FETCH_ASSOC)){
        $settings[$row['setting_key']] =
        $row['setting_value'];
    }

    $parents = $pdo->prepare("
    SELECT
    s.first_name,
    s.last_name,
    p.email
    FROM student_bus_status bs
    INNER JOIN students s
        ON s.id = bs.student_id
    INNER JOIN parent_students ps
        ON ps.student_id = s.id
    INNER JOIN parents p
        ON p.id = ps.parent_id
    WHERE bs.bus_id=?
    AND bs.picked_up=1
    ");

    $parents->execute([$bus_id]);
	while($parent = $parents->fetch(PDO::FETCH_ASSOC)){

    if(empty($parent['email'])){
        continue;
    }

    try{

        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = $settings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $settings['smtp_username'];
        $mail->Password = $settings['smtp_password'];
        $mail->Port = $settings['smtp_port'];

        if($settings['smtp_encryption']=='ssl'){
            $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_SMTPS;
        }else{
            $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            $settings['mail_from'],
            'SchoolMedia'
        );

        $mail->addAddress(
            $parent['email']
        );

        $mail->isHTML(true);

        $mail->Subject =
        'Άφιξη μαθητή στο σχολείο';

        $mail->Body = '

        <h2>🏫 Άφιξη στο σχολείο</h2>

        <p>
        Ο μαθητής
        <strong>'.
        htmlspecialchars(
            $parent['first_name'].' '.$parent['last_name']
        ).
        '</strong>
        έφτασε με ασφάλεια στο σχολείο.
        </p>

        <p>
        '.date('d/m/Y H:i').'
        </p>

        <hr>

        <p>SchoolMedia</p>
        ';

        $mail->send();

    }catch(Exception $e){

    }
}

$trip_stop = $pdo->prepare("
UPDATE buses
SET trip_active=0
WHERE id=?
");

$trip_stop->execute([$bus_id]);

$success_message =
'Οι μαθητές έφτασαν στο σχολείο.';
}

/*
|--------------------------------------------------------------------------
| ΣΤΟΙΧΕΙΑ ΛΕΩΦΟΡΕΙΟΥ
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
SELECT *
FROM buses
WHERE id=?
");

$stmt->execute([$bus_id]);

$bus = $stmt->fetch(PDO::FETCH_ASSOC);

$students = $pdo->prepare("
SELECT *
FROM students
WHERE bus_id=?
ORDER BY last_name
");

$students->execute([$bus_id]);

?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Πίνακας Οδηγού</title>

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

.student{
padding:12px;
border:1px solid #ddd;
border-radius:10px;
margin-bottom:10px;
background:white;
}

.action-btn{
height:80px;
font-size:24px;
font-weight:bold;
border-radius:15px;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="card-box">

<h2>
🚌 <?= htmlspecialchars($bus['name']); ?>
</h2>

<p>
👨 <?= htmlspecialchars($bus['driver_name']); ?>
</p>

<p>
📞 <?= htmlspecialchars($bus['driver_phone']); ?>
</p>

<?php if($bus['trip_active']): ?>

<div class="alert alert-success">
🟢 Η διαδρομή είναι ενεργή
</div>

<?php else: ?>

<div class="alert alert-danger">
🔴 Η διαδρομή είναι ανενεργή
</div>

<?php endif; ?>

<?php if(!empty($success_message)): ?>

<div class="alert alert-success">
<?= $success_message; ?>
</div>

<?php endif; ?>

<form method="post">

<button
name="start_trip"
class="btn btn-success w-100 mb-3 action-btn">

🟢 Έναρξη Διαδρομής

</button>

<button
name="arrived_all"
class="btn btn-primary w-100 mb-3 action-btn">

🏫 Έφτασαν στο Σχολείο

</button>

<button
name="stop_trip"
class="btn btn-danger w-100 action-btn">

🔴 Λήξη Διαδρομής

</button>

</form>

</div>

<div class="card-box">

<h3>👨‍🎓 Μαθητές</h3>
	<?php foreach($students as $student): ?>

<?php

$status = $pdo->prepare("
SELECT *
FROM student_bus_status
WHERE student_id=?
AND bus_id=?
LIMIT 1
");

$status->execute([
    $student['id'],
    $bus_id
]);

$row = $status->fetch(PDO::FETCH_ASSOC);

$bus_absence = $pdo->prepare("
SELECT id
FROM bus_absences
WHERE student_id=?
AND absence_date=CURDATE()
LIMIT 1
");

$bus_absence->execute([
    $student['id']
]);

$absent_today = $bus_absence->fetch(PDO::FETCH_ASSOC);

?>

<div
class="student"
style="
background:
<?= (!empty($row['picked_up'])) ? '#d4edda' : '#ffffff'; ?>;
">

<strong>

<?= htmlspecialchars(
$student['last_name'].' '.$student['first_name']
); ?>

</strong>

<?php if($absent_today): ?>

<div class="alert alert-danger mt-2">

🔴 ΔΕΝ ΘΑ ΕΡΘΕΙ ΣΗΜΕΡΑ ΜΕ ΤΟ ΛΕΩΦΟΡΕΙΟ

</div>

<?php endif; ?>

<div class="alert alert-info mt-2">

📞

<a href="tel:<?= htmlspecialchars($student['phone']); ?>">

<?= htmlspecialchars($student['phone']); ?>

</a>

</div>

<?php if(empty($row['picked_up']) && !$absent_today): ?>

<a
href="pickup_student.php?student_id=<?= $student['id']; ?>"
class="btn btn-success btn-sm">

🟢 Παραλήφθηκε

</a>

<?php else: ?>

<span class="badge bg-success">

✅ Παραλήφθηκε

</span>

<br>

<?= $row['pickup_time'] ?? ''; ?>

<?php if(!empty($row['arrived_school'])): ?>

<br><br>

<span class="badge bg-primary">

🏫 Έφτασε στο Σχολείο

</span>

<br>

<?= $row['arrived_school_time']; ?>

<?php endif; ?>

<?php if(!empty($row['delivered_home'])): ?>

<br><br>

<span class="badge bg-warning text-dark">

🏠 Παραδόθηκε στον Γονέα

</span>

<br>

<?= $row['delivered_home_time']; ?>

<?php endif; ?>

<?php endif; ?>

</div>

<?php endforeach; ?>

</div>

</div>

<script>

function startGPS(){

    if(!navigator.geolocation){
        return;
    }

    navigator.geolocation.watchPosition(

        function(position){

            let formData = new FormData();

            formData.append(
                'lat',
                position.coords.latitude
            );

            formData.append(
                'lng',
                position.coords.longitude
            );

            fetch(
                'update_gps.php',
                {
                    method:'POST',
                    body:formData
                }
            )
            .then(response => response.text())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.log(error);
            });

        },

        function(error){
            console.log(error);
        },

        {
            enableHighAccuracy:true,
            maximumAge:0,
            timeout:10000
        }

    );

}

</script>

<?php if($bus['trip_active'] == 1): ?>

<script>
startGPS();
</script>

<?php endif; ?>

</body>
</html>
