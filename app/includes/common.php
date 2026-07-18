<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/license_check.php';
require_once __DIR__ . '/update_check.php';
require_once __DIR__ . '/language.php';
require_once __DIR__ . '/ui/icons.php';
require_once __DIR__ . '/ui/colors.php';
require_once __DIR__ . '/ui/components.php';
/*
|--------------------------------------------------------------------------
| School Settings
|--------------------------------------------------------------------------
*/

$school = [];

try {

    $stmt = $pdo->query("SELECT * FROM school_settings LIMIT 1");

    $school = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

} catch (Exception $e) {

    $school = [];

}