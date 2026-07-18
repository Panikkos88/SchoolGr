<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);

$user_stmt = $pdo->prepare("
SELECT teacher_id
FROM users
WHERE id=?
LIMIT 1
");

$user_stmt->execute([
    $_SESSION['user_id']
]);

$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$teacher_id = $user['teacher_id'];

$lesson_stmt = $pdo->prepare("
SELECT *
FROM daily_lessons
WHERE id=?
AND teacher_id=?
LIMIT 1
");

$lesson_stmt->execute([
    $id,
    $teacher_id
]);

$lesson = $lesson_stmt->fetch(PDO::FETCH_ASSOC);

if(!$lesson){
    die('Το μάθημα δεν βρέθηκε');
}

$files = $pdo->prepare("
SELECT *
FROM lesson_files
WHERE lesson_id=?
");

$files->execute([$id]);

while($file = $files->fetch(PDO::FETCH_ASSOC)){

    $filepath =
    '../uploads/lessons/'.
    $file['file_name'];

    if(file_exists($filepath)){
        unlink($filepath);
    }
}

$delete_files = $pdo->prepare("
DELETE FROM lesson_files
WHERE lesson_id=?
");

$delete_files->execute([$id]);

$delete_lesson = $pdo->prepare("
DELETE FROM daily_lessons
WHERE id=?
");

$delete_lesson->execute([$id]);

header("Location: my_lessons.php");
exit;
?>