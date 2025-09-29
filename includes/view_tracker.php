<?php
/**
 * Система отслеживания просмотров
 * Подключается к публичным страницам для подсчета просмотров
 */

// Подключаем только если не в админке
if (!defined('ADMIN_PATH') && !strpos($_SERVER['REQUEST_URI'], '/admin/')) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../functions/views_counter.php';
    
    // Определяем тип контента и ID из URL
    $current_url = $_SERVER['REQUEST_URI'];
    $id = null;
    $type = null;
    
    // Проверяем, это страница услуги
    if (preg_match('/services\.php\?id=(\d+)/', $current_url, $matches)) {
        $id = $matches[1];
        $type = 'services';
    }
    // Проверяем, это страница проекта
    elseif (preg_match('/portfolio\.php\?id=(\d+)/', $current_url, $matches)) {
        $id = $matches[1];
        $type = 'portfolio';
    }
    // Проверяем, это страница статьи блога
    elseif (preg_match('/blog_post\.php\?(?:slug=|id=)([^&]+)/', $current_url, $matches)) {
        $slug_or_id = $matches[1];
        
        // Если это ID
        if (is_numeric($slug_or_id)) {
            $id = $slug_or_id;
        } else {
            // Если это slug, получаем ID
            $db = get_database();
            $post = $db->query("SELECT id FROM blog_posts WHERE slug = ?", [$slug_or_id])->fetch();
            if ($post) {
                $id = $post['id'];
            }
        }
        $type = 'blog_posts';
    }
    
    // Увеличиваем счетчик просмотров, если определили тип и ID
    if ($id && $type) {
        increment_views($type, $id);
    }
}
?>
