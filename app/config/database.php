<?php

$host = getenv('SCHOOLMEDIA_DB_HOST') ?: '127.0.0.1';
$dbname = getenv('SCHOOLMEDIA_DB_NAME') ?: '';
$username = getenv('SCHOOLMEDIA_DB_USER') ?: '';
$password = getenv('SCHOOLMEDIA_DB_PASSWORD') ?: '';

if ($dbname === '' || $username === '' || $password === '') {
    http_response_code(503);
    exit('Database configuration is unavailable.');
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: '.$e->getCode());
    http_response_code(503);
    exit('Database connection is temporarily unavailable.');
}
