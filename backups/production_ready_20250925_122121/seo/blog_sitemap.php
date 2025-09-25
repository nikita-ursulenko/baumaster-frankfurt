<?php
/**
 * Генератор sitemap.xml для статей блога
 * Baumaster SEO - Blog Sitemap Generator
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';

try {
    $db = get_database();

    // Получаем все опубликованные статьи блога
    $posts = $db->select('blog_posts', [
        'status' => 'published'
    ], [
        'order_by' => 'published_at DESC'
    ]);

    // Устанавливаем заголовки для XML
    header('Content-Type: application/xml; charset=UTF-8');

    // Начало XML sitemap
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    echo '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
    echo '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
    echo '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

    // Добавляем главную страницу блога
    echo "\n";
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars(SITE_URL . '/blog.php') . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>weekly</changefreq>' . "\n";
    echo '    <priority>0.8</priority>' . "\n";
    echo '  </url>' . "\n";

    // Добавляем каждую статью блога
    foreach ($posts as $post) {
        // Пропускаем статьи без slug
        if (empty($post['slug'])) {
            continue;
        }

        echo "\n";
        echo '  <url>' . "\n";
        echo '    <loc>' . htmlspecialchars(SITE_URL . '/blog_post.php?slug=' . urlencode($post['slug'])) . '</loc>' . "\n";

        // Дата последнего изменения
        $lastmod = $post['updated_at'] ?: $post['published_at'];
        if ($lastmod) {
            echo '    <lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>' . "\n";
        }

        // Частота изменений (статьи блога обновляются редко)
        echo '    <changefreq>monthly</changefreq>' . "\n";

        // Приоритет (зависит от популярности и даты публикации)
        $priority = 0.6; // базовый приоритет

        // Более свежие статьи имеют больший приоритет
        if ($post['published_at']) {
            $days_since_publish = (time() - strtotime($post['published_at'])) / (60 * 60 * 24);
            if ($days_since_publish < 30) {
                $priority = 0.8; // очень свежие статьи
            } elseif ($days_since_publish < 90) {
                $priority = 0.7; // относительно свежие
            }
        }

        // Рекомендуемые статьи имеют больший приоритет
        if ($post['featured']) {
            $priority += 0.1;
        }

        // Популярные статьи (по просмотрам) имеют больший приоритет
        if ($post['views'] > 100) {
            $priority += 0.1;
        }

        // Ограничиваем приоритет максимумом 1.0
        $priority = min($priority, 1.0);

        echo '    <priority>' . number_format($priority, 1, '.', '') . '</priority>' . "\n";
        echo '  </url>' . "\n";
    }

    // Закрываем XML
    echo "\n" . '</urlset>' . "\n";

} catch (Exception $e) {
    // В случае ошибки возвращаем базовый sitemap с главной страницей блога
    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars(SITE_URL . '/blog.php') . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
    echo '    <changefreq>weekly</changefreq>' . "\n";
    echo '    <priority>0.8</priority>' . "\n";
    echo '  </url>' . "\n";
    echo '</urlset>' . "\n";

    // Логируем ошибку
    error_log("Ошибка генерации blog sitemap: " . $e->getMessage());
}
?>
