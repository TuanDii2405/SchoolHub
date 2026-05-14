<?php
declare(strict_types=1);

function createDatabaseConnection(): PDO
{
    $host = '127.0.0.1';
    $port = 3306;
    $username = 'root';
    $password = '';

    // Prefer school_exam_db and validate schema by requiring the User table.
    $candidateDatabases = ['school_exam_db', 'finaldatabase'];

    $baseDsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $basePdo = new PDO($baseDsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $selectedDb = null;
    foreach ($candidateDatabases as $dbName) {
        $stmt = $basePdo->prepare('SHOW DATABASES LIKE :db');
        $stmt->execute(['db' => $dbName]);
        if (!$stmt->fetchColumn()) {
            continue;
        }

        // Only accept databases that contain the expected authentication table.
        $tableStmt = $basePdo->prepare(
            'SELECT COUNT(*)
             FROM information_schema.tables
             WHERE table_schema = :db AND table_name = :table'
        );
        $tableStmt->execute(['db' => $dbName, 'table' => 'User']);

        if ((int)$tableStmt->fetchColumn() > 0) {
            $selectedDb = $dbName;
            break;
        }
    }

    if ($selectedDb === null) {
        throw new RuntimeException('Không tìm thấy CSDL hợp lệ (thiếu bảng User).');
    }

    $dsn = "mysql:host={$host};port={$port};dbname={$selectedDb};charset=utf8mb4";

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
