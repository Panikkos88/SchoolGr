<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once '../../config/database.php';

if(
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] != 'teacher'
){
    header("Location: ../../login.php");
    exit;
}

$classrooms = $pdo->query("
SELECT
    vc.*,
    c.name AS class_name,
    s.name AS section_name
FROM virtual_classrooms vc
LEFT JOIN classes c
ON c.id = vc.class_id
LEFT JOIN sections s
ON s.id = vc.section_id
ORDER BY c.name, s.name
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="el">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Ψηφιακές Αίθουσες</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-4">

<h2>🎥 Ψηφιακές Αίθουσες</h2>

<a href="create.php" class="btn btn-success mb-4">
➕ Νέα Βιντεοκλήση
</a>

<?php if(count($classrooms)==0){ ?>

<div class="alert alert-info">

Δεν υπάρχουν ακόμα ψηφιακές αίθουσες.

</div>

<?php }else{ ?>

<table class="table table-bordered table-striped">

<thead>

<tr>

<th>Τάξη</th>

<th>Τμήμα</th>

<th>Κατάσταση</th>

<th width="180">Ενέργεια</th>

</tr>

</thead>

<tbody>

<?php foreach($classrooms as $room){ ?>

<tr>

<td>

<?= htmlspecialchars($room['class_name']) ?>

</td>

<td>

<?= htmlspecialchars($room['section_name']) ?>

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
href="meeting.php?id=<?= $room['id'] ?>"
class="btn btn-primary btn-sm">

🎥 Είσοδος

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<?php } ?>

<br>

<a href="../index.php" class="btn btn-secondary">

⬅ Επιστροφή στο Portal

</a>

</div>

</body>

</html>