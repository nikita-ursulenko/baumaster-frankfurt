<?php
/**
 * Генератор XML Sitemap
 * Baumaster SEO Tools
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';

// Настройки sitemap - автоматически получаем URL из конфигурации
$base_url = get_setting('site_url', SITE_URL);
$lastmod = date('Y-m-d');

// Начало XML
$xml_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Статические страницы
$static_pages = [
    ['url' => '', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['url' => '/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/portfolio', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['url' => '/reviews', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['url' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
    // Добавляем языковые версии
    ['url' => '/de', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/de/services', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/de/portfolio', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['url' => '/de/about', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['url' => '/de/reviews', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => '/de/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['url' => '/de/contact', 'priority' => '0.7', 'changefreq' => 'monthly']
];

foreach ($static_pages as $page) {
    $xml_content .= '  <url>' . "\n";
    $xml_content .= '    <loc>' . htmlspecialchars($base_url . $page['url']) . '</loc>' . "\n";
    $xml_content .= '    <lastmod>' . $lastmod . '</lastmod>' . "\n";
    $xml_content .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
    $xml_content .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
    $xml_content .= '  </url>' . "\n";
}

// Динамические страницы
$db = get_database();

// Услуги
$services = $db->select('services', ['status' => 'active'], ['order' => 'priority DESC']);
foreach ($services as $service) {
    $slug = generate_slug($service['title']);
    $xml_content .= '  <url>' . "\n";
    $xml_content .= '    <loc>' . htmlspecialchars($base_url . '/service/' . $slug) . '</loc>' . "\n";
    $xml_content .= '    <lastmod>' . date('Y-m-d', strtotime($service['updated_at'] ?? $service['created_at'])) . '</lastmod>' . "\n";
    $xml_content .= '    <changefreq>weekly</changefreq>' . "\n";
    $xml_content .= '    <priority>0.8</priority>' . "\n";
    $xml_content .= '  </url>' . "\n";
}

// Проекты портфолио
$portfolio = $db->select('portfolio', ['status' => 'active'], ['order' => 'featured DESC, created_at DESC']);
foreach ($portfolio as $project) {
    $slug = generate_slug($project['title']);
    $xml_content .= '  <url>' . "\n";
    $xml_content .= '    <loc>' . htmlspecialchars($base_url . '/portfolio/' . $slug) . '</loc>' . "\n";
    $xml_content .= '    <lastmod>' . date('Y-m-d', strtotime($project['updated_at'] ?? $project['created_at'])) . '</lastmod>' . "\n";
    $xml_content .= '    <changefreq>monthly</changefreq>' . "\n";
    $xml_content .= '    <priority>0.7</priority>' . "\n";
    $xml_content .= '  </url>' . "\n";
}

// Статьи блога
$blog_posts = $db->select('blog_posts', ['status' => 'published'], ['order' => 'published_at DESC']);
foreach ($blog_posts as $post) {
    $slug = generate_slug($post['title']);
    $xml_content .= '  <url>' . "\n";
    $xml_content .= '    <loc>' . htmlspecialchars($base_url . '/blog_post.php?slug=' . $slug) . '</loc>' . "\n";
    $xml_content .= '    <lastmod>' . date('Y-m-d', strtotime($post['updated_at'] ?? $post['published_at'])) . '</lastmod>' . "\n";
    $xml_content .= '    <changefreq>monthly</changefreq>' . "\n";
    $xml_content .= '    <priority>0.8</priority>' . "\n";
    $xml_content .= '  </url>' . "\n";
}

// Закрываем XML
$xml_content .= '</urlset>' . "\n";

// Если файл вызывается напрямую, выводим XML
if (basename($_SERVER['PHP_SELF']) === 'generate_sitemap.php') {
    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: public, max-age=3600');
    echo $xml_content;
    exit;
}

// Возвращаем содержимое для сохранения в файл
return $xml_content;
?>
