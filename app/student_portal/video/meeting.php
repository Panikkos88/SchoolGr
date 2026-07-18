<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']!='student'
){
    header("Location: ../../login.php");
    exit;
}

$id=(int)$_GET['id'];

$stmt=$pdo->prepare("
SELECT
vc.*,
t.first_name,
t.last_name
FROM virtual_classrooms vc
INNER JOIN teachers t
ON t.id=vc.teacher_id
WHERE vc.id=?
LIMIT 1
");

$stmt->execute([$id]);

$room=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$room){

die("Η αίθουσα δεν βρέθηκε.");

}

if(!$room['is_live']){

die("Ο καθηγητής δεν έχει ξεκινήσει ακόμη το μάθημα.");

}

$roomName=$room['room_code'];

?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Βιντεοκλήση</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<script src="https://meet.jit.si/external_api.js"></script>

<style>

body{
background:#f4f6f9;
}

#jitsi{
width:100%;
height:80vh;
border-radius:15px;
overflow:hidden;
}

</style>

</head>

<body>

<div class="container-fluid py-3">

<div class="d-flex justify-content-between align-items-center mb-3">

<h3>

🎥
<?= htmlspecialchars($room['room_name']) ?>

</h3>

<a
href="index.php"
class="btn btn-secondary">

⬅ Επιστροφή

</a>

</div>

<div id="jitsi"></div>

<script>

const domain="meet.jit.si";

const options={

roomName:"<?= $roomName ?>",

width:"100%",

height:"100%",

parentNode:document.querySelector("#jitsi"),

userInfo:{

displayName:"Μαθητής"

}

};

const api=new JitsiMeetExternalAPI(domain,options);

</script>

</div>

</body>

</html>