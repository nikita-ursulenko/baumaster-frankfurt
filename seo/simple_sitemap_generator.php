<?php
/**
 * Простой генератор XML Sitemap
 * Baumaster SEO Tools
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';

// Функция генерации slug
function generate_slug($text) {
    // Транслитерация русских символов
    $transliteration = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo',
        'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm',
        'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u',
        'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
        'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo',
        'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
        'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U',
        'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
        'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
    ];
    
    $text = strtr($text, $transliteration);
    
    // Преобразуем в нижний регистр
    $text = mb_strtolower($text, 'UTF-8');
    
    // Заменяем пробелы и специальные символы на дефисы
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    
    // Убираем дефисы в начале и конце
    $text = trim($text, '-');
    
    return $text;
}

function generate_simple_sitemap() {
    // Настройки sitemap - автоматически получаем URL сайта
    $base_url = get_site_url();
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

    return $xml_content;
}

// Если файл вызывается напрямую, генерируем и сохраняем sitemap
if (basename($_SERVER['PHP_SELF']) === 'simple_sitemap_generator.php') {
    $xml_content = generate_simple_sitemap();
    
    // Путь к файлу sitemap
    $sitemap_path = __DIR__ . '/../sitemap.xml';
    
    // Сохраняем в файл
    if (file_put_contents($sitemap_path, $xml_content)) {
        echo "Sitemap успешно создан: " . $sitemap_path . "\n";
        echo "Размер файла: " . filesize($sitemap_path) . " байт\n";
    } else {
        echo "Ошибка при создании sitemap\n";
    }
}
?>
