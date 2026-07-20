<?php
/**
 * PDO database connection
 */

require_once __DIR__ . '/config.php';

function getDB(): ?PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Graceful fallback — pages can still render with sample data
        error_log('DB connection failed: ' . $e->getMessage());
        return null;
    }
}

function dbAvailable(): bool
{
    return getDB() !== null;
}
