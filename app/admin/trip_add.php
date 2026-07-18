<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$teachers = $pdo->query("
SELECT
id,
first_name,
last_name
FROM teachers
ORDER BY last_name,first_name
")->fetchAll(PDO::FETCH_ASSOC);

$classes = $pdo->query("
SELECT *
FROM classes
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

$sections = $pdo->query("
SELECT *
FROM sections
ORDER BY class_id,name
")->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']=='POST'){

$title=trim($_POST['title']);
$destination=trim($_POST['destination']);
$trip_date=$_POST['trip_date'];

$departure_time=$_POST['departure_time'];
$return_time=$_POST['return_time'];

$meeting_point=trim($_POST['meeting_point']);

$bus_company=trim($_POST['bus_company']);
$bus_number=trim($_POST['bus_number']);

$driver_name=trim($_POST['driver_name']);
$driver_phone=trim($_POST['driver_phone']);

$description=trim($_POST['description']);
$notes=trim($_POST['notes']);

$cost=(float)$_POST['cost'];

$target_type=$_POST['target_type'];

$leader_teacher_id=(int)$_POST['leader_teacher_id'];

if($target_type=='school'){

    $target_id=null;

}elseif($target_type=='class'){

    $target_id=(int)$_POST['class_id'];

}else{

    $target_id=(int)$_POST['section_id'];

}
	$stmt=$pdo->prepare("
INSERT INTO trips
(
    title,
    destination,
    trip_date,
    departure_time,
    return_time,
    meeting_point,
    bus_company,
    bus_number,
    driver_name,
    driver_phone,
    description,
    notes,
    cost,
    target_type,
    target_id,
    leader_teacher_id,
    payment_required,
    payment_cash,
    payment_card,
    payment_bank
)
VALUES
(
    ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
)
");

$stmt->execute([

    $title,
    $destination,
    $trip_date,
    $departure_time,
    $return_time,
    $meeting_point,
    $bus_company,
    $bus_number,
    $driver_name,
    $driver_phone,
    $description,
    $notes,
    $cost,
    $target_type,
    $target_id,
    $leader_teacher_id,

    isset($_POST['payment_required']) ? 1 : 0,
    isset($_POST['payment_cash']) ? 1 : 0,
    isset($_POST['payment_card']) ? 1 : 0,
    isset($_POST['payment_bank']) ? 1 : 0

]);

$trip_id=$pdo->lastInsertId();
	if($target_type=='school'){

    $students_stmt=$pdo->query("
    SELECT id
    FROM students
    ");

}elseif($target_type=='class'){

    $students_stmt=$pdo->prepare("
    SELECT id
    FROM students
    WHERE class_id=?
    ");

    $students_stmt->execute([$target_id]);

}else{

    $students_stmt=$pdo->prepare("
    SELECT id
    FROM students
    WHERE section_id=?
    ");

    $students_stmt->execute([$target_id]);

}

$students=$students_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($students as $student){

    $parent_stmt=$pdo->prepare("
    SELECT parent_id
    FROM parent_students
    WHERE student_id=?
    LIMIT 1
    ");

    $parent_stmt->execute([
        $student['id']
    ]);

    $parent=$parent_stmt->fetch(PDO::FETCH_ASSOC);

    if(!$parent){
        continue;
    }

    $token=bin2hex(random_bytes(16));

    $sig=$pdo->prepare("
    INSERT INTO trip_signatures
    (
        trip_id,
        student_id,
        parent_id,
        status,
        token
    )
    VALUES
    (
        ?,?,?,?,?
    )
    ");

    $sig->execute([

        $trip_id,
        $student['id'],
        $parent['parent_id'],
        'pending',
        $token

    ]);

}

header("Location: trip_view.php?id=".$trip_id);
exit;

}

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title><?= $LANG['new_trip']; ?></title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.form-card{
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    margin-bottom:20px;
}

.card-header{
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">

🚌 <?= $LANG['new_trip']; ?>

</h2>

<div class="form-card">

<form method="post">

<div class="mb-3">

<label class="form-label">

Τίτλος Εκδρομής

</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

📍 Προορισμός

</label>
	<input
type="text"
name="destination"
class="form-control"
required
placeholder="π.χ. Αρχαία Ολυμπία">

</div>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

📅 Ημερομηνία

</label>

<input
type="date"
name="trip_date"
class="form-control"
required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

🕒 Ώρα Αναχώρησης

</label>

<input
type="time"
name="departure_time"
class="form-control">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

🕒 Ώρα Επιστροφής

</label>

<input
type="time"
name="return_time"
class="form-control">

</div>

</div>

<div class="mb-3">

<label class="form-label">

📍 Σημείο Συγκέντρωσης

</label>

<input
type="text"
name="meeting_point"
class="form-control">

</div>
	<div class="card border-info mb-4">

<div class="card-header bg-info text-white">

🚌 Στοιχεία Μεταφοράς

</div>

<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Εταιρεία Λεωφορείου

</label>

<input
type="text"
name="bus_company"
class="form-control"
placeholder="π.χ. KTEL Tours">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Αριθμός Λεωφορείου

</label>

<input
type="text"
name="bus_number"
class="form-control"
placeholder="π.χ. ΚΗΙ-1234">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Ονοματεπώνυμο Οδηγού

</label>

<input
type="text"
name="driver_name"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Τηλέφωνο Οδηγού

</label>

<input
type="text"
name="driver_phone"
class="form-control">

</div>

</div>

</div>

</div>

<div class="mb-3">

<label class="form-label">

💰 Κόστος (€)

</label>

<input
type="number"
step="0.01"
name="cost"
class="form-control"
value="0">

	</div>
	<div class="card border-primary mb-4">

<div class="card-header bg-primary text-white">

💳 Πληρωμή Εκδρομής

</div>

<div class="card-body">

<div class="form-check mb-3">

<input
class="form-check-input"
type="checkbox"
id="payment_required"
name="payment_required"
value="1">

<label
class="form-check-label"
for="payment_required">

Η εκδρομή απαιτεί πληρωμή

</label>

</div>

<div id="payment_methods" style="display:none;">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="payment_cash"
value="1"
checked>

<label class="form-check-label">

💵 Μετρητά στο σχολείο

</label>

</div>

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="payment_card"
value="1">

<label class="form-check-label">

💳 Πληρωμή με κάρτα

</label>

</div>

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="payment_bank"
value="1">

<label class="form-check-label">

🏦 Τραπεζική κατάθεση

</label>

</div>

</div>

</div>

</div>

<div class="mb-3">

<label class="form-label">

📝 Περιγραφή Εκδρομής

</label>

<textarea
name="description"
rows="4"
class="form-control"></textarea>

</div>

<div class="mb-3">

<label class="form-label">

📋 Σημειώσεις για τους Συνοδούς

</label>

<textarea
name="notes"
rows="4"
class="form-control"
placeholder="Οδηγίες, απαραίτητα αντικείμενα, ιδιαίτερες πληροφορίες..."></textarea>

</div>

<div class="mb-3">

<label class="form-label">

👨‍🏫 Αρχηγός Εκδρομής

</label>

<select
name="leader_teacher_id"
class="form-select"
required>

<option value="">

Επιλέξτε εκπαιδευτικό

</option>

<?php foreach($teachers as $teacher): ?>

<option value="<?= $teacher['id']; ?>">

<?= htmlspecialchars($teacher['last_name'].' '.$teacher['first_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>
	<div class="mb-3">

<label class="form-label">

👨‍🎓 Συμμετέχουν

</label>

<select
name="target_type"
id="target_type"
class="form-select">

<option value="school">

🏫 Όλο το σχολείο

</option>

<option value="class">

📚 Συγκεκριμένη Τάξη

</option>

<option value="section">

🏫 Συγκεκριμένο Τμήμα

</option>

</select>

</div>

<div
class="mb-3"
id="class_box"
style="display:none;">

<label class="form-label">

Τάξη

</label>

<select
name="class_id"
id="class_id"
class="form-select">

<option value="">

Επιλέξτε Τάξη

</option>

<?php foreach($classes as $class): ?>

<option value="<?= $class['id']; ?>">

<?= htmlspecialchars($class['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div
class="mb-3"
id="section_box"
style="display:none;">

<label class="form-label">

Τμήμα

</label>

<select
name="section_id"
id="section_id"
class="form-select">

<option value="">

Επιλέξτε Τμήμα

</option>

<?php foreach($sections as $section): ?>

<option
value="<?= $section['id']; ?>"
data-class="<?= $section['class_id']; ?>">

<?= htmlspecialchars($section['name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<div class="d-grid gap-2 mt-4">

<button
type="submit"
class="btn btn-success btn-lg">

💾 Αποθήκευση Εκδρομής

</button>

<a
href="trips.php"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</form>

</div>
	<script>

const targetType=document.getElementById('target_type');
const classBox=document.getElementById('class_box');
const sectionBox=document.getElementById('section_box');
const classSelect=document.getElementById('class_id');
const sectionSelect=document.getElementById('section_id');

const allSections=[];

<?php foreach($sections as $section): ?>

allSections.push({

id:"<?= $section['id']; ?>",

class_id:"<?= $section['class_id']; ?>",

name:"<?= htmlspecialchars($section['name'],ENT_QUOTES); ?>"

});

<?php endforeach; ?>

function loadSections(){

sectionSelect.innerHTML='';

let first=document.createElement('option');

first.value='';

first.text='Επιλέξτε Τμήμα';

sectionSelect.appendChild(first);

allSections.forEach(function(section){

if(section.class_id==classSelect.value){

let opt=document.createElement('option');

opt.value=section.id;

opt.text=section.name;

sectionSelect.appendChild(opt);

}

});

}

function changeTarget(){

classBox.style.display='none';
sectionBox.style.display='none';

if(targetType.value=='class'){

classBox.style.display='block';

}

if(targetType.value=='section'){

classBox.style.display='block';
sectionBox.style.display='block';

loadSections();

}

}

classSelect.addEventListener('change',function(){

if(targetType.value=='section'){

loadSections();

}

});

targetType.addEventListener('change',changeTarget);

changeTarget();
changeTarget();
changePayment();
const paymentRequired=document.getElementById('payment_required');
const paymentMethods=document.getElementById('payment_methods');

function changePayment(){

if(paymentRequired.checked){

paymentMethods.style.display='block';

}else{

paymentMethods.style.display='none';

}

}

paymentRequired.addEventListener('change',changePayment);

changePayment();

</script>

</body>

	</html>