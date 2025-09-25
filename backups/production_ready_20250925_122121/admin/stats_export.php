<?php
/**
 * Экспорт статистики в CSV
 * Baumaster Admin Panel - Statistics Export
 */

require_once __DIR__ . '/../config.php';
require_once ADMIN_PATH . 'auth.php';

// Проверка авторизации и прав доступа
$current_user = get_current_admin_user();
if (!has_permission('export.data', $current_user)) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Получение параметров
$period = $_GET['period'] ?? '30';
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime("-{$period} days"));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Получение данных
$db = get_database();
$stats = get_statistics_data($date_from, $date_to);

// Настройка заголовков для скачивания файла
$filename = 'statistics_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Открытие потока вывода
$output = fopen('php://output', 'w');

// BOM для корректного отображения UTF-8 в Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Заголовки отчета
fputcsv($output, ['ОТЧЕТ ПО СТАТИСТИКЕ САЙТА'], ';');
fputcsv($output, ['Период: ' . $date_from . ' - ' . $date_to], ';');
fputcsv($output, ['Дата создания: ' . date('Y-m-d H:i:s')], ';');
fputcsv($output, [], ';'); // Пустая строка

// Общая статистика
fputcsv($output, ['ОБЩАЯ СТАТИСТИКА'], ';');
fputcsv($output, ['Показатель', 'Значение', 'Изменение'], ';');
fputcsv($output, ['Всего услуг', $stats['services']['total'], $stats['services']['change']], ';');
fputcsv($output, ['Активных услуг', $stats['services']['active'], ''], ';');
fputcsv($output, ['Рекомендуемых услуг', $stats['services']['featured'], ''], ';');
fputcsv($output, ['Средняя цена услуг', format_price($stats['services']['avg_price']), ''], ';');
fputcsv($output, [], ';'); // Пустая строка

fputcsv($output, ['Проектов в портфолио', $stats['portfolio']['total'], $stats['portfolio']['change']], ';');
fputcsv($output, ['Завершенных проектов', $stats['portfolio']['completed'], ''], ';');
fputcsv($output, ['Рекомендуемых проектов', $stats['portfolio']['featured'], ''], ';');
fputcsv($output, ['Средний бюджет проекта', format_price($stats['portfolio']['avg_budget']), ''], ';');
fputcsv($output, [], ';'); // Пустая строка

fputcsv($output, ['Отзывов клиентов', $stats['reviews']['total'], $stats['reviews']['change']], ';');
fputcsv($output, ['Проверенных отзывов', $stats['reviews']['verified'], ''], ';');
fputcsv($output, ['На модерации', $stats['reviews']['pending'], ''], ';');
fputcsv($output, ['Средний рейтинг', number_format($stats['reviews']['avg_rating'], 1) . '/5', ''], ';');
fputcsv($output, [], ';'); // Пустая строка

fputcsv($output, ['Статей в блоге', $stats['blog']['total'], $stats['blog']['change']], ';');
fputcsv($output, ['Опубликованных статей', $stats['blog']['published'], ''], ';');
fputcsv($output, ['Рекомендуемых статей', $stats['blog']['featured'], ''], ';');
fputcsv($output, [], ';'); // Пустая строка

// Детальная статистика по услугам
fputcsv($output, ['ДЕТАЛЬНАЯ СТАТИСТИКА ПО УСЛУГАМ'], ';');
$services = $db->select('services', [], ['order' => 'created_at DESC']);
fputcsv($output, ['ID', 'Название', 'Цена', 'Тип цены', 'Статус', 'Рекомендуемая', 'Дата создания'], ';');
foreach ($services as $service) {
    fputcsv($output, [
        $service['id'],
        $service['title'],
        $service['price'],
        $service['price_type'],
        $service['status'],
        $service['featured'] ? 'Да' : 'Нет',
        $service['created_at']
    ], ';');
}
fputcsv($output, [], ';'); // Пустая строка

// Детальная статистика по портфолио
fputcsv($output, ['ДЕТАЛЬНАЯ СТАТИСТИКА ПО ПОРТФОЛИО'], ';');
$portfolio = $db->select('portfolio', [], ['order' => 'created_at DESC']);
fputcsv($output, ['ID', 'Название', 'Категория', 'Бюджет', 'Статус', 'Рекомендуемый', 'Дата создания'], ';');
foreach ($portfolio as $project) {
    fputcsv($output, [
        $project['id'],
        $project['title'],
        $project['category'],
        $project['budget'],
        $project['status'],
        $project['featured'] ? 'Да' : 'Нет',
        $project['created_at']
    ], ';');
}
fputcsv($output, [], ';'); // Пустая строка

// Детальная статистика по отзывам
fputcsv($output, ['ДЕТАЛЬНАЯ СТАТИСТИКА ПО ОТЗЫВАМ'], ';');
$reviews = $db->select('reviews', [], ['order' => 'created_at DESC']);
fputcsv($output, ['ID', 'Клиент', 'Рейтинг', 'Статус', 'Проверен', 'Дата отзыва'], ';');
foreach ($reviews as $review) {
    fputcsv($output, [
        $review['id'],
        $review['client_name'],
        $review['rating'],
        $review['status'],
        $review['verified'] ? 'Да' : 'Нет',
        $review['review_date']
    ], ';');
}
fputcsv($output, [], ';'); // Пустая строка

// Детальная статистика по блогу
fputcsv($output, ['ДЕТАЛЬНАЯ СТАТИСТИКА ПО БЛОГУ'], ';');
$blog_posts = $db->select('blog_posts', [], ['order' => 'created_at DESC']);
fputcsv($output, ['ID', 'Заголовок', 'Категория', 'Тип', 'Статус', 'Просмотры', 'Дата создания'], ';');
foreach ($blog_posts as $post) {
    fputcsv($output, [
        $post['id'],
        $post['title'],
        $post['category'],
        $post['post_type'],
        $post['status'],
        $post['views'],
        $post['created_at']
    ], ';');
}

// Закрытие потока
fclose($output);

// Логирование экспорта
write_log("Statistics exported to CSV by user: " . $current_user['username'], 'INFO');
log_user_activity('stats_export', 'statistics', 0);

exit;
?>

