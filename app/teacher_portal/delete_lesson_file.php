<?php
session_start();

if(!isset($_SESSION['user_id'])){
    exit;
}

require_once '../config/database.php';

$id = (int)($_GET['id'] ?? 0);
$lesson_id = (int)($_GET['lesson_id'] ?? 0);

$stmt = $pdo->prepare("
SELECT *
FROM lesson_files
WHERE id=?
LIMIT 1
");

$stmt->execute([$id]);

$file = $stmt->fetch(PDO::FETCH_ASSOC);

if($file){

    $path =
    '../uploads/lessons/'.
    $file['file_name'];

    if(file_exists($path)){
        unlink($path);
    }

    $delete = $pdo->prepare("
    DELETE FROM lesson_files
    WHERE id=?
    ");

    $delete->execute([$id]);
}

header(
"Location: edit_lesson.php?id=".$lesson_id
);
exit;
?>