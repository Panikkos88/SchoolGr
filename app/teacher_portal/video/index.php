<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']!='teacher'
){
    header("Location: ../../login.php");
    exit;
}

/* ==========================
   Συνδεδεμένος Καθηγητής
========================== */

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$stmt->execute([
    $user_id
]);

$teacher_id = $stmt->fetchColumn();

if(!$teacher_id){
    die("Δεν βρέθηκε καθηγητής.");
}

/* ==========================
   Ψηφιακές Αίθουσες
========================== */

$stmt = $pdo->prepare("
SELECT

vc.*,

c.name AS class_name,

s.name AS section_name

FROM virtual_classrooms vc

LEFT JOIN classes c
ON c.id = vc.class_id

LEFT JOIN sections s
ON s.id = vc.section_id

WHERE vc.teacher_id=?

ORDER BY c.name,s.name
");

$stmt->execute([
    $teacher_id
]);

$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==========================
   Dashboard
========================== */

$total = count($classrooms);

$live = 0;
$closed = 0;

foreach($classrooms as $room){

    if($room['is_live']){

        $live++;

    }else{

        $closed++;

    }

}
?>
<?php

error_reporting(E_ALL);
ini_set('display_errors',1);



require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role']!='teacher'
){
    header("Location: ../../login.php");
    exit;
}

/* ==========================
   Συνδεδεμένος Καθηγητής
========================== */

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$stmt->execute([
    $user_id
]);

$teacher_id = $stmt->fetchColumn();

if(!$teacher_id){
    die("Δεν βρέθηκε καθηγητής.");
}

/* ==========================
   Ψηφιακές Αίθουσες
========================== */

$stmt = $pdo->prepare("
SELECT

vc.*,

c.name AS class_name,

s.name AS section_name

FROM virtual_classrooms vc

LEFT JOIN classes c
ON c.id = vc.class_id

LEFT JOIN sections s
ON s.id = vc.section_id

WHERE vc.teacher_id=?

ORDER BY c.name,s.name
");

$stmt->execute([
    $teacher_id
]);

$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ==========================
   Dashboard
========================== */

$total = count($classrooms);

$live = 0;
$closed = 0;

foreach($classrooms as $room){

    if($room['is_live']){

        $live++;

    }else{

        $closed++;

    }

}
?>
<!doctype html>

<html lang="el">

<head>

<meta charset="utf-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>🎥 Ψηφιακές Αίθουσες</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card-box{
    background:white;
    border-radius:18px;
    padding:20px;
    box-shadow:0 2px 12px rgba(0,0,0,.08);
    margin-bottom:20px;
}

.stat-card{
    border-radius:18px;
    padding:20px;
    text-align:center;
    color:white;
    font-weight:bold;
}

.stat-card h2{
    margin:0;
    font-size:34px;
}

.table{
    background:white;
    border-radius:15px;
    overflow:hidden;
}

.btn{
    border-radius:10px;
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">

🎥 Ψηφιακές Αίθουσες

</h2>

<div class="row mb-4">

<div class="col-md-4 mb-3">

<div class="stat-card bg-primary">

<h2><?= $total; ?></h2>

Σύνολο Αιθουσών

</div>

</div>

<div class="col-md-4 mb-3">

<div class="stat-card bg-success">

<h2><?= $live; ?></h2>

🟢 Ενεργές

</div>

</div>

<div class="col-md-4 mb-3">

<div class="stat-card bg-secondary">

<h2><?= $closed; ?></h2>

🔴 Κλειστές

</div>

</div>

</div>

<a
href="create.php"
class="btn btn-success mb-4">

➕ Νέο εξ αποστάσεως μάθημα

</a>
	<?php if(count($classrooms)==0){ ?>

<div class="alert alert-info">

Δεν υπάρχουν ακόμη ψηφιακές αίθουσες.

</div>

<?php }else{ ?>

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>🏫 Τάξη</th>

<th>📚 Τμήμα</th>

<th>📅 Δημιουργήθηκε</th>

<th>📡 Κατάσταση</th>

<th width="220">Ενέργειες</th>

</tr>

</thead>

<tbody>

<?php foreach($classrooms as $room){ ?>

<tr>

<td>

<?= htmlspecialchars($room['class_name']); ?>

</td>

<td>

<?= htmlspecialchars($room['section_name']); ?>

</td>

<td>

<?php

if(!empty($room['created_at'])){

    echo date(
        'd/m/Y H:i',
        strtotime($room['created_at'])
    );

}else{

    echo '-';

}

?>

</td>

<td>

<?php if($room['is_live']){ ?>

<span class="badge bg-success">

🟢 Ζωντανά

</span>

<?php }else{ ?>

<span class="badge bg-secondary">

🔴 Κλειστή

</span>

<?php } ?>

</td>

<td>
	<a
href="meeting.php?id=<?= $room['id']; ?>"
class="btn btn-primary btn-sm mb-1">

🎥 Άνοιγμα

</a>

<a
href="delete.php?id=<?= $room['id']; ?>"
class="btn btn-danger btn-sm mb-1"
onclick="return confirm('Θέλετε να διαγράψετε την ψηφιακή αίθουσα;');">

🗑️ Διαγραφή

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<?php } ?>
	<br>

<a
href="../index.php"
class="btn btn-secondary">

⬅️ Επιστροφή στο Portal

</a>

</div>

</body>

</html>