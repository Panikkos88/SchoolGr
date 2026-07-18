<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$student_id = (int)($_GET['student_id'] ?? 0);

if($_SERVER['REQUEST_METHOD']=='POST'){

    $stmt = $pdo->prepare("
    INSERT INTO parent_students
    (
        parent_id,
        student_id
    )
    VALUES
    (
        ?,?
    )
    ");

    $stmt->execute([
        $_POST['parent_id'],
        $student_id
    ]);

    header("Location: student_view.php?id=".$student_id);
    exit;
}

$stmt = $pdo->query("
SELECT *
FROM parents
ORDER BY last_name, first_name
");

$parents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['link_parent']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<div class="container py-4">
<h2>
👨‍👩‍👧 <?= $LANG['link_parent']; ?>
</h2>
<form method="post">

<select
name="parent_id"
class="form-control mb-3"
required>

<?php foreach($parents as $parent): ?>

<option value="<?= $parent['id']; ?>">

<?= htmlspecialchars($parent['first_name']); ?>
<?= htmlspecialchars($parent['last_name']); ?>

(<?= htmlspecialchars($parent['relationship']); ?>)

</option>

<?php endforeach; ?>

</select>

<button
type="submit"
class="btn btn-success w-100">

🔗 <?= $LANG['link']; ?>

</button>

</form>
<br>

<a
href="student_view.php?id=<?= $student_id; ?>"
class="btn btn-secondary w-100">

⬅️ <?= $LANG['back']; ?>

</a>
</div>

</body>
</html>