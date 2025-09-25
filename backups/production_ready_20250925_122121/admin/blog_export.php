<?php
/**
 * Экспорт блога в CSV
 * Baumaster Admin Panel - Blog CSV Export
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once ADMIN_PATH . 'auth.php';

// Проверка авторизации и прав доступа
require_auth();

// Получение данных
$db = get_database();
$posts = $db->select('blog_posts', [], ['order' => 'sort_order DESC, created_at DESC']);

// Настройка заголовков для скачивания файла
$filename = 'blog_export_' . date('Y-m-d_H-i-s') . '.csv';
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
    'Заголовок',
    'Slug',
    'Краткое описание',
    'Категория',
    'Тип',
    'Статус',
    'Рекомендуемая',
    'Просмотры',
    'Теги',
    'Meta Title',
    'Meta Description',
    'Ключевые слова',
    'Дата публикации',
    'Дата создания',
    'Дата обновления'
];

fputcsv($output, $headers, ';');

// Экспорт данных
foreach ($posts as $post) {
    $tags = json_decode($post['tags'], true);
    $tags_string = is_array($tags) ? implode(', ', $tags) : '';
    
    $row = [
        $post['id'],
        $post['title'],
        $post['slug'],
        substr(strip_tags($post['excerpt']), 0, 200) . (strlen(strip_tags($post['excerpt'])) > 200 ? '...' : ''),
        translate_blog_category($post['category']),
        translate_blog_type($post['post_type']),
        translate_blog_status($post['status']),
        $post['featured'] ? 'Да' : 'Нет',
        $post['views'],
        $tags_string,
        $post['meta_title'] ?? '',
        $post['meta_description'] ?? '',
        $post['keywords'] ?? '',
        $post['published_at'] ? format_date($post['published_at']) : '',
        format_date($post['created_at']),
        format_date($post['updated_at'])
    ];
    
    fputcsv($output, $row, ';');
}

// Закрытие потока
fclose($output);

// Логирование экспорта
$current_user = get_current_admin_user();
write_log("Blog posts exported to CSV by user: " . $current_user['username'], 'INFO');
log_user_activity('blog_export', 'blog_posts', 0);

/**
 * Перевод категории блога
 */
function translate_blog_category($category) {
    switch ($category) {
        case 'tips': return 'Советы';
        case 'faq': return 'FAQ';
        case 'news': return 'Новости';
        case 'guides': return 'Руководства';
        default: return ucfirst($category);
    }
}

/**
 * Перевод типа статьи
 */
function translate_blog_type($type) {
    switch ($type) {
        case 'article': return 'Статья';
        case 'faq': return 'FAQ';
        case 'news': return 'Новость';
        case 'tips': return 'Совет';
        default: return ucfirst($type);
    }
}

/**
 * Перевод статуса статьи
 */
function translate_blog_status($status) {
    switch ($status) {
        case 'draft': return 'Черновик';
        case 'published': return 'Опубликовано';
        default: return ucfirst($status);
    }
}

exit;
?>

