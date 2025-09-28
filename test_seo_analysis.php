<?php
/**
 * Тест SEO анализа
 */

require_once 'seo/advanced_seo_analyzer.php';

echo "=== Тест SEO анализатора ===\n\n";

$test_urls = [
    'http://5.61.34.176/',
    'http://5.61.34.176/services.php',
    'http://5.61.34.176/de/'
];

foreach ($test_urls as $url) {
    echo "Анализ: $url\n";
    echo str_repeat("-", 50) . "\n";
    
    $analysis = analyze_page_seo($url);
    
    echo "Общий балл: {$analysis['score']}/{$analysis['max_score']}\n";
    echo "Критические проблемы: " . count($analysis['critical_issues']) . "\n";
    echo "Предупреждения: " . count($analysis['warnings']) . "\n";
    echo "Рекомендации: " . count($analysis['recommendations']) . "\n\n";
    
    echo "Детали по категориям:\n";
    foreach ($analysis['checks'] as $category => $data) {
        $percentage = round(($data['score'] / $data['max_score']) * 100);
        echo "- {$category}: {$data['score']}/{$data['max_score']} ({$percentage}%)\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n\n";
}
?>
