<?php
/**
 * Экспорт портфолио в CSV
 * Baumaster Admin Panel - Portfolio CSV Export
 */

require_once __DIR__ . '/../config.php';
require_once ADMIN_PATH . 'auth.php';

// Проверка авторизации и прав доступа
require_auth();

// Получение данных
$db = get_database();
$portfolio = $db->select('portfolio', [], ['order' => 'sort_order DESC, featured DESC, created_at DESC']);

// Настройка заголовков для скачивания файла
$filename = 'portfolio_export_' . date('Y-m-d_H-i-s') . '.csv';
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
    'Категория',
    'Клиент',
    'Местоположение',
    'Площадь',
    'Продолжительность',
    'Бюджет',
    'Дата завершения',
    'Статус',
    'Рекомендуемый',
    'Приоритет',
    'Теги',
    'Meta Title',
    'Meta Description',
    'Дата создания',
    'Дата обновления'
];

fputcsv($output, $headers, ';');

// Экспорт данных
foreach ($portfolio as $project) {
    $tags = json_decode($project['tags'], true);
    $tags_string = is_array($tags) ? implode(', ', $tags) : '';
    
    $row = [
        $project['id'],
        $project['title'],
        substr($project['description'], 0, 200) . (strlen($project['description']) > 200 ? '...' : ''),
        translate_portfolio_category($project['category']),
        $project['client_name'] ?? '',
        $project['location'] ?? '',
        $project['area'] ?? '',
        $project['duration'] ?? '',
        $project['budget'] > 0 ? '€' . number_format($project['budget'], 0, ',', ' ') : '',
        $project['completion_date'] ? format_date($project['completion_date']) : '',
        $project['status'] === 'active' ? 'Активный' : 'Скрытый',
        $project['featured'] ? 'Да' : 'Нет',
        $project['sort_order'],
        $tags_string,
        $project['meta_title'] ?? '',
        $project['meta_description'] ?? '',
        format_date($project['created_at']),
        format_date($project['updated_at'])
    ];
    
    fputcsv($output, $row, ';');
}

// Закрытие потока
fclose($output);

// Логирование экспорта
$current_user = get_current_admin_user();
write_log("Portfolio exported to CSV by user: " . $current_user['username'], 'INFO');
log_user_activity('portfolio_export', 'portfolio', 0);

/**
 * Перевод категории портфолио
 */
function translate_portfolio_category($category) {
    switch ($category) {
        case 'apartment': return 'Квартиры';
        case 'house': return 'Дома';
        case 'office': return 'Офисы';
        case 'commercial': return 'Коммерческие';
        case 'bathroom': return 'Ванные комнаты';
        case 'kitchen': return 'Кухни';
        default: return ucfirst($category);
    }
}

exit;
?>

