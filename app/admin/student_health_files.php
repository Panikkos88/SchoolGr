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
LIMIT 1
");

$stmt->execute([$id]);

$student=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Ο μαθητής δεν βρέθηκε.");
}

$stmt=$pdo->prepare("
SELECT *
FROM student_health_files
WHERE student_id=?
ORDER BY uploaded_at DESC
");

$stmt->execute([$id]);

$files=$stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>

<html lang="el">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width,initial-scale=1">

<title>

📎 Ιατρικά Έγγραφα

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
background:#f4f6f9;
}

.card-box{
background:#fff;
padding:25px;
border-radius:20px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
margin-top:20px;
}

.table td{
vertical-align:middle;
}

</style>

</head>

<body>

<div class="container py-4">

<div class="card-box">

<h2>

📎 Ιατρικά Έγγραφα

</h2>

<h4>

<?= htmlspecialchars($student['first_name']) ?>

<?= htmlspecialchars($student['last_name']) ?>

</h4>

<hr>

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

<hr>

<h3>

📁 Αποθηκευμένα Ιατρικά Έγγραφα

</h3>
	<?php if(count($files)==0){ ?>

<div class="alert alert-warning">

Δεν υπάρχουν ακόμη αποθηκευμένα ιατρικά έγγραφα.

</div>

<?php }else{ ?>

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th width="35%">Έγγραφο</th>

<th width="20%">Ημερομηνία Λήξης</th>

<th width="45%">Ενέργειες</th>

</tr>

</thead>

<tbody>

<?php foreach($files as $file){ ?>

<tr>

<td>

<strong>

📄 <?= htmlspecialchars($file['title']) ?>

</strong>

<?php if(!empty($file['description'])){ ?>

<br>

<small class="text-muted">

<?= htmlspecialchars($file['description']) ?>

</small>

<?php } ?>

<br>

<small>

📅 Ανέβηκε:

<?= date('d/m/Y H:i',strtotime($file['uploaded_at'])) ?>

</small>

</td>

<td>

<?php

if(empty($file['expire_date'])){

    echo "-";

}else{

    $expire=strtotime($file['expire_date']);
    $today=strtotime(date('Y-m-d'));

    if($expire<$today){

        echo '<span class="badge bg-danger">Έληξε</span><br>';

    }elseif($expire<=strtotime('+30 days')){

        echo '<span class="badge bg-warning text-dark">Λήγει σύντομα</span><br>';

    }

    echo date('d/m/Y',$expire);

}

?>

</td>

<td>

<a
href="../uploads/health/<?= urlencode($file['file_name']) ?>"
target="_blank"
class="btn btn-primary btn-sm">

👁 Προβολή

</a>

<a
href="../uploads/health/<?= urlencode($file['file_name']) ?>"
download
class="btn btn-success btn-sm">

⬇ Λήψη

</a>

<a
href="health_delete_file.php?id=<?= $file['id'] ?>&student_id=<?= $student['id'] ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Να διαγραφεί το αρχείο;')">

🗑 Διαγραφή

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<?php } ?>
	<hr class="mt-4">

<div class="d-flex justify-content-between">

<a
href="student_view.php?id=<?= $student['id']; ?>#health"
class="btn btn-secondary">

⬅️ Επιστροφή στον Μαθητή

</a>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>