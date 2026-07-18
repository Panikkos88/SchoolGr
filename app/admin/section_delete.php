<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}

require_once '../includes/common.php';

$id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) ?: 0;

if($id<=0){
    header("Location: classes.php");
    exit;
}

try{

    $pdo->beginTransaction();

    // Διαγραφή συσχετίσεων καθηγητών
    $stmt=$pdo->prepare("
    DELETE FROM teacher_sections
    WHERE section_id=?
    ");
    $stmt->execute([$id]);

    // Διαγραφή ψηφιακών αιθουσών
    $stmt=$pdo->prepare("
    DELETE FROM virtual_classrooms
    WHERE section_id=?
    ");
    $stmt->execute([$id]);

    // Διαγραφή μαθητών του τμήματος
    $stmt=$pdo->prepare("
    DELETE FROM students
    WHERE section_id=?
    ");
    $stmt->execute([$id]);

    // Διαγραφή τμήματος
    $stmt=$pdo->prepare("
    DELETE FROM sections
    WHERE id=?
    ");
    $stmt->execute([$id]);

    $pdo->commit();

}catch(Exception $e){

    $pdo->rollBack();

    die($e->getMessage());

}

header("Location: classes.php");
exit;