<?php
// Simple test for German page without TranslationManager
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../ux/layout.php';
require_once __DIR__ . '/../ux/components.php';
require_once __DIR__ . '/../ux/data.php';

// Установка языка
define('CURRENT_LANG', 'de');

echo "German page test - no TranslationManager\n";
echo "Current language: " . CURRENT_LANG . "\n";

// Test basic data functions
try {
    $services = get_services_data();
    echo "Services loaded: " . count($services) . " items\n";
} catch (Exception $e) {
    echo "Error loading services: " . $e->getMessage() . "\n";
}

try {
    $portfolio = get_portfolio_data();
    echo "Portfolio loaded: " . count($portfolio) . " items\n";
} catch (Exception $e) {
    echo "Error loading portfolio: " . $e->getMessage() . "\n";
}

try {
    $reviews = get_reviews_data();
    echo "Reviews loaded: " . count($reviews) . " items\n";
} catch (Exception $e) {
    echo "Error loading reviews: " . $e->getMessage() . "\n";
}

echo "Test completed successfully\n";
?>
