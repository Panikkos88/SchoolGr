<?php

session_start();

if(!isset($_SESSION['user_id'])){
    exit;
}
if(!is_dir('../backups')){
    mkdir('../backups', 0777, true);
}
require_once '../includes/common.php';

$filename =
'../backups/backup_'.
date('Y-m-d_H-i-s').
'.sql';

$file = fopen($filename,'w');

$tables =
$pdo->query("SHOW TABLES")
->fetchAll(PDO::FETCH_COLUMN);

foreach($tables as $table){

    $create =
    $pdo->query("SHOW CREATE TABLE `$table`")
    ->fetch(PDO::FETCH_ASSOC);

    fwrite(
        $file,
        $create['Create Table'].";\n\n"
    );

    $rows =
    $pdo->query("SELECT * FROM `$table`")
    ->fetchAll(PDO::FETCH_ASSOC);

    foreach($rows as $row){

        $values = array_map(
        function($v) use($pdo){

            if($v===null){
                return "NULL";
            }

            return $pdo->quote($v);

        },array_values($row));

        fwrite(
            $file,
            "INSERT INTO `$table` VALUES(".
            implode(',',$values).
            ");\n"
        );
    }

    fwrite($file,"\n\n");
}

fclose($file);

header("Location: backup_files.php");
exit;