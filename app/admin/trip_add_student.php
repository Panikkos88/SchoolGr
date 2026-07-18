<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$trip_id = (int)($_GET['trip_id'] ?? 0);

if($_SERVER['REQUEST_METHOD']=='POST'){

    $student_id = (int)$_POST['student_id'];

    $parent_stmt = $pdo->prepare("
    SELECT parent_id
    FROM parent_students
    WHERE student_id=?
    LIMIT 1
    ");

    $parent_stmt->execute([$student_id]);

    $parent = $parent_stmt->fetch(PDO::FETCH_ASSOC);

    if($parent){

        $stmt = $pdo->prepare("
        INSERT INTO trip_signatures
        (
            trip_id,
            student_id,
            parent_id,
            status
        )
        VALUES
        (
            ?,?,?,?
        )
        ");

        $stmt->execute([
            $trip_id,
            $student_id,
            $parent['parent_id'],
            'pending'
        ]);
    }

    header("Location: trip_view.php?id=".$trip_id);
    exit;
}

$stmt = $pdo->query("
SELECT *
FROM students
ORDER BY last_name, first_name
");

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['add_student']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container py-4">

<h2>
👨‍🎓 <?= $LANG['add_student_to_trip']; ?>
</h2>

<form method="post">
<label><?= $LANG['student']; ?></label>
<select
name="student_id"
class="form-control mb-3"
required>

<?php foreach($students as $student): ?>

<option value="<?= $student['id']; ?>">

<?= htmlspecialchars($student['first_name']); ?>
<?= htmlspecialchars($student['last_name']); ?>

</option>

<?php endforeach; ?>

</select>

<button
type="submit"
class="btn btn-success w-100">

➕ <?= $LANG['add']; ?>

</button>

</form>
<br>

<a
href="trip_view.php?id=<?= $trip_id; ?>"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</body>
</html>