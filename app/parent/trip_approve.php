<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

require_once '../config/database.php';

$token = trim($_GET['token'] ?? '');

$stmt = $pdo->prepare("
SELECT
ts.*,
t.title,
t.trip_date,
t.description,
s.first_name,
s.last_name
FROM trip_signatures ts
INNER JOIN trips t
ON t.id = ts.trip_id
INNER JOIN students s
ON s.id = ts.student_id
WHERE ts.token=?
LIMIT 1
");

$stmt->execute([$token]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    die('Μη έγκυρος σύνδεσμος.');
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $signature_image =
    $_POST['signature_image'] ?? null;

    $stmt = $pdo->prepare("
    UPDATE trip_signatures
    SET
    status='approved',
    signed_at=NOW(),
    signature_ip=?,
    signature_image=?
    WHERE id=?
    ");

    $stmt->execute([
        $_SERVER['REMOTE_ADDR'],
        $signature_image,
        $row['id']
    ]);

    echo '<h2>Η έγκριση καταχωρήθηκε επιτυχώς.</h2>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="el">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Έγκριση Εκδρομής</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container py-4">

<h2>🚌 <?= htmlspecialchars($row['title']); ?></h2>

<p>
<b>Μαθητής:</b>
<?= htmlspecialchars($row['first_name']); ?>
<?= htmlspecialchars($row['last_name']); ?>
</p>

<p>
<b>Ημερομηνία:</b>
<?= htmlspecialchars($row['trip_date']); ?>
</p>

<p>
<?= nl2br(htmlspecialchars($row['description'])); ?>
</p>

<form method="post">

<div class="mb-3">

<label><b>Υπογραφή Γονέα</b></label>

<canvas
id="signature-pad"
style="
border:1px solid #ccc;
width:100%;
height:200px;
touch-action:none;
"></canvas>

<input
type="hidden"
name="signature_image"
id="signature_image">

</div>

<button
type="button"
class="btn btn-secondary mb-3"
onclick="clearPad()">

Καθαρισμός Υπογραφής

</button>

<br>

<button
type="submit"
class="btn btn-success btn-lg"
onclick="saveSignature(event)">

✅ Εγκρίνω την Εκδρομή

</button>

</form>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>

const canvas =
document.getElementById('signature-pad');

canvas.width = canvas.offsetWidth;
canvas.height = 200;

const signaturePad =
new SignaturePad(canvas);

function clearPad(){

    signaturePad.clear();

}

function saveSignature(event){

    if(signaturePad.isEmpty()){

        alert('Παρακαλώ υπογράψτε πρώτα.');

        event.preventDefault();

        return false;
    }

    document.getElementById('signature_image').value =
    signaturePad.toDataURL();

}

</script>

</div>

</body>
</html>