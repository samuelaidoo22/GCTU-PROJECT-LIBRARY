<?php
/**
 * GCTU Online Project Library System
 * Database Connection with Mock Fallback
 */

$host = '127.0.0.1';
$dbname = 'gctu_library';
$username = 'root';
$password = '';

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
