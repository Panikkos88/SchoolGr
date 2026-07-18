<?php

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

$lang = 'el';

$stmt = $pdo->prepare("
SELECT setting_value
FROM settings
WHERE setting_key='system_language'
LIMIT 1
");

$stmt->execute();

if($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    if(!empty($row['setting_value'])){
        $lang = strtolower(trim($row['setting_value']));
    }
}

if(!in_array($lang, ['el','en','de'])){
    $lang = 'el';
}

require_once __DIR__ . '/../languages/' . $lang . '.php';