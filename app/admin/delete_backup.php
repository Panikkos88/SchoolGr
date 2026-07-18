<?php

session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit;
}
require_once '../includes/common.php';

$file = basename($_GET['file'] ?? '');

$path = "../backups/".$file;

if(file_exists($path)){
    unlink($path);
}

header("Location: backup_files.php");
exit;