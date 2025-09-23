<?php
/**
 * Скрипт для очистки портфолио
 * Удаляет все работы из базы данных
 */

// Подключаем конфигурацию
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

try {
    $db = get_database();
    
    // Получаем все работы портфолио
    $portfolio = $db->select('portfolio');
    echo "Найдено работ портфолио: " . count($portfolio) . "\n";
    
    if (count($portfolio) > 0) {
        // Удаляем все работы
        $result = $db->delete('portfolio', []);
        echo "Удалено работ: " . $result . "\n";
        
        // Удаляем изображения из папки uploads/portfolio
        $upload_dir = ASSETS_PATH . '/uploads/portfolio/';
        if (is_dir($upload_dir)) {
            $files = glob($upload_dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    echo "Удален файл: " . basename($file) . "\n";
                }
            }
        }
        
        echo "Портфолио полностью очищено!\n";
    } else {
        echo "Портфолио уже пустое.\n";
    }
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
?>
