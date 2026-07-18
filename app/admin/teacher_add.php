<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$error='';

if($_SERVER['REQUEST_METHOD']=='POST'){

    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $specialty  = trim($_POST['specialty']);
    $phone      = trim($_POST['phone']);
    $email      = trim($_POST['email']);
    $position   = $_POST['position'];

    $check = $pdo->prepare("
    SELECT id
    FROM users
    WHERE email=?
    LIMIT 1
    ");

    $check->execute([$email]);

    if($check->fetch()){

        $error="Το email χρησιμοποιείται ήδη.";

    }else{

        try{

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
            INSERT INTO teachers
            (
                first_name,
                last_name,
                specialty,
                phone,
                email,
                position
            )
            VALUES
            (
                ?,?,?,?,?,?
            )
            ");

            $stmt->execute([
                $first_name,
                $last_name,
                $specialty,
                $phone,
                $email,
                $position
            ]);

            $teacher_id = $pdo->lastInsertId();

            $tempPassword = '123456';

            $user = $pdo->prepare("
            INSERT INTO users
            (
                first_name,
                last_name,
                email,
                password,
                role,
                teacher_id,
                must_change_password
            )
            VALUES
            (
                ?,?,?,?,?,?,1
            )
            ");

            $user->execute([
                $first_name,
                $last_name,
                $email,
                password_hash($tempPassword,PASSWORD_DEFAULT),
                'teacher',
                $teacher_id
            ]);
			            if(!empty($_POST['sections'])){

                foreach($_POST['sections'] as $section_id){

                    $class_stmt = $pdo->prepare("
                    SELECT class_id
                    FROM sections
                    WHERE id=?
                    ");

                    $class_stmt->execute([$section_id]);

                    $class = $class_stmt->fetch(PDO::FETCH_ASSOC);

                    if($class){

                        $insert = $pdo->prepare("
                        INSERT INTO teacher_sections
                        (
                            teacher_id,
                            class_id,
                            section_id
                        )
                        VALUES
                        (
                            ?,?,?
                        )
                        ");

                        $insert->execute([
                            $teacher_id,
                            $class['class_id'],
                            $section_id
                        ]);

                    }

                }

            }

            $pdo->commit();

            header("Location: teachers.php");
            exit;

        }catch(Exception $e){

            $pdo->rollBack();

            $error = $e->getMessage();

        }

    }

}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $LANG['new_teacher']; ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f4f6f9;
}

.form-card{
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<h2 class="mb-4">
👨‍🏫 <?= $LANG['new_teacher']; ?>
</h2>

<div class="form-card">

<?php if(!empty($error)): ?>

<div class="alert alert-danger">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>

<form method="post">

<div class="mb-3">

<label><?= $LANG['first_name']; ?></label>

<input
type="text"
name="first_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label><?= $LANG['last_name']; ?></label>

<input
type="text"
name="last_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label><?= $LANG['teacher_specialty']; ?></label>

<input
type="text"
name="specialty"
class="form-control">

</div>

<div class="mb-3">

<label><?= $LANG['phone']; ?></label>

<input
type="text"
name="phone"
class="form-control">

</div>

<div class="mb-3">

<label><?= $LANG['email']; ?></label>

<input
type="email"
name="email"
class="form-control"
required>

</div>

<div class="mb-3">

<label><?= $LANG['teacher_position']; ?></label>

<select
name="position"
class="form-control">

<option value="kindergarten"><?= $LANG['kindergarten_teacher']; ?></option>

<option value="teacher_primary"><?= $LANG['primary_teacher']; ?></option>

<option value="teacher" selected><?= $LANG['teacher']; ?></option>

<option value="principal"><?= $LANG['principal']; ?></option>

<option value="vice_principal"><?= $LANG['vice_principal']; ?></option>

</select>

</div>

<hr>

<h5>📚 <?= $LANG['teacher_sections']; ?></h5>
	<?php

$sections = $pdo->query("
SELECT
s.id,
c.name AS class_name,
s.name AS section_name
FROM sections s
INNER JOIN classes c
ON c.id=s.class_id
ORDER BY c.name,s.name
");

while($sec = $sections->fetch(PDO::FETCH_ASSOC)):
?>

<div class="form-check mb-2">

<input
class="form-check-input"
type="checkbox"
name="sections[]"
value="<?= $sec['id']; ?>"
id="sec<?= $sec['id']; ?>">

<label
class="form-check-label"
for="sec<?= $sec['id']; ?>">

<?= htmlspecialchars($sec['class_name']); ?>

-

<?= htmlspecialchars($sec['section_name']); ?>

</label>

</div>

<?php endwhile; ?>

<hr>

<div class="d-grid gap-2">

<button
type="submit"
class="btn btn-success">

💾 <?= $LANG['save']; ?>

</button>

<a
href="teachers.php"
class="btn btn-secondary">

⬅️ <?= $LANG['back']; ?>

</a>

</div>

</form>

</div>

</div>

</body>
</html>
