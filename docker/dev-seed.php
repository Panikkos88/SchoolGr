<?php

declare(strict_types=1);

$host = getenv('SCHOOLMEDIA_DB_HOST') ?: '';
$database = getenv('SCHOOLMEDIA_DB_NAME') ?: '';
$username = getenv('SCHOOLMEDIA_DB_USER') ?: '';
$password = getenv('SCHOOLMEDIA_DB_PASSWORD') ?: '';
$adminEmail = getenv('SCHOOLGR_DEV_ADMIN_EMAIL') ?: '';
$adminPassword = getenv('SCHOOLGR_DEV_ADMIN_PASSWORD') ?: '';

if (
    $host === ''
    || $database === ''
    || $username === ''
    || $password === ''
    || $adminEmail === ''
    || strlen($adminPassword) < 12
    || str_starts_with($adminPassword, 'change-me')
) {
    fwrite(
        STDERR,
        "Λείπουν ασφαλείς τοπικές μεταβλητές ή ο admin κωδικός είναι πολύ μικρός.\n"
    );
    exit(1);
}

$pdo = new PDO(
    "mysql:host={$host};dbname={$database};charset=utf8mb4",
    $username,
    $password,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

$pdo->beginTransaction();

try {
    $adminExists = $pdo->prepare(
        "SELECT id FROM users WHERE email = ? LIMIT 1"
    );
    $adminExists->execute([$adminEmail]);

    if (!$adminExists->fetchColumn()) {
        $insertAdmin = $pdo->prepare(
            "INSERT INTO users
                (first_name, last_name, email, password, role, must_change_password)
             VALUES
                (?, ?, ?, ?, 'admin', 0)"
        );
        $insertAdmin->execute([
            'Local',
            'Administrator',
            $adminEmail,
            password_hash($adminPassword, PASSWORD_DEFAULT),
        ]);
    }

    $schoolExists = (int) $pdo
        ->query("SELECT COUNT(*) FROM school_settings")
        ->fetchColumn();

    if ($schoolExists === 0) {
        $insertSchool = $pdo->prepare(
            "INSERT INTO school_settings
                (school_name, school_address, school_email)
             VALUES
                (?, ?, ?)"
        );
        $insertSchool->execute([
            'SchoolGr Local Development',
            'Localhost',
            $adminEmail,
        ]);
    }

    $pdo->commit();
    fwrite(STDOUT, "Το development seed ολοκληρώθηκε.\n");
} catch (Throwable $error) {
    $pdo->rollBack();
    fwrite(STDERR, "Αποτυχία development seed: {$error->getMessage()}\n");
    exit(1);
}
