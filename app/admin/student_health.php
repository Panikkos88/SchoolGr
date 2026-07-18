<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);

session_start();

require_once '../includes/common.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

$id=(int)($_GET['id'] ?? 0);

$stmt=$pdo->prepare("
SELECT *
FROM students
WHERE id=?
");

$stmt->execute([$id]);

$student=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Ο μαθητής δεν βρέθηκε.");
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $check=$pdo->prepare("
    SELECT id
    FROM student_health_cards
    WHERE student_id=?
    ");

    $check->execute([$id]);

    if($check->fetch()){

        $stmt=$pdo->prepare("
        UPDATE student_health_cards
        SET

        blood_group=?,
        medical_conditions=?,
        allergies=?,
        medications=?,
        doctor_restrictions=?,
        doctor_name=?,
        doctor_specialty=?,
        doctor_phone=?,
        emergency_contact_name=?,
        emergency_contact_relationship=?,
        emergency_contact_phone1=?,
        emergency_contact_phone2=?,
        insurance_company=?,
        insurance_number=?,
        notes=?

        WHERE student_id=?
        ");

        $stmt->execute([

            $_POST['blood_group'],
            $_POST['medical_conditions'],
            $_POST['allergies'],
            $_POST['medications'],
            $_POST['doctor_restrictions'],
            $_POST['doctor_name'],
            $_POST['doctor_specialty'],
            $_POST['doctor_phone'],
            $_POST['emergency_contact_name'],
            $_POST['emergency_contact_relationship'],
            $_POST['emergency_contact_phone1'],
            $_POST['emergency_contact_phone2'],
            $_POST['insurance_company'],
            $_POST['insurance_number'],
            $_POST['notes'],
            $id

        ]);

    }else{

        $stmt=$pdo->prepare("
        INSERT INTO student_health_cards
        (

        student_id,
        blood_group,
        medical_conditions,
        allergies,
        medications,
        doctor_restrictions,
        doctor_name,
        doctor_specialty,
        doctor_phone,
        emergency_contact_name,
        emergency_contact_relationship,
        emergency_contact_phone1,
        emergency_contact_phone2,
        insurance_company,
        insurance_number,
        notes

        )

        VALUES

        (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)

        ");

        $stmt->execute([

            $id,
            $_POST['blood_group'],
            $_POST['medical_conditions'],
            $_POST['allergies'],
            $_POST['medications'],
            $_POST['doctor_restrictions'],
            $_POST['doctor_name'],
            $_POST['doctor_specialty'],
            $_POST['doctor_phone'],
            $_POST['emergency_contact_name'],
            $_POST['emergency_contact_relationship'],
            $_POST['emergency_contact_phone1'],
            $_POST['emergency_contact_phone2'],
            $_POST['insurance_company'],
            $_POST['insurance_number'],
            $_POST['notes']

        ]);

    }

           header("Location: student_view.php?id=".$id."#health");
           exit;
}

$stmt=$pdo->prepare("
SELECT *
FROM student_health_cards
WHERE student_id=?
");

$stmt->execute([$id]);

$health=$stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>❤️ Κάρτα Υγείας</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:white;
padding:25px;
border-radius:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-top:20px;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="card-box">

<h2 class="mb-4">

❤️ Κάρτα Υγείας

</h2>

<h4>

<?= htmlspecialchars($student['first_name']); ?>

<?= htmlspecialchars($student['last_name']); ?>

</h4>

<hr>
<form method="post">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Ομάδα Αίματος</label>

<select
name="blood_group"
class="form-select">

<option value="">-- Επιλέξτε --</option>

<option value="A+" <?= (($health['blood_group'] ?? '')=='A+')?'selected':''; ?>>A+</option>
<option value="A-" <?= (($health['blood_group'] ?? '')=='A-')?'selected':''; ?>>A-</option>

<option value="B+" <?= (($health['blood_group'] ?? '')=='B+')?'selected':''; ?>>B+</option>
<option value="B-" <?= (($health['blood_group'] ?? '')=='B-')?'selected':''; ?>>B-</option>

<option value="AB+" <?= (($health['blood_group'] ?? '')=='AB+')?'selected':''; ?>>AB+</option>
<option value="AB-" <?= (($health['blood_group'] ?? '')=='AB-')?'selected':''; ?>>AB-</option>

<option value="O+" <?= (($health['blood_group'] ?? '')=='O+')?'selected':''; ?>>O+</option>
<option value="O-" <?= (($health['blood_group'] ?? '')=='O-')?'selected':''; ?>>O-</option>

</select>

</div>

<div class="col-12 mb-3">

<label class="form-label">Παθήσεις</label>

<textarea
name="medical_conditions"
class="form-control"
rows="3"><?= htmlspecialchars($health['medical_conditions'] ?? '') ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Αλλεργίες</label>

<textarea
name="allergies"
class="form-control"
rows="3"><?= htmlspecialchars($health['allergies'] ?? '') ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Φάρμακα</label>

<textarea
name="medications"
class="form-control"
rows="3"><?= htmlspecialchars($health['medications'] ?? '') ?></textarea>

</div>

<div class="col-12 mb-3">

<label class="form-label">Περιορισμοί από τον Ιατρό</label>

<textarea
name="doctor_restrictions"
class="form-control"
rows="3"><?= htmlspecialchars($health['doctor_restrictions'] ?? '') ?></textarea>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Θεράπων Ιατρός</label>

<input
type="text"
name="doctor_name"
class="form-control"
value="<?= htmlspecialchars($health['doctor_name'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Ειδικότητα</label>

<input
type="text"
name="doctor_specialty"
class="form-control"
value="<?= htmlspecialchars($health['doctor_specialty'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Τηλέφωνο Ιατρού</label>

<input
type="text"
name="doctor_phone"
class="form-control"
value="<?= htmlspecialchars($health['doctor_phone'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Ασφαλιστική Εταιρεία</label>

<input
type="text"
name="insurance_company"
class="form-control"
value="<?= htmlspecialchars($health['insurance_company'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Αριθμός Ασφαλιστηρίου</label>

<input
type="text"
name="insurance_number"
class="form-control"
value="<?= htmlspecialchars($health['insurance_number'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Όνομα Επαφής Έκτακτης Ανάγκης</label>

<input
type="text"
name="emergency_contact_name"
class="form-control"
value="<?= htmlspecialchars($health['emergency_contact_name'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Σχέση με τον μαθητή</label>

<input
type="text"
name="emergency_contact_relationship"
class="form-control"
value="<?= htmlspecialchars($health['emergency_contact_relationship'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Τηλέφωνο 1</label>

<input
type="text"
name="emergency_contact_phone1"
class="form-control"
value="<?= htmlspecialchars($health['emergency_contact_phone1'] ?? '') ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Τηλέφωνο 2</label>

<input
type="text"
name="emergency_contact_phone2"
class="form-control"
value="<?= htmlspecialchars($health['emergency_contact_phone2'] ?? '') ?>">

</div>

<div class="col-12 mb-3">

<label class="form-label">Παρατηρήσεις</label>

<textarea
name="notes"
class="form-control"
rows="5"><?= htmlspecialchars($health['notes'] ?? '') ?></textarea>

</div>

</div>
<div class="d-grid gap-2 mt-3">

<button
type="submit"
class="btn btn-success btn-lg">

💾 Αποθήκευση Κάρτας Υγείας

</button>

<a
href="student_view.php?id=<?= $student['id']; ?>"
class="btn btn-secondary">

⬅️ Επιστροφή στον Μαθητή

</a>

</div>
	</form>
<hr class="mt-5">

<h3>📎 Ιατρικά Έγγραφα</h3>

<form
action="health_upload.php"
method="post"
enctype="multipart/form-data">

<input
type="hidden"
name="student_id"
value="<?= $student['id']; ?>">

<div class="mb-3">

<label class="form-label">

Τίτλος Εγγράφου

</label>

<input
type="text"
name="title"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">

Περιγραφή

</label>

<textarea
name="description"
class="form-control"
rows="3"></textarea>

</div>

<div class="mb-3">

<label class="form-label">

Ημερομηνία Λήξης

</label>

<input
type="date"
name="expire_date"
class="form-control">

</div>

<div class="mb-3">

<label class="form-label">

Επιλογή Αρχείου

</label>

<input
type="file"
name="health_file"
class="form-control"
required>

</div>

<button
type="submit"
class="btn btn-primary">

📤 Ανέβασμα Εγγράφου

</button>

</form>
	<hr class="mt-5">

<h3>📁 Αποθηκευμένα Ιατρικά Έγγραφα</h3>

<?php

$stmt=$pdo->prepare("
SELECT *
FROM student_health_files
WHERE student_id=?
ORDER BY uploaded_at DESC
");

$stmt->execute([$student['id']]);

$files=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if(count($files)==0){ ?>

<div class="alert alert-warning">

Δεν υπάρχουν ακόμη αρχεία.

</div>

<?php }else{ ?>

<table class="table table-hover align-middle">

<thead>

<tr>

<th>Έγγραφο</th>

<th>Ημερομηνία Λήξης</th>

<th width="250">Ενέργειες</th>

</tr>

</thead>

<tbody>

<?php foreach($files as $file){ ?>

<tr>

<td>

📄 <strong><?= htmlspecialchars($file['title']) ?></strong>

<?php if(!empty($file['description'])){ ?>

<br>

<small class="text-muted">

<?= htmlspecialchars($file['description']) ?>

</small>

<?php } ?>

</td>

<td>

<?= $file['expire_date'] ?: '-' ?>

</td>

<td>

<a
href="../uploads/health/<?= urlencode($file['file_name']) ?>"
target="_blank"
class="btn btn-sm btn-primary">

👁 Προβολή

</a>

<a
href="../uploads/health/<?= urlencode($file['file_name']) ?>"
download
class="btn btn-sm btn-success">

⬇ Λήψη

</a>

<a
href="health_delete_file.php?id=<?= $file['id'] ?>&student_id=<?= $student['id'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('Να διαγραφεί το αρχείο;')">

🗑 Διαγραφή

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<?php } ?>

</div>

</div>

</body>

</html>