<?php
/**
 * GCTU Online Project Library System
 * Database Connection with Mock Fallback
 */

$env = file_exists(__DIR__ . '/env.php') ? require __DIR__ . '/env.php' : [];
$host = $env['DB_HOST'] ?? '127.0.0.1';
$dbname = $env['DB_NAME'] ?? 'gctu_library';
$username = $env['DB_USER'] ?? 'root';
$password = $env['DB_PASS'] ?? '';

$is_mock = false;

try {
    // Attempt real connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Fallback to Mock Data if connection fails
    // error_log("Database connection failed: " . $e->getMessage());
    $is_mock = true;
    require_once 'mock_db.php';
    $pdo = new MockPDO();
}

/**
 * MOCK DATABASE LAYER
 * This allows the project to run without a MySQL driver/server for demonstration purposes.
 */
?>
