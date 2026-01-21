<?php
/**
 * Admin Database Restore Script
 * Restores database from backup
 */

// Prevent direct access without proper path
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

require_once ABSPATH . 'config.php';

header('Content-Type: text/plain');

echo "=== DATABASE RESTORE ===\n\n";

// Check if backup file exists
$backup_file = ABSPATH . 'db_backup.txt';
if (!file_exists($backup_file)) {
    die("ERROR: Backup file not found at {$backup_file}\n");
}

echo "Backup file found\n";
echo "Backup file size: " . filesize($backup_file) . " bytes\n\n";

// Read and decode
echo "Reading backup...\n";
$base64_data = file_get_contents($backup_file);
$binary_data = base64_decode($base64_data);

if ($binary_data === false) {
    die("ERROR: Failed to decode backup\n");
}

echo "Decoded size: " . strlen($binary_data) . " bytes\n\n";

// Write to database
echo "Database path: " . DB_PATH . "\n";
echo "Writing database...\n";

file_put_contents(DB_PATH, $binary_data);

echo "Database written: " . filesize(DB_PATH) . " bytes\n\n";

// Verify
echo "=== VERIFICATION ===\n";
$db = get_database();

$tables = ['services', 'portfolio', 'reviews', 'users'];
foreach ($tables as $table) {
    $records = $db->select($table, []);
    echo "Table '{$table}': " . count($records) . " records\n";
}

echo "\n=== DONE ===\n";
