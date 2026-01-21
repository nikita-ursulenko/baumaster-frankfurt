<?php
/**
 * Database Upload Script
 * Decodes base64 database and saves to /data/baumaster.db
 */

$base64_data = <<<'EOD'

EOD;

$db_path = '/data/baumaster.db';

// Ensure /data directory exists
if (!is_dir('/data')) {
    mkdir('/data', 0755, true);
}

// Decode and save
$binary_data = base64_decode($base64_data);
file_put_contents($db_path, $binary_data);

echo "Database uploaded successfully to {$db_path}\n";
echo "File size: " . filesize($db_path) . " bytes\n";
