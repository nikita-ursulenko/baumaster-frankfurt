<?php
/**
 * Экспорт отзывов в CSV
 * Baumaster Admin Panel - Reviews CSV Export
 */

require_once __DIR__ . '/../config.php';
require_once ADMIN_PATH . 'auth.php';

// Проверка авторизации и прав доступа
require_auth();

// Получение данных
$db = get_database();
$reviews = $db->select('reviews', [], ['order' => 'sort_order DESC, created_at DESC']);

// Настройка заголовков для скачивания файла
$filename = 'reviews_export_' . date('Y-m-d_H-i-s') . '.csv';
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
    'Имя клиента',
    'Email',
    'Телефон',
    'Текст отзыва',
    'Рейтинг',
    'Статус',
    'Проверен',
    'Рекомендуемый',
    'Дата отзыва',
    'Приоритет',
    'Заметки админа',
    'Дата создания'
];

fputcsv($output, $headers, ';');

// Экспорт данных
foreach ($reviews as $review) {
    $row = [
        $review['id'],
        $review['client_name'],
        $review['client_email'] ?? '',
        $review['client_phone'] ?? '',
        substr($review['review_text'], 0, 500) . (strlen($review['review_text']) > 500 ? '...' : ''),
        $review['rating'] . '/5',
        translate_review_status($review['status']),
        $review['verified'] ? 'Да' : 'Нет',
        $review['featured'] ? 'Да' : 'Нет',
        format_date($review['review_date']),
        $review['sort_order'],
        $review['admin_notes'] ?? '',
        format_date($review['created_at'])
    ];
    
    fputcsv($output, $row, ';');
}

// Закрытие потока
fclose($output);

// Логирование экспорта
$current_user = get_current_admin_user();
write_log("Reviews exported to CSV by user: " . $current_user['username'], 'INFO');
log_user_activity('reviews_export', 'reviews', 0);

/**
 * Перевод статуса отзыва
 */
function translate_review_status($status) {
    switch ($status) {
        case 'pending': return 'На модерации';
        case 'published': return 'Опубликован';
        case 'rejected': return 'Отклонен';
        default: return ucfirst($status);
    }
}

exit;
?>

