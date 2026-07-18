<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$trip_id=(int)($_GET['trip_id'] ?? 0);
$student_id=(int)($_GET['student_id'] ?? 0);

$stmt=$pdo->prepare("
SELECT
t.*,
s.first_name,
s.last_name
FROM trips t
INNER JOIN students s
ON s.id=?
WHERE t.id=?
LIMIT 1
");

$stmt->execute([
    $student_id,
    $trip_id
]);

$data=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$data){
    die("Δεν βρέθηκαν στοιχεία.");
}

if($_SERVER['REQUEST_METHOD']=='POST'){

    $method=$_POST['payment_method'];
    $notes=trim($_POST['notes']);

    $check=$pdo->prepare("
    SELECT id
    FROM trip_payments
    WHERE trip_id=?
    AND student_id=?
    LIMIT 1
    ");

    $check->execute([
        $trip_id,
        $student_id
    ]);

    if($payment=$check->fetch(PDO::FETCH_ASSOC)){

        $upd=$pdo->prepare("
        UPDATE trip_payments
        SET
        amount=?,
        payment_method=?,
        status='paid',
        paid_at=NOW(),
        notes=?
        WHERE id=?
        ");

        $upd->execute([
            $data['cost'],
            $method,
            $notes,
            $payment['id']
        ]);

    }else{

        $ins=$pdo->prepare("
        INSERT INTO trip_payments
        (
            trip_id,
            student_id,
            amount,
            payment_method,
            status,
            paid_at,
            notes
        )
        VALUES
        (
            ?,?,?,?,'paid',NOW(),?
        )
        ");

        $ins->execute([
            $trip_id,
            $student_id,
            $data['cost'],
            $method,
            $notes
        ]);

    }

    header("Location: trip_payments.php?id=".$trip_id);
    exit;

}

?>
<!DOCTYPE html>
<html lang="el">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<title>Καταχώριση Πληρωμής</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.card-box{
    max-width:700px;
    margin:40px auto;
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="card-box">

<h2>

💳 Καταχώριση Πληρωμής

</h2>

<hr>

<h4>

👨‍🎓

<?= htmlspecialchars($data['last_name'].' '.$data['first_name']); ?>

</h4>

<p>

🚌

<?= htmlspecialchars($data['title']); ?>

</p>

<p>

💰

<strong>

<?= number_format($data['cost'],2); ?> €

</strong>

</p>

<form method="post">

<div class="mb-3">

<label class="form-label">

Τρόπος Πληρωμής

</label>

<select
name="payment_method"
class="form-select"
required>

<option value="cash">

💵 Μετρητά

</option>

<option value="card">

💳 Κάρτα

</option>

<option value="bank">

🏦 Τραπεζική Κατάθεση

</option>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Σημειώσεις (προαιρετικά)

</label>

<textarea
name="notes"
rows="4"
class="form-control"></textarea>

</div>

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success btn-lg">

✅ Καταχώριση Πληρωμής

</button>

<a
href="trip_payments.php?id=<?= $trip_id; ?>"
class="btn btn-secondary">

⬅️ Επιστροφή

</a>

</div>

</form>

</div>

</body>

</html>