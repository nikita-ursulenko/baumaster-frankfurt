<?php
/**
 * Экспорт услуг в CSV
 * Baumaster Admin Panel - Services CSV Export
 */

require_once __DIR__ . '/../config.php';
require_once ADMIN_PATH . 'auth.php';

// Проверка авторизации и прав доступа
require_auth();

// Получение данных
$db = get_database();
$services = $db->select('services', [], ['order' => 'priority DESC, created_at DESC']);

// Настройка заголовков для скачивания файла
$filename = 'services_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Открытие потока вывода
$output = fopen('php://output', 'w');

// BOM для корректного отображения UTF-8 в Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Заголовки столбцов
$headers = [
    'ID',
    'Название',
    'Описание',
    'Цена',
    'Тип цены',
    'Категория',
    'Статус',
    'Приоритет',
    'Meta Title',
    'Meta Description',
    'Ключевые слова',
    'Дата создания',
    'Дата обновления'
];

fputcsv($output, $headers, ';');

// Экспорт данных
foreach ($services as $service) {
    $row = [
        $service['id'],
        $service['title'],
        $service['description'],
        $service['price'] > 0 ? $service['price'] . ' €' : 'По договорённости',
        translate_price_type($service['price_type']),
        translate_category($service['category']),
        $service['status'] === 'active' ? 'Активна' : 'Неактивна',
        $service['priority'],
        $service['meta_title'],
        $service['meta_description'],
        $service['keywords'],
        format_date($service['created_at']),
        format_date($service['updated_at'])
    ];
    
    fputcsv($output, $row, ';');
}

// Закрытие потока
fclose($output);

// Логирование экспорта
$current_user = get_current_admin_user();
write_log("Services exported to CSV by user: " . $current_user['username'], 'INFO');
log_user_activity('services_export', 'services', 0);

/**
 * Перевод типа цены
 */
function translate_price_type($type) {
    switch ($type) {
        case 'fixed': return 'Фиксированная';
        case 'per_m2': return 'За м²';
        case 'per_hour': return 'За час';
        default: return $type;
    }
}

/**
 * Перевод категории
 */
function translate_category($category) {
    switch ($category) {
        case 'painting': return 'Малярные работы';
        case 'flooring': return 'Укладка полов';
        case 'bathroom': return 'Ремонт ванных';
        case 'drywall': return 'Гипсокартон';
        case 'tiling': return 'Плитка';
        case 'renovation': return 'Комплексный ремонт';
        default: return ucfirst($category);
    }
}

exit;
?>

