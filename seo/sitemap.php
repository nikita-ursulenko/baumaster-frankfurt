<?php
/**
 * XML Sitemap Generator
 * Baumaster SEO Tools
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';

// Настройки sitemap - автоматически получаем URL из конфигурации
$base_url = get_setting('site_url', SITE_URL);
$lastmod = date('Y-m-d');

// Проверяем, вызывается ли файл напрямую (для браузера) или через генератор
$is_direct_call = !isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !isset($_GET['generate']);

if ($is_direct_call) {
    // Заголовки для XML (только при прямом вызове)
    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: public, max-age=3600');
}

// Начало XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Статические страницы
$static_pages = [
    ['url' => '', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['url' => '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/portfolio', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['url' => '/reviews', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['url' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly']
];

foreach ($static_pages as $page) {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($base_url . $page['url']) . '</loc>' . "\n";
    echo '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
    echo '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
    echo '    <priority>' . $page['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

// Динамические страницы
$db = get_database();

// Услуги
$services = $db->select('services', ['status' => 'active'], ['order' => 'priority DESC']);
foreach ($services as $service) {
    $slug = generate_slug($service['title']);
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($base_url . '/service/' . $slug) . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d', strtotime($service['updated_at'])) . '</lastmod>' . "\n";
    echo '    <changefreq>weekly</changefreq>' . "\n";
    echo '    <priority>0.8</priority>' . "\n";
    echo '  </url>' . "\n";
}

// Портфолио
$portfolio = $db->select('portfolio', ['status' => 'completed'], ['order' => 'created_at DESC']);
foreach ($portfolio as $project) {
    $slug = generate_slug($project['title']);
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($base_url . '/project/' . $slug) . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d', strtotime($project['updated_at'])) . '</lastmod>' . "\n";
    echo '    <changefreq>monthly</changefreq>' . "\n";
    echo '    <priority>0.7</priority>' . "\n";
    echo '  </url>' . "\n";
}

// Блог статьи
$blog_posts = $db->select('blog_posts', ['status' => 'published'], ['order' => 'published_at DESC']);
foreach ($blog_posts as $post) {
    // Пропускаем статьи без slug
    if (empty($post['slug'])) {
        continue;
    }

    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($base_url . '/blog_post.php?slug=' . urlencode($post['slug'])) . '</loc>' . "\n";

    // Дата последнего изменения
    $lastmod_date = $post['updated_at'] ?: $post['published_at'];
    echo '    <lastmod>' . date('Y-m-d', strtotime($lastmod_date)) . '</lastmod>' . "\n";

    // Частота изменений
    echo '    <changefreq>monthly</changefreq>' . "\n";

    // Приоритет зависит от популярности и свежести
    $priority = 0.6;
    if ($post['published_at']) {
        $days_since_publish = (time() - strtotime($post['published_at'])) / (60 * 60 * 24);
        if ($days_since_publish < 30) {
            $priority = 0.8; // очень свежие статьи
        } elseif ($days_since_publish < 90) {
            $priority = 0.7; // относительно свежие
        }
    }
    if ($post['featured']) {
        $priority += 0.1; // рекомендуемые статьи
    }
    if ($post['views'] > 100) {
        $priority += 0.1; // популярные статьи
    }
    $priority = min($priority, 1.0);

    echo '    <priority>' . number_format($priority, 1, '.', '') . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>' . "\n";

/**
 * Генерация SEO-friendly slug
 */
function generate_slug($text) {
    $text = transliterate($text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

/**
 * Транслитерация кириллицы
 */
function transliterate($text) {
    $cyr = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'];
    $lat = ['a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya'];
    
    return str_replace($cyr, $lat, $text);
}
?>

